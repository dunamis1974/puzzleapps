# Restricting the installer after setup

The default Nginx configuration leaves `/install/` available for the initial
setup. Nginx does not detect when installation is complete.

After successful installation and before exposing the configured site, add this
restriction inside the `server` block in `docker/nginx/default.conf`:

```nginx
location ^~ /install/ { return 404; }
```

Apply the updated configuration to the Nginx service used by the selected
Compose profile. Rebuild and recreate the `nginx` service for the `publish`
profile, or `nginx-dev` for the `dev` profile; the Nginx image copies
`default.conf` during its build. Alternatively, reload the relevant service
after the updated configuration is present in its container.

This is an operator-applied post-setup control, not automatic detection or
enforcement. Writing these instructions does not apply the restriction or
verify live Nginx access; confirm the operator change and runtime behavior
separately.
