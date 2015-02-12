<?php

/**
 * SendMail
 * 
 * @package Puzzle CMS
 * @author .... 
 * @copyright Copyright (c) 2004
 * @version $Id: mail.module.php,v 1.2 2005/10/29 11:46:09 bobby Exp $
 * @access public 
 */
class SendMail {
    var $From;
    var $FromName;
    var $To;
    var $ToName;
    var $Subject;
    var $Text;
    var $Html;
    var $AttmFiles;
    var $Encoding = "UTF-8";
    var $Priority;

    /**
     * SendMail::doSendMail()
     * 
     * @return 
     **/
    function doSendMail() {
        $this->OB = "----=_OuterBoundary_000";
        $this->IB = "----=_InnerBoundery_001";
        if ($this->Html) $this->Html = $this->Html?$this->Html:nl2br($this->Text) or die("neither text nor html part present.");
        $this->Text = $this->Text?$this->Text:"Sorry, but you need an html mailer to read this mail.";
        $this->From or die("sender address missing");
        $this->To or die("recipient address missing");

        $this->_headers();
        $this->_message();
        $this->_attachments(); 
        // message ends
        $this->Msg .= "\n--" . $this->OB . "--\n"; 
        // compose $TO
        $TO = $this->ToName . " <" . $this->To . ">"; 
        // And now let's send e-mail message
        if ($_SERVER["SERVER_ADDR"] != "127.0.0.1") {
            if (@mail($TO, $this->Subject, $this->Msg, $this->headers)) return true;
            else return false;
        } else {
            echo "<pre><div align=\"left\">";
            echo "Headers:\n---------------------------------------------------\n" . htmlspecialchars($this->headers) . "\n";
            echo "To:\n---------------------------------------------------\n" . htmlspecialchars($TO) . "\n";
            echo "Subject:\n---------------------------------------------------\n" . htmlspecialchars($this->Subject) . "\n";
            echo "\nTEXT Message:\n---------------------------------------------------\n";
            echo $this->Text;
            echo "\n\nHTML Message:\n---------------------------------------------------\n";
            echo $this->Html . "\n";
            echo "</div></pre>";
            return true;
        } 
    } 

    /**
     * SendMail::_headers()
     * 
     * @return 
     **/
    function _headers () {
        $this->headers = "MIME-Version: 1.0\n";
        $this->headers .= "From: " . $this->FromName . " <" . $this->From . ">\n"; 
        // $this->headers .= "To: ". $this->ToName ." <".$this->To.">\n";
        $this->headers .= "Reply-To: " . $this->FromName . " <" . $this->From . ">\n";
        $this->headers .= "X-Priority: 3\n";
        $this->headers .= "X-MSMail-Priority: " . $this->Priority . "\n";
        $this->headers .= "X-Mailer: Newsletter Mailer\n";
        $this->headers .= "Content-Type: multipart/mixed;\n\tboundary=\"" . $this->OB . "\"\n";
    } 

    function _message () {
        // Messages start with text/html alternatives in OB
        $this->Msg = "This is a multi-part message in MIME format.\n";
        $this->Msg .= "\n--" . $this->OB . "\n";
        $this->Msg .= "Content-Type: multipart/alternative;\n\tboundary=\"" . $this->IB . "\"\n\n"; 
        // plaintext section
        $this->Msg .= "\n--" . $this->IB . "\n";
        $this->Msg .= "Content-Type: text/plain;\n\tcharset=\"" . $this->Encoding . "\"\n";
        $this->Msg .= "Content-Transfer-Encoding: quoted-printable\n\n"; 
        // plaintext goes here
        $this->Msg .= $this->Text . "\n\n";
        if ($this->Html) {
            // html section
            $this->Msg .= "\n--" . $this->IB . "\n";
            $this->Msg .= "Content-Type: text/html;\n\tcharset=\"" . $this->Encoding . "\n";
            $this->Msg .= "Content-Transfer-Encoding: base64\n\n"; 
            // html goes here
            $this->Msg .= chunk_split(base64_encode($this->Html)) . "\n\n";
        } 
        // end of IB
        $this->Msg .= "\n--" . $this->IB . "--\n";
    } 

    /**
     * SendMail::_attachments()
     * 
     * @return 
     **/
    function _attachments () {
        // attachments
        if ($this->AttmFiles) {
            foreach($this->AttmFiles as $AttmFile) {
                $patharray = explode ("/", $AttmFile);
                $FileName = $patharray[count($patharray)-1];
                $this->Msg .= "\n--" . $OB . "\n";
                $this->Msg .= "Content-Type: application/octetstream;\n\tname=\"" . $FileName . "\"\n";
                $this->Msg .= "Content-Transfer-Encoding: base64\n";
                $this->Msg .= "Content-Disposition: attachment;\n\tfilename=\"" . $FileName . "\"\n\n"; 
                // file goes here
                $fd = fopen ($AttmFile, "r");
                $FileContent = fread($fd, filesize($AttmFile));
                fclose ($fd);
                $FileContent = chunk_split(base64_encode($FileContent));
                $this->Msg .= $FileContent;
                $this->Msg .= "\n\n";
            } 
        } 
    } 
} 

?>