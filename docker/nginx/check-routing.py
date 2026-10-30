#!/usr/bin/env python3
"""Statically verify the Nginx routing contract against the CMS sources."""

from pathlib import Path
import re
import sys


ROOT = Path(__file__).resolve().parents[2]
NGINX_CONFIG = ROOT / "docker/nginx/default.conf"
APACHE_CONFIG = ROOT / ".htaccess"


def require(condition, message, failures):
    if not condition:
        failures.append(message)


def compose_service(text, name):
    match = re.search(
        rf"(?ms)^  {re.escape(name)}:\s*\n(.*?)(?=^  [A-Za-z0-9_-]+:\s*$|^volumes:\s*$)",
        text,
    )
    return match.group(1) if match else None


def main():
    failures = []
    nginx = NGINX_CONFIG.read_text(encoding="utf-8")
    apache = APACHE_CONFIG.read_text(encoding="utf-8")
    app_dockerfile = (ROOT / "Dockerfile").read_text(encoding="utf-8")
    nginx_dockerfile = (ROOT / "docker/nginx.Dockerfile").read_text(encoding="utf-8")
    compose = (ROOT / "docker-compose.yml").read_text(encoding="utf-8")

    require(
        re.search(r"(?m)^WORKDIR /var/www/html$", app_dockerfile) is not None,
        "Dockerfile must use /var/www/html as its working directory.",
        failures,
    )
    require(
        re.search(r"(?m)^WORKDIR /var/www/html$", nginx_dockerfile) is not None
        and re.search(r"(?m)^COPY \. /var/www/html$", nginx_dockerfile) is not None,
        "Nginx image must use and populate /var/www/html.",
        failures,
    )
    for service_name in ("app-dev", "nginx-dev"):
        service = compose_service(compose, service_name)
        require(
            service is not None
            and re.search(r"(?m)^\s+- \./:/var/www/html\s*$", service) is not None,
            f"Compose service {service_name} must bind-mount the repository at /var/www/html.",
            failures,
        )
    require(
        re.search(r"(?m)^\s*root /var/www/html;$", nginx) is not None,
        "Nginx server root must be /var/www/html.",
        failures,
    )

    php_match = re.search(r"(?s)location ~ \\\.php\$\s*\{(.*?)\}", nginx)
    php_location = php_match.group(1) if php_match else ""
    require(
        "fastcgi_pass app:9000;" in php_location,
        "PHP location must forward to the Compose app:9000 FastCGI service.",
        failures,
    )
    require(
        "fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;"
        in php_location,
        "PHP location must derive SCRIPT_FILENAME from the shared document root.",
        failures,
    )
    require(
        "include fastcgi_params;" in php_location,
        "PHP location must include standard FastCGI parameters.",
        failures,
    )
    php_exists_check = php_location.find("try_files $uri =404;")
    php_forward = php_location.find("fastcgi_pass app:9000;")
    require(
        php_exists_check >= 0 and php_forward > php_exists_check,
        "PHP location must reject missing scripts before FastCGI forwarding.",
        failures,
    )

    static_first = nginx.find("try_files $uri $uri/ @cms;")
    cms_location = nginx.find("location @cms")
    require(
        static_first >= 0 and cms_location > static_first,
        "Real files and directories must be tried before the named CMS fallback.",
        failures,
    )
    cms_match = re.search(r"(?s)location @cms\s*\{(.*?)\}", nginx)
    cms_rewrites = {
        pattern: replacement
        for pattern, replacement in re.findall(
            r"(?m)^\s*rewrite\s+(\S+)\s+(\S+)\s+last;",
            cms_match.group(1) if cms_match else "",
        )
    }
    apache_rules = (
        (
            "html",
            r"RewriteRule\s+\^\(\.\*\)\.html\s+index\.php\?tid=\$1\s+\[L\]",
            r"^/(.*)\.html$",
            "/index.php?tid=$1?",
        ),
        (
            "gallery",
            r"RewriteRule\s+\^gallery/\(\.\*\)\s+index\.php\?id=\$1\s+\[L\]",
            r"^/gallery/(.*)$",
            "/index.php?id=$1?",
        ),
        (
            "media",
            r"RewriteRule\s+\^media\(\.\*\)\s+index\.php\?downloadfile=tid&tid=\$1\s+\[L\]",
            r"^/media(.*)$",
            "/index.php?downloadfile=tid&tid=$1?",
        ),
    )
    for route, apache_pattern, nginx_pattern, replacement in apache_rules:
        require(
            re.search(apache_pattern, apache, re.MULTILINE) is not None,
            f".htaccess must contain the expected {route} rewrite source.",
            failures,
        )
        require(
            cms_rewrites.get(nginx_pattern) == replacement,
            f"Nginx {route} route must preserve the .htaccess destination and replace old query args.",
            failures,
        )
    require(
        "return 404;" in (cms_match.group(1) if cms_match else ""),
        "Unmatched CMS fallback requests must return 404.",
        failures,
    )
    dotfile_location = nginx.find("location ~ /\\.(?!well-known(?:/|$))")
    require(
        dotfile_location >= 0
        and "deny all;" in nginx[dotfile_location : nginx.find("}", dotfile_location)],
        "Nginx must deny dotfiles while exempting /.well-known/.",
        failures,
    )

    if failures:
        for failure in failures:
            print(f"FAIL: {failure}", file=sys.stderr)
        return 1

    print("PASS: Nginx routing and shared document-root contracts are satisfied.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
