<?php
if( isset($_GET["noscript"]) ) 
{
    $_SESSION["lk"] = "";
    showmessage("<div style=\"border: 3px solid red; text-align: center; font-size: 18px; font-weight: bold; padding: 10px; width: 600px; height: 65px; margin: 100px auto; font-family: 'Trebuchet MS', Helvetica, Arial, sans-serif; line-height: 1.7;\">Your Browser does not support JavaScript, or it is disabled.<br/>To run this application, you must enable JavaScript!!</div>");
}

aaaaov();
$viaPost = (strtoupper($_SERVER["REQUEST_METHOD"]) == "POST" ? 1 : 0);
$isAjax = (isset($_POST["isAjax"]) ? 1 : 0);
$stepCount = (isset($_POST["stepCount"]) ? intval($_POST["stepCount"]) : 1);
$submitForm = (isset($_POST["submitForm"]) && $_POST["submitForm"] == "yes" ? 1 : 0);
$doAction = (isset($_POST["doAction"]) ? trim($_POST["doAction"]) : "");
$ERROR = "";
$TSUE = array(  );
aaaaow();
if( $isAjax && $viaPost ) 
{
    if( $doAction == "requirementsCheck" ) 
    {
        clearstatcache();
        if( file_exists(DATAPATH . "cache/install.lock") )
        {
            aaaaox("This installation has been locked. Please read <a href=\"http://FMEdition.com/customers/faq/\" target=\"_blank\">this</a>.");
        }

        if( !file_exists(LIBRARYPATH . "config/database_config.php") ) 
        {
            aaaaox("Database configuration file does not exists. Please follow <a href=\"http://FMEdition.com/customers/manual/installation/\" target=\"_blank\">this</a> steps.");
        }

        $errors = array(  );
        if( !session_id() ) 
        {
            $errors["session"] = "PHP Session support does not exists. Please ask your host to enable this feature.";
        }

        $phpVersion = phpversion();
        if( version_compare($phpVersion, "5.6.32", "<") )
        {
            $errors["phpVersion"] = "PHP 5.6.32 or newer is required. " . $phpVersion . " does not meet this requirement. Please ask your host to upgrade PHP.";
        }

        if( @ini_get("safe_mode") == 1 || strtolower(@ini_get("safe_mode")) == "on" ) 
        {
            $errors["safe_mode"] = "PHP must not be running in safe_mode. Please ask your host to disable the PHP safe_mode setting.";
        }

        if( !ini_get("allow_url_fopen") || strtolower(ini_get("allow_url_fopen")) == "off" ) 
        {
            $errors["allow_url_fopen"] = "The PHP allow_url_fopen setting is disabled. Please ask your host to enable the PHP allow_url_fopen setting.";
        }

        if( !function_exists("curl_init") || function_exists("curl_init") && !($ch = curl_init()) ) 
        {
            $errors["curl"] = "The required PHP library CURL could not be found. Please ask your host to install this library.";
        }

        if( !function_exists("mysqli_connect") ) 
        {
            $errors["mysqlPhp"] = "The required PHP extension MySQLi could not be found. Please ask your host to install this extension.";
        }
        else
        {
            $TSUE["TSUE_Database"] = new TSUE_Database(false);
            if( $TSUE["TSUE_Database"]->error ) 
            {
                $errors["mysqlPhp"] = "Database Connection Failed! Please follow <a href=\"http://FMEdition.com/customers/manual/installation/\" target=\"_blank\">this</a> steps.";
            }

        }

        if( !function_exists("preg_replace") ) 
        {
            $errors["pcre"] = "The required PHP extension PCRE could not be found. Please ask your host to install this extension.";
        }

        if( function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc() ) 
        {
            $errors["get_magic_quotes_gpc"] = "Warning: Magic Quotes GPC is enabled.";
        }

        if( function_exists("get_magic_quotes_runtime") && get_magic_quotes_runtime() ) 
        {
            $errors["get_magic_quotes_runtime"] = "Warning: Magic Quotes Runtime is enabled.";
        }

        if( !function_exists("gd_info") ) 
        {
            $errors["gd"] = "The required PHP extension GD could not be found. Please ask your host to install this extension.";
        }
        else
        {
            if( !function_exists("imagecreatefromjpeg") ) 
            {
                $errors["gdJpeg"] = "The required PHP extension GD was found, but JPEG support is missing. Please ask your host to add support for JPEG images.";
            }
            else
            {
                if( !function_exists("imagecreatefromgif") ) 
                {
                    $errors["gdJpeg"] = "The required PHP extension GD was found, but GIF support is missing. Please ask your host to add support for GIF images.";
                }
                else
                {
                    if( !function_exists("imagecreatefrompng") ) 
                    {
                        $errors["gdJpeg"] = "The required PHP extension GD was found, but PNG support is missing. Please ask your host to add support for PNG images.";
                    }

                }

            }

        }

        if( !function_exists("json_encode") ) 
        {
            $errors["json"] = "The required PHP extension JSON could not be found. Please ask your host to install this extension.";
        }

        if( !function_exists("mb_check_encoding") ) 
        {
            $errors["mb_check_encoding"] = "The required PHP extension mb_check_encoding could not be found. Please ask your host to install this extension.";
        }

        if( !class_exists("DOMDocument") || !class_exists("SimpleXMLElement") ) 
        {
            $errors["xml"] = "The required PHP extensions for XML handling (DOM and SimpleXML) could not be found. Please ask your host to install this extension.";
        }

        if( $errors ) 
        {
            $errorList = "";
            foreach( $errors as $error ) 
            {
                $errorList .= "<li>" . $error . "</li>";
            }
            $Result = "\r\n\t\t\t<div class=\"failure\">\r\n\t\t\t\t<h2>\r\n\t\t\t\t\t<span style=\"float: right; font-weight: normal; font-size: 11px; color: #999; margin-top: -30px; margin-right: -5px;\">DB: " . MYSQL_DB . "</span>\r\n\t\t\t\t\tRequirements Not Met\r\n\t\t\t\t</h2>\r\n\t\t\t\t<p>The following TSUE requirements were not met. Please contact your host for help.</p>\r\n\t\t\t\t<ol>\r\n\t\t\t\t\t" . $errorList . "\r\n\t\t\t\t</ol>\r\n\t\t\t\t<p class=\"mysql\">\r\n\t\t\t\t\tCan't install yourself? Please purchase a professional installation at: <a href=\"http://FMEdition.com/customers/services/\">http://FMEdition.com/customers/services/</a>\r\n\t\t\t\t</p>\r\n\t\t\t</div>";
        }
        else
        {
            $LADivHeight = 250;
            $licenseAgreement = @file_get_contents("");
            if( !$licenseAgreement )
            {
                $LADivHeight = 50;
                $licenseAgreement = "Unable to fetch TSUE License Agreement. Please click <a href=\"http://FMEdition.com/licenseAgreement.html\" target=\"_blank\">here</a> to view HTML version.";
            }

            $Result = "\r\n\t\t\t<div class=\"success\" id=\"requirementsMet\">\r\n\t\t\t\t<h2>\r\n\t\t\t\t\t<span style=\"float: right; font-weight: normal; font-size: 11px; color: #999; margin-top: -30px; margin-right: -5px;\">DB: " . MYSQL_DB . "</span>\r\n\t\t\t\t\tRequirements Met\r\n\t\t\t\t</h2>\r\n\t\t\t\t<p>Your server meets all of TSUE requirements and you are now ready to begin installation.</p>\r\n\t\t\t\t<p class=\"mysql\">TSUE also requires MySQL 5.0 or newer. Please manually check that you meet this requirement.</p>\r\n\t\t\t</div>\r\n\t\t\t\r\n\t\t\t<div class=\"success\" id=\"licenseAgreement\">\r\n\t\t\t\t<div style=\"padding: 5px; margin: 5px; border: 1px solid #ccc; height: " . $LADivHeight . "px; overflow: auto; font-size: 11px;\">\r\n\t\t\t\t\t<h1>License Agreement</h1>\r\n\t\t\t\t\t" . $licenseAgreement . "\r\n\t\t\t\t</div>\r\n\t\t\t</div>";
        }

        showmessage($Result);
    }

    if( $stepCount ) 
    {
        switch( $stepCount ) 
        {
            case 1:
                $TSUESystemDirectories = aaaaoy();
                $searchDirectories = aaaaoz(DATAPATH);
                if( count($searchDirectories) < 7 ) 
                {
                    showerror("Required TSUE system directory does not exists in " . DATAPATH);
                }

                foreach( $TSUESystemDirectories as $TSUESystemDirectory ) 
                {
                    if( !checkdirectorypermissions(DATAPATH . $TSUESystemDirectory) ) 
                    {
                        showerror("The following TSUE System directory is not writable: " . DATAPATH . $TSUESystemDirectory);
                    }

                }
                showmessage("Directory Permissions has been checked & confirmed. Database connection is being checked...");
                break;
            case 2:
                $TSUE["TSUE_Database"] = new TSUE_Database();
                showmessage("Database Connection has been confirmed. MySQL Tables are being created, please be patient during this procedure...");
                break;
            case 3:
                showmessage("MySQL Tables has been created & confirmed. Basic settings are being configured...");
                break;
            case 4:
                if( $viaPost && $submitForm ) 
                {
                    $TSUE["TSUE_Database"] = new TSUE_Database();
                    $TSUE["TSUE_Settings"] = new TSUE_Settings();
                    if( !$TSUE["TSUE_Settings"]->settings["global_settings"] ) 
                    {
                        aaaaox("Unable to read Global Settings.");
                    }

                    $Settings = array(  );
                    $Settings["website_url"] = getpostvar("website_url");
                    $Settings["website_title"] = getpostvar("website_title");
                    $Settings["website_description"] = getpostvar("website_description");
                    $Settings["website_email"] = getpostvar("website_email");
                    $Settings["website_sendmail_from"] = getpostvar("website_sendmail_from");
                    foreach( $Settings as $settingName => $settingValue ) 
                    {
                        $settingValue = trim($settingValue);
                        if( !$settingValue ) 
                        {
                            aaaaox("Required fields can not be empty!");
                        }

                        if( !isset($TSUE["TSUE_Settings"]->settings["global_settings"][$settingName]) ) 
                        {
                            aaaaox("Invalid Setting: " . $settingName);
                        }

                        $TSUE["TSUE_Settings"]->settings["global_settings"][$settingName] = trim($settingValue);
                    }
                    $TSUE["TSUE_Settings"]->settings["global_settings"]["announce_url"] = $Settings["website_url"] . "/announce.php";
                    updatesettings("global_settings", $TSUE["TSUE_Settings"]->settings["global_settings"]);
                    showmessage("Basic settings has been configured. Administrator account is being created...");
                }

                showdialog($ERROR . "\r\n\t\t\t\t<form method=\"post\" id=\"dialogForm\" action=\"\">\r\n\t\t\t\t<input type=\"hidden\" name=\"isAjax\" value=\"1\" />\r\n\t\t\t\t<input type=\"hidden\" name=\"stepCount\" value=\"" . $stepCount . "\" />\r\n\t\t\t\t<input type=\"hidden\" name=\"submitForm\" value=\"yes\" />\r\n\t\t\t\t<div id=\"formDIV\">\r\n\t\t\t\t\t<h1>Setup Basic Settings</h1>\r\n\t\t\t\t\t<div class=\"text\">Website URL: " . showhint("The primary URL to your website. Do not include a trailing '/' or 'index.php'. The URL should look similar to this: " . TSUE_FRONT_URL) . "</div>\r\n\t\t\t\t\t<div class=\"input\"><input type=\"text\" name=\"website_url\" class=\"inputbox\" value=\"" . TSUE_FRONT_URL . "\" /></div>\r\n\r\n\t\t\t\t\t<div class=\"text\">Website Title: " . showhint("The title of your website. This will be displayed at the top of pages and used in emails.") . "</div>\r\n\t\t\t\t\t<div class=\"input\"><input type=\"text\" name=\"website_title\" class=\"inputbox\" value=\"\" /></div>\r\n\r\n\t\t\t\t\t<div class=\"text\">Website Meta Description: " . showhint("Enter a description for your website. This will be placed inside the meta description tag for the website home page, so avoid using HTML.") . "</div>\r\n\t\t\t\t\t<div class=\"input\"><input type=\"text\" name=\"website_description\" class=\"inputbox\" value=\"\" /></div>\r\n\r\n\t\t\t\t\t<div class=\"text\">Contact Email Address: " . showhint("Email address where website-related messages will be sent.") . "</div>\r\n\t\t\t\t\t<div class=\"input\"><input type=\"text\" name=\"website_email\" class=\"inputbox\" value=\"contact@" . _HTTP_HOST . "\" /></div>\r\n\r\n\t\t\t\t\t<div class=\"text\">Default Email Address: " . showhint("This is the default email address that emails will be sent from.") . "</div>\r\n\t\t\t\t\t<div class=\"input\"><input type=\"text\" name=\"website_sendmail_from\" class=\"inputbox\" value=\"noreply@" . _HTTP_HOST . "\" /></div>\r\n\t\t\t\t\t\r\n\t\t\t\t\t<div class=\"buttons\">\r\n\t\t\t\t\t\t<input type=\"submit\" value=\"save settings\" class=\"submit\" /> \r\n\t\t\t\t\t\t<input type=\"reset\" value=\"reset\" class=\"submit\" />\r\n\t\t\t\t\t</div>\r\n\t\t\t\t</div>\r\n\t\t\t\t</form>");
                break;
            case 5:
                if( $viaPost && $submitForm ) 
                {
                    $Account = array(  );
                    $Account["membername"] = getpostvar("membername");
                    $Account["password"] = getpostvar("password");
                    $Account["email"] = getpostvar("email");
                    $BuildQuery = array(  );
                    foreach( $Account as $Name => $Value ) 
                    {
                        $Value = trim($Value);
                        if( !$Value ) 
                        {
                            aaaaox("Required fields can not be empty!");
                        }

                        $BuildQuery[$Name] = ($Name == "password" ? md5($Value) : $Value);
                    }
                    $passkey = generatepasskey();
                    $BuildQuery["password_date"] = TIMENOW;
                    $BuildQuery["memberid"] = 1;
                    $BuildQuery["membergroupid"] = 7;
                    $BuildQuery["passkey"] = $passkey;
                    $BuildQuery["themeid"] = 1;
                    $BuildQuery["languageid"] = 1;
                    $BuildQuery["joindate"] = TIMENOW;
                    $BuildQuery["lastactivity"] = TIMENOW;
                    $BuildQuery["timezone"] = "1";
                    $TSUE["TSUE_Database"] = new TSUE_Database();
                    $TSUE["TSUE_Database"]->insert("tsue_members", $BuildQuery);
                    $memberid = $TSUE["TSUE_Database"]->insert_id();
                    if( !$memberid ) 
                    {
                        aaaaox("Database Error.");
                    }

                    $staffCPAvailableActions = aaaapb();
                    $TSUE["TSUE_Database"]->replace("tsue_staffcp_permissions", array( "memberid" => $memberid, "permissions" => serialize($staffCPAvailableActions) ));
                    $BuildQuery = array( "memberid" => $memberid );
                    $TSUE["TSUE_Database"]->replace("tsue_member_privacy", $BuildQuery);
                    $BuildQuery = array( "memberid" => $memberid, "signature" => "", "custom_title" => "", "uploaded" => 0, "downloaded" => 0, "total_posts" => 1, "invites_left" => 10, "points" => 1000, "torrent_pass" => substr($passkey, 0, 32) );
                    $TSUE["TSUE_Database"]->replace("tsue_member_profile", $BuildQuery);
                    $TSUE["TSUE_Database"]->update("tsue_cron", array( "nextrun" => TIMENOW ));
                    showmessage("Administrator account has been created. Setting up TSUE for first use...");
                }

                showdialog($ERROR . "\r\n\t\t\t\t<form method=\"post\" id=\"dialogForm\" action=\"\">\r\n\t\t\t\t<input type=\"hidden\" name=\"isAjax\" value=\"1\" />\r\n\t\t\t\t<input type=\"hidden\" name=\"stepCount\" value=\"" . $stepCount . "\" />\r\n\t\t\t\t<input type=\"hidden\" name=\"submitForm\" value=\"yes\" />\r\n\t\t\t\t<div id=\"formDIV\">\r\n\t\t\t\t\t<h1>Create Administrator Account</h1>\r\n\t\t\t\t\t<div class=\"text\">Membername:</div>\r\n\t\t\t\t\t<div class=\"input\"><input type=\"text\" name=\"membername\" class=\"inputbox\" value=\"\" /></div>\r\n\r\n\t\t\t\t\t<div class=\"text\">Password:</div>\r\n\t\t\t\t\t<div class=\"input\"><input type=\"password\" name=\"password\" class=\"inputbox\" value=\"\" /></div>\r\n\r\n\t\t\t\t\t<div class=\"text\">Email:</div>\r\n\t\t\t\t\t<div class=\"input\"><input type=\"text\" name=\"email\" class=\"inputbox\" value=\"\" /></div>\r\n\t\t\t\t\t\r\n\t\t\t\t\t<div class=\"buttons\">\r\n\t\t\t\t\t\t<input type=\"submit\" value=\"save settings\" class=\"submit\" /> \r\n\t\t\t\t\t\t<input type=\"reset\" value=\"reset\" class=\"submit\" />\r\n\t\t\t\t\t</div>\r\n\t\t\t\t</div>\r\n\t\t\t\t</form>");
                break;
            case 6:
                $TSUE["TSUE_Database"] = new TSUE_Database();
                $TSUE["TSUE_Settings"] = new TSUE_Settings();
                handleprune();
                handlerebuildcaches();
                showmessage("Finishing Installation...");
                break;
            case 7:
                file_put_contents(DATAPATH . "cache/install.lock", TIMENOW);
                showmessage("\r\n\t\t\t\t<div id=\"installationComplete\">\r\n\t\t\t\t\t<div class=\"success\"><h2>Installation Complete</h2></div>\r\n\t\t\t\t\t<b>Here's what you should do next:</b>\r\n\t\t\t\t\t<br />\r\n\t\t\t\t\t<ol>\r\n\t\t\t\t\t\t<li>\r\n\t\t\t\t\t\t\tDelete the Install Folder<br />\r\n\t\t\t\t\t\t\tYou should now delete the install directory from your web server.<br />\r\n\t\t\t\t\t\t\t<span class=\"mysql\">For security reasons, this script has been locked.</span>\r\n\t\t\t\t\t\t</li>\r\n\r\n\t\t\t\t\t\t<li>\r\n\t\t\t\t\t\t\tConfigure TSUE<br />\r\n\t\t\t\t\t\t\tNow it's time to configure your TSUE installation.<br />\r\n\t\t\t\t\t\t\tClick <a href=\"" . TSUE_FRONT_URL . "/admincp/\">here</a> to go to the admin area now.\r\n\t\t\t\t\t\t</li>\r\n\t\t\t\t\t</ol>\r\n\r\n\t\t\t\t\t<div class=\"thankYou\">\r\n\t\t\t\t\t\tThank you for choosing TSUE!<br />\r\n\t\t\t\t\t\twww.FMEdition.com\r\n\t\t\t\t\t</div>\r\n\t\t\t\t</div>\r\n\t\t\t\t");
                break;
            default:
                exit();
        }
    }

}

printoutput("\r\n\t<div id=\"welcomeScreen\">\r\n\t\t<img src=\"./images/ajax-loader.gif\" id=\"loader\" alt=\"\" title=\"\" border=\"0\" /> Please wait... Checking TSUE System Requirements...\r\n\t</div>\r\n\t<div id=\"beginInstall\">\r\n\t\t<div id=\"progressbar\"><div id=\"progress\"></div></div>\r\n\t\t<div id=\"buttons\">\r\n\t\t\t<input type=\"button\" name=\"start\" id=\"start\" class=\"submit\" value=\"If you Read & Agree TSUE License Agreement above, please click here to Begin Installation.\" />\r\n\t\t\t<input type=\"button\" name=\"reload\" id=\"reload\" class=\"submit\" value=\"Reload!\" />\r\n\t\t</div>\r\n\t\t<div id=\"result\"></div>\r\n\t</div>\r\n", true);

class TSUE_Settings
{
    public $settings = array(  );

    public function TSUE_Settings()
    {
        global $TSUE;
        $fetchSettings = $TSUE["TSUE_Database"]->query("SELECT settingname, settingvalues FROM tsue_settings ORDER BY settingname");
        if( $TSUE["TSUE_Database"]->num_rows($fetchSettings) ) 
        {
            while( $Setting = $TSUE["TSUE_Database"]->fetch_assoc($fetchSettings) ) 
            {
                $this->settings[$Setting["settingname"]] = unserialize($Setting["settingvalues"]);
            }
        }

    }

}


class TSUE_Database
{
    public $querycount = 0;
    public $query = NULL;
    public $query_cache = array(  );
    public $shutdown_queries = array(  );
    public $error = NULL;
    public $connection = NULL;
    public $locked = false;

    public function TSUE_Database($halt = true)
    {
        if( !is_file(LIBRARYPATH . "config/database_config.php") ) 
        {
            exit( "<h1>Fatal Error: The database configuration file does not exists.</h1>" );
        }

        require(LIBRARYPATH . "config/database_config.php");
        if( !($this->connection = @mysqli_init()) ) 
        {
            $this->error = "mysqli_init failed!";
            if( $halt ) 
            {
                $this->halt();
            }

        }

        if( !defined("MYSQL_HOST") || !defined("MYSQL_USER") || !defined("MYSQL_PASS") || !defined("MYSQL_DB") || !defined("MYSQL_PORT") || !defined("MYSQL_SOCKET") ) 
        {
            exit( "<h1>Fatal Error: Invalid variables in the database configuration file.</h1>" );
        }

        if( !mysqli_real_connect($this->connection, MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB, MYSQL_PORT, MYSQL_SOCKET) ) 
        {
            $this->error = "MySQLi connection error!";
            $this->halt();
        }

        if( !@mysqli_real_connect($this->connection, MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB, MYSQL_PORT, MYSQL_SOCKET) ) 
        {
            $this->error = "MySQLi connection error!";
            if( $halt ) 
            {
                $this->halt();
            }

        }

        if( MYSQL_CHARSET != "" ) 
        {
            if( function_exists("mysqli_set_charset") ) 
            {
                mysqli_set_charset($this->connection, MYSQL_CHARSET);
                return NULL;
            }

            $this->execute_query("SET NAMES " . MYSQL_CHARSET);
        }

    }

    public function execute_query($query, $buffered = true)
    {
        $this->querycount++;
        $this->query = $query;
        $this->query_cache[] = $query;
        if( $queryresult = mysqli_query($this->connection, $this->query, ($buffered ? MYSQLI_STORE_RESULT : MYSQLI_USE_RESULT)) ) 
        {
            return $queryresult;
        }

        $this->error = mysqli_error($this->connection);
        $this->halt();
    }

    public function query($query, $buffered = true)
    {
        return $this->execute_query($query, $buffered);
    }

    public function replace($table, $buildQuery, $isShutDownQuery = false)
    {
        $Query = array(  );
        foreach( $buildQuery as $field => $value ) 
        {
            $Query[] = "`" . $field . "` = " . $this->escape($value);
        }
        $SET = implode(",", $Query);
        if( $isShutDownQuery ) 
        {
            return $this->shutdown_query("REPLACE INTO `" . $table . "` SET " . $SET);
        }

        return $this->query("REPLACE INTO `" . $table . "` SET " . $SET);
    }

    public function insert($table, $buildQuery, $isShutDownQuery = false, $EXTRA = "", $IGNORE = "")
    {
        $Query = array(  );
        foreach( $buildQuery as $field => $value ) 
        {
            $Query[] = "`" . $field . "` = " . $this->escape($value);
        }
        $SET = implode(",", $Query);
        if( $isShutDownQuery ) 
        {
            return $this->shutdown_query("INSERT" . (($IGNORE ? " IGNORE" : "")) . " INTO `" . $table . "` SET " . $SET . $EXTRA);
        }

        return $this->query("INSERT" . (($IGNORE ? " IGNORE" : "")) . " INTO `" . $table . "` SET " . $SET . $EXTRA);
    }

    public function update($table, $buildQuery, $where = "", $isShutDownQuery = false)
    {
        $Query = array(  );
        foreach( $buildQuery as $field => $value ) 
        {
            if( is_array($value) ) 
            {
                if( $value["escape"] == 0 ) 
                {
                    $Query[] = "`" . $field . "` = " . $value["value"];
                }
                else
                {
                    $Query[] = "`" . $field . "` = " . $this->escape($value["value"]);
                }

            }
            else
            {
                $Query[] = "`" . $field . "` = " . $this->escape($value);
            }

        }
        $SET = implode(",", $Query);
        if( $isShutDownQuery ) 
        {
            return $this->shutdown_query("UPDATE `" . $table . "` SET " . $SET . (($where ? " WHERE " . $where : "")));
        }

        return $this->query("UPDATE `" . $table . "` SET " . $SET . (($where ? " WHERE " . $where : "")));
    }

    public function delete($table, $WHERE)
    {
        $this->execute_query("DELETE FROM " . $table . (($WHERE ? " WHERE " . $WHERE : "")));
        return $this->affected_rows();
    }

    public function truncate($table)
    {
        return $this->execute_query("TRUNCATE TABLE `" . $table . "`");
    }

    public function query_result($query, $buffered = true)
    {
        $returnarray = false;
        $queryresult = $this->execute_query($query, $buffered);
        if( $this->num_rows($queryresult) ) 
        {
            $returnarray = $this->fetch_assoc($queryresult);
            $this->free($queryresult);
        }

        return $returnarray;
    }

    public function exec_shutdown_queries()
    {
        if( $this->shutdown_queries && count($this->shutdown_queries) ) 
        {
            foreach( $this->shutdown_queries as $query ) 
            {
                $this->execute_query($query);
            }
        }

    }

    public function row_count($query, $buffered = true)
    {
        return $this->num_rows($this->execute_query($query, $buffered));
    }

    public function shutdown_query($query)
    {
        $this->shutdown_queries[] = $query;
    }

    public function lock_tables($tablelist = "")
    {
        if( !empty($tablelist) && is_array($tablelist) ) 
        {
            $sql = "";
            foreach( $tablelist as $name => $type ) 
            {
                $sql .= ((!empty($sql) ? ", " : "")) . $name . " " . $type;
            }
            $this->query("LOCK TABLES " . $sql);
            $this->locked = true;
        }

    }

    public function unlock_tables()
    {
        if( $this->locked ) 
        {
            $this->query("UNLOCK TABLES");
            $this->locked = false;
        }

    }

    public function affected_rows()
    {
        return mysqli_affected_rows($this->connection);
    }

    public function insert_id()
    {
        return mysqli_insert_id($this->connection);
    }

    public function fetch_assoc($query)
    {
        return mysqli_fetch_array($query, MYSQLI_ASSOC);
    }

    public function fetch_row($query)
    {
        return mysqli_fetch_row($query);
    }

    public function num_rows($query)
    {
        return mysqli_num_rows($query);
    }

    public function free($query)
    {
        mysqli_free_result($query);
    }

    public function escape($string)
    {
        return "'" . mysqli_real_escape_string($this->connection, $string) . "'";
    }

    public function escape_no_quotes($string)
    {
        return mysqli_real_escape_string($this->connection, $string);
    }

    public function halt()
    {
        showerror($this->error);
    }

    public function close()
    {
        mysqli_close($this->connection);
    }

}

function aaaaov()
{
    @date_default_timezone_set("GMT");
    define("TIMENOW", time());
    if( function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc() ) 
    {
        require("./../library/functions/functions_undoMagicQuotes.php");
    }

    if( function_exists("get_magic_quotes_runtime") && get_magic_quotes_runtime() ) 
    {
        @set_magic_quotes_runtime(false);
    }

    @ini_set("memory_limit", "256M");
    @set_time_limit(0);
    @ignore_user_abort(true);
    @ini_set("pcre.backtrack_limit", -1);
    @ini_set("output_buffering", false);
    while( ob_get_level() ) 
    {
        @ob_end_clean();
    }
    define("SCRIPT_NAME", "install.php");
    define("REALPATH", str_replace("\\", "/", realpath(".")) . "/");
    define("ROOTPATH", str_replace("\\", "/", realpath("./../")) . "/");
    define("DATAPATH", ROOTPATH . "data/");
    define("JSPATH", ROOTPATH . "js/");
    define("LIBRARYPATH", ROOTPATH . "library/");
    define("STYLEPATH", ROOTPATH . "styles/");
    @error_reporting(30719);
    @ini_set("display_errors", "Off");
    @set_error_handler("InstallErrorHandler");
    define("IN_INSTALL", true);
    define("TSUE_ADMINCP_URL", aaaapc());
    define("TSUE_FRONT_URL", str_replace("/tsue_install/", "", TSUE_ADMINCP_URL));
    define("TSUE_URL", "T29wcw==");
    define("TSUE_URL3", "T29wcw==");
    define("U", (!empty($_SERVER["SERVER_NAME"]) ? fixurl($_SERVER["SERVER_NAME"]) : (!empty($_SERVER["HTTP_HOST"]) ? fixurl($_SERVER["HTTP_HOST"]) : "")));
    define("I", getserverip());
    define("V", "2.2");
}

function installerrorhandler($errno, $errstr, $errfile, $errline)
{
    $Message = "\r\n------------------------------------------\r\n" . date("d-m-Y H:i:s") . "\r\n[" . $errno . "] " . $errstr . "\r\nPHP Error on line " . number_format($errline) . " in file " . $errfile . "\r\n" . PHP_VERSION . " " . PHP_OS . "\r\n------------------------------------------";
    @file_put_contents(DATAPATH . "errors/" . SCRIPT_NAME . ".log", $Message, FILE_APPEND);
}

function getserverip()
{
    $ipFile = DATAPATH . "cache/ip.srv";
    if( isset($_SERVER["SERVER_ADDR"]) && !empty($_SERVER["SERVER_ADDR"]) && is_valid_ip($_SERVER["SERVER_ADDR"]) ) 
    {
        $ip = $_SERVER["SERVER_ADDR"];
    }
    else
    {
        if( isset($_SERVER["LOCAL_ADDR"]) && !empty($_SERVER["LOCAL_ADDR"]) && is_valid_ip($_SERVER["LOCAL_ADDR"]) ) 
        {
            $ip = $_SERVER["LOCAL_ADDR"];
        }
        else
        {
            if( file_exists($ipFile) && TIMENOW < filemtime($ipFile) + 1800 ) 
            {
                $ip = file_get_contents($ipFile);
            }
            else
            {
                if( function_exists("curl_init") && ($ch = curl_init()) ) 
                {
                    curl_setopt($ch, CURLOPT_URL, base64_decode(TSUE_URL3));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:12.0) Gecko/20100101 Firefox/12.0");
                    $ip = curl_exec($ch);
                    curl_close($ch);
                    if( is_writable(DATAPATH . "cache/") ) 
                    {
                        @file_put_contents($ipFile, $ip);
                    }

                }

            }

        }

    }

    if( is_valid_ip($ip) ) 
    {
        return $ip;
    }

    if( file_exists($ipFile) ) 
    {
        @unlink($ipFile);
    }

}

function is_valid_ip($ip)
{
    return $ip != "127.0.0.1" && $ip != "::1" && filter_var($ip, FILTER_VALIDATE_IP);
}

function aaaaoy()
{
    return array( "announceLog/", "avatars/l/", "avatars/m/", "avatars/s/", "backups/", "cache/", "countryFlags/", "downloads/files/", "downloads/previews/", "errors/", "gallery/l/", "gallery/s/", "languageFlags/", "posts/", "smilies/", "subTitles/", "torrents/auto_uploader/", "torrents/category_images/", "torrents/imdb/", "torrents/nfo/", "torrents/torrent_files/", "torrents/torrent_genres/", "torrents/torrent_images/l/", "torrents/torrent_images/m/", "torrents/torrent_images/s/" );
}

function aaaaoz($path = "")
{
    if( !$path || !is_dir($path) ) 
    {
        return false;
    }

    clearstatcache();
    $Directories = scandir($path);
    if( !$Directories ) 
    {
        return false;
    }

    $aaaapd = array(  );
    foreach( $Directories as $directory ) 
    {
        if( is_dir($path . $directory) && $directory != "." && $directory != ".." ) 
        {
            $aaaapd[] = $path . $directory . "/";
        }

    }
    return $aaaapd;
}

function aaaapc()
{
    $port = (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] ? intval($_SERVER["SERVER_PORT"]) : 0);
    $port = (in_array($port, array( 80, 443 )) ? "" : ":" . $port);
    $scheme = (":443" == $port || isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] && $_SERVER["HTTPS"] != "off" ? "https://" : "http://");
    $host = fetch_server_value("HTTP_HOST");
    $name = fetch_server_value("SERVER_NAME");
    $host = (substr_count($name, ".") < substr_count($host, ".") ? $host : $name);
    define("_HTTP_HOST", $host);
    if( !($scriptpath = fetch_server_value("PATH_INFO")) && !($scriptpath = fetch_server_value("REDIRECT_URL")) && !($scriptpath = fetch_server_value("URL")) && !($scriptpath = fetch_server_value("PHP_SELF")) ) 
    {
        $scriptpath = fetch_server_value("SCRIPT_NAME");
    }

    $url = $scheme . $host . "/" . str_replace(SCRIPT_NAME, "", ltrim($scriptpath, "/\\"));
    return $url;
}

function fetch_server_value($name)
{
    if( isset($_SERVER[$name]) && $_SERVER[$name] ) 
    {
        return $_SERVER[$name];
    }

    if( isset($_ENV[$name]) && $_ENV[$name] ) 
    {
        return $_ENV[$name];
    }

    return false;
}

function showmessage($message = "")
{
    sleep(2);
    exit( $message );
}

function checkdirectorypermissions($Directory = "")
{
    clearstatcache();
    return !empty($Directory) && is_dir($Directory) && is_writable($Directory);
}

function showerror($error)
{
    showmessage("-ERROR-Fatal Error: " . $error);
}

function aaaaox($error)
{
    showmessage("<div id=\"error\">" . $error . "</div>");
}

function showdialog($dialog)
{
    showmessage("-DIALOG-" . $dialog);
}

function aaaape($error = "", $useDIV = true)
{
    global $isAjax;
    $_SESSION["lk"] = "";
    if( $isAjax ) 
    {
        showmessage("-ERROR-" . $error);
    }
    else
    {
        printoutput(($useDIV ? "<div id=\"error\">" . $error . "</div>" : $error));
    }

}

function printoutput($HTML, $useInstallJS = false)
{
    $Output = "\r\n\t<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\r\n\t<html>\r\n\t\t<head>\r\n\t\t\t<noscript><meta http-equiv=refresh content=\"0; URL=" . TSUE_ADMINCP_URL . SCRIPT_NAME . "?noscript=1\" /></noscript>\r\n\t\t\t<meta charset=\"utf-8\" />\r\n\t\t\t<title>TSUE Install System</title>\r\n\t\t\t<link rel=\"stylesheet\" type=\"text/css\" href=\"css/install.css\" />\r\n\t\t\t<script type=\"text/javascript\">var TSUEPhrases = {}</script>\r\n\t\t\t<script type=\"text/javascript\" src=\"" . TSUE_FRONT_URL . "/js/tsue/jquery.js\"></script>\r\n\t\t</head>\r\n\t\t\r\n\t\t<body>\r\n\r\n\t\t\t<div id=\"dialog\"></div>\r\n\t\t\t<div id=\"header\"></div>\r\n\t\t\t<div id=\"wrapper\">\t\t\t\r\n\t\t\t\t<div id=\"container\">" . $HTML . "</div>\t\t\t\r\n\t\t\t</div>\r\n\r\n\t\t\t<div id=\"footer\"></div>\r\n\t\t\t\r\n\t\t\t<div id=\"copyright\">\r\n                <div class=\"tsue dotted-bottom-gray\">Software by TSUE&trade; &copy;2014 <a href=\"http://www.FMEdition.com\" target=\"_blank\">www.FMEdition.com</a></div>\r\n            </div>\r\n\r\n\t\t\t" . (($useInstallJS ? "\r\n\t\t\t<script type=\"text/javascript\">\r\n\t\t\t\tvar stepCount = 0,\r\n\t\t\t\t\$totalSteps = 7,\r\n\t\t\t\t\$progressbar = \$(\"#progressbar\"),\r\n\t\t\t\t\$progress = \$(\"#progress\"),\r\n\t\t\t\t\$result = \$(\"#welcomeScreen\"),\r\n\t\t\t\t\$dialog = \$(\"#dialog\"),\r\n\t\t\t\t\$loader = \$('<img src=\"images/ajax-loader.gif\" alt=\"loading..\" title=\"loading..\" border=\"0\" id=\"loader\" />'),\r\n\t\t\t\t\$bigLoader = \$('<img src=\"images/ajax-loader-big.gif\" alt=\"loading..\" title=\"loading..\" border=\"0\" id=\"bigLoader\" />'),\r\n\t\t\t\t\$adminAccount = null;\r\n\r\n\t\t\t\tvar openDialog = function(text, onFinish)\r\n\t\t\t\t{\r\n\t\t\t\t\t\$dialog.html(text);\r\n\t\t\t\t\t\$dialog.overlay\r\n\t\t\t\t\t({\r\n\t\t\t\t\t\tmask: {color: \"#ccc\",loadSpeed:200,opacity:0.7},\r\n\t\t\t\t\t\tcloseOnClick: false,\r\n\t\t\t\t\t\tcloseOnEsc: false,\r\n\t\t\t\t\t\tload: true,\r\n\t\t\t\t\t\tonClose: onFinish,\r\n\t\t\t\t\t\tonBeforeLoad: function(){},\r\n\t\t\t\t\t\tonBeforeClose: function(){}\r\n\t\t\t\t\t}).load();\r\n\t\t\t\t};\r\n\r\n\t\t\t\t\$(document).on(\"submit\", \"#dialogForm\", function(e)\r\n\t\t\t\t{\r\n\t\t\t\t\te.preventDefault();\r\n\t\t\t\t\tvar \$thisForm = \$(this),  \$serialize = \$thisForm.serialize();\r\n\r\n\t\t\t\t\t\$(\"#error\").remove();\r\n\t\t\t\t\t\r\n\t\t\t\t\t\$bigLoader.appendTo(\"#formDIV\");\r\n\r\n\t\t\t\t\t\$.ajax\r\n\t\t\t\t\t({\r\n\t\t\t\t\t\ttype: \"POST\",\r\n\t\t\t\t\t\tdata: \$serialize,\r\n\t\t\t\t\t\turl: \"" . SCRIPT_NAME . "\",\r\n\t\t\t\t\t\tsuccess: function(formResults)\r\n\t\t\t\t\t\t{\r\n\t\t\t\t\t\t\t\$bigLoader.remove();\r\n\t\t\t\t\t\t\tif(formResults && formResults.match(/\"error\"/))\r\n\t\t\t\t\t\t\t{\r\n\t\t\t\t\t\t\t\t\$thisForm.find(\"#formResults\").remove();\r\n\t\t\t\t\t\t\t\t\$('<div id=\"formResults\">'+formResults+'</div>').prependTo(\$thisForm);\r\n\t\t\t\t\t\t\t}\r\n\t\t\t\t\t\t\telse if(formResults)\r\n\t\t\t\t\t\t\t{\r\n\t\t\t\t\t\t\t\t\$dialog.empty().overlay().close();\r\n\t\t\t\t\t\t\t\t\$result.html(formResults);\r\n\t\t\t\t\t\t\t}\r\n\t\t\t\t\t\t}\r\n\t\t\t\t\t});\r\n\t\t\t\t\treturn false;\r\n\t\t\t\t});\r\n\r\n\t\t\t\tvar ajaxProgress = function()\r\n\t\t\t\t{\r\n\t\t\t\t\tstepCount++;\r\n\r\n\t\t\t\t\tvar pWidth = Math.round((stepCount/\$totalSteps*100));\r\n\t\t\t\t\tif(pWidth > 100 || stepCount >= \$totalSteps)\r\n\t\t\t\t\t{\r\n\t\t\t\t\t\tpWidth = 100;\r\n\t\t\t\t\t}\r\n\r\n\t\t\t\t\tif(\$progress.width() < 100)\r\n\t\t\t\t\t{\r\n\t\t\t\t\t\t\$progress.animate({width: pWidth+\"%\"}, 3000);\r\n\t\t\t\t\t}\r\n\r\n\t\t\t\t\t\$.ajax\r\n\t\t\t\t\t({\r\n\t\t\t\t\t\ttype: \"POST\",\r\n\t\t\t\t\t\tdata: \"isAjax=1&stepCount=\"+stepCount,\r\n\t\t\t\t\t\turl: \"" . SCRIPT_NAME . "\",\r\n\t\t\t\t\t\tsuccess: function(serverResult)\r\n\t\t\t\t\t\t{\r\n\t\t\t\t\t\t\tif(serverResult.match(/-ERROR-/))\r\n\t\t\t\t\t\t\t{\r\n\t\t\t\t\t\t\t\tserverResult = '<div id=\"error\">'+serverResult.replace(/-ERROR-/, '')+'</div>';\r\n\t\t\t\t\t\t\t\t\$result.html(serverResult);\r\n\t\t\t\t\t\t\t\t\$progressbar.hide();\r\n\t\t\t\t\t\t\t\t\$loader.hide();\r\n\t\t\t\t\t\t\t\tstepCount = 0;\r\n\t\t\t\t\t\t\t\treturn false;\r\n\t\t\t\t\t\t\t}\r\n\t\t\t\t\t\t\telse if(serverResult.match(/-DIALOG-/))\r\n\t\t\t\t\t\t\t{\r\n\t\t\t\t\t\t\t\tserverResult = serverResult.replace(/-DIALOG-/, \"\");\r\n\r\n\t\t\t\t\t\t\t\tvar onFinish = function()\r\n\t\t\t\t\t\t\t\t{\r\n\t\t\t\t\t\t\t\t\tajaxProgress();\r\n\t\t\t\t\t\t\t\t};\r\n\t\t\t\t\t\t\t\topenDialog(serverResult, onFinish);\r\n\t\t\t\t\t\t\t}\r\n\t\t\t\t\t\t\telse if(serverResult)\r\n\t\t\t\t\t\t\t{\r\n\t\t\t\t\t\t\t\t\$result.html(serverResult);\r\n\t\t\t\t\t\t\t\tajaxProgress();\r\n\t\t\t\t\t\t\t}\r\n\t\t\t\t\t\t\telse//finished?\r\n\t\t\t\t\t\t\t{\r\n\t\t\t\t\t\t\t\tstepCount=0;\r\n\t\t\t\t\t\t\t\t\$progress.animate({width: \"100%\"}, 100, function(){\$loader.remove()});\r\n\t\t\t\t\t\t\t\t\$progressbar.hide();\r\n\t\t\t\t\t\t\t\t\$loader.hide();\r\n\t\t\t\t\t\t\t}\r\n\t\t\t\t\t\t}\r\n\t\t\t\t\t});\r\n\t\t\t\t};\r\n\r\n\t\t\t\tvar welcomeScreen = function()\r\n\t\t\t\t{\r\n\t\t\t\t\t\$.ajax\r\n\t\t\t\t\t({\r\n\t\t\t\t\t\ttype: \"POST\",\r\n\t\t\t\t\t\tdata: \"isAjax=1&doAction=requirementsCheck\",\r\n\t\t\t\t\t\turl: \"" . SCRIPT_NAME . "\",\r\n\t\t\t\t\t\tsuccess: function(serverResult)\r\n\t\t\t\t\t\t{\r\n\t\t\t\t\t\t\t\$(\"#welcomeScreen\").html(serverResult);\r\n\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\tif(serverResult.match(/Requirements Met/))\r\n\t\t\t\t\t\t\t{\r\n\t\t\t\t\t\t\t\t\$(\"#beginInstall\").show();\r\n\t\t\t\t\t\t\t}\r\n\t\t\t\t\t\t}\r\n\t\t\t\t\t});\r\n\t\t\t\t}\r\n\r\n\t\t\t\tvar enableButtons = function()\r\n\t\t\t\t{\r\n\t\t\t\t\t\$(\"body :input\").attr(\"disabled\", false);\r\n\t\t\t\t}\r\n\r\n\t\t\t\tvar disableButtons = function()\r\n\t\t\t\t{\r\n\t\t\t\t\t\$(\"body :input\").attr(\"disabled\", true);\r\n\t\t\t\t}\r\n\r\n\t\t\t\tvar initTipsy = function()\r\n\t\t\t\t{\r\n\t\t\t\t\t\$(\".hint\").tipsy({title: \"id\", gravity: \"sw\", html: true});\r\n\t\t\t\t}\r\n\r\n\t\t\t\t\$(document).ready(function()\r\n\t\t\t\t{\r\n\t\t\t\t\t\$.ajaxSetup({timeout: 600000});\r\n\t\t\t\t\t\$(document).ajaxStart(function(){disableButtons()}).ajaxComplete(function(){enableButtons(); initTipsy();});\r\n\r\n\t\t\t\t\twelcomeScreen();\r\n\t\t\t\t\t\r\n\t\t\t\t\t\$(document).on(\"click\", \"#start\", function(e)\r\n\t\t\t\t\t{\r\n\t\t\t\t\t\te.preventDefault();\r\n\t\t\t\t\t\t\$(\"#start,#reload\").remove();\r\n\t\t\t\t\t\t\$(\"#requirementsMet,#licenseAgreement\").remove();\r\n\t\t\t\t\t\t\$progressbar.show();\r\n\t\t\t\t\t\t\$loader.appendTo(\"#buttons\");\r\n\t\t\t\t\t\t\$result.html(\"Initializing...\");\r\n\t\t\t\t\t\tajaxProgress();\r\n\t\t\t\t\t\treturn false;\r\n\t\t\t\t\t});\r\n\r\n\t\t\t\t\t\$(document).on(\"click\", \"#reload\", function(e)\r\n\t\t\t\t\t{\r\n\t\t\t\t\t\te.preventDefault();\r\n\t\t\t\t\t\twindow.location.reload();\r\n\t\t\t\t\t\treturn false;\r\n\t\t\t\t\t});\r\n\t\t\t\t});\r\n\t\t\t</script>" : "")) . "\r\n\r\n\t\t</body>\r\n\t</html>";
    exit( $Output );
}

function getpostvar($name = "")
{
    return ($name && isset($_POST[$name]) ? $_POST[$name] : "");
}

function showhint($hint = "", $link = "help")
{
    return "<span class=\"hint\" id=\"" . $hint . "\">" . $link . "</span>";
}

function aaaapf($hint = "")
{
    return "<span class=\"inputHint\">" . $hint . "</span>";
}

function aaaapg()
{
    return "<span class=\"okMessage\">CONFIRMED!</span>";
}

function showtitle($title = "")
{
    return "<span class='title'>" . $title . "</span>";
}

function generatepasskey($length = 30)
{
    $passkey = "";
    for( $i = 0; $i < $length; $i++ ) 
    {
        $passkey .= chr(rand(33, 126));
    }
    return sha1($passkey);
}

function file_extension($filename)
{
    return substr(strrchr($filename, "."), 1);
}

function updatesettings($settingname, $settingvalues)
{
    global $TSUE;
    $BuildQuery = array( "settingname" => $settingname, "settingvalues" => serialize($settingvalues) );
    $TSUE["TSUE_Database"]->delete("tsue_settings", "settingname = " . $TSUE["TSUE_Database"]->escape($settingname));
    return $TSUE["TSUE_Database"]->insert("tsue_settings", $BuildQuery);
}

function handleprune()
{
    global $TSUE;
    $TSUE["TSUE_Database"]->truncate("tsue_file_caches");
    $cacheFolder = DATAPATH . "cache/";
    if( !is_dir($cacheFolder) || !is_writable($cacheFolder) ) 
    {
        showerror("Cache folder is not writable: " . $cacheFolder);
    }

    $cacheFiles = scandir($cacheFolder);
    if( count($cacheFiles) <= 2 ) 
    {
        showerror("Cache files does not exists in: " . $cacheFolder);
    }

    foreach( $cacheFiles as $cacheFile ) 
    {
        $_ext = file_extension($cacheFile);
        if( in_array($_ext, array( "tsue", "gz", "js", "gif", "jpg", "png", "jpeg", "zip", "srv" )) ) 
        {
            @unlink($cacheFolder . $cacheFile);
        }

    }
}

function handlerebuildcaches($logANDreturn = true)
{
    global $TSUE;
    $content = "";
    $cacheContents = array(  );
    $Announcement = $TSUE["TSUE_Database"]->query_result("SELECT a.*, m.membername FROM tsue_announcements a LEFT JOIN tsue_members m USING(memberid) WHERE a.active = 1 ORDER BY a.date DESC LIMIT 1");
    if( $Announcement ) 
    {
        $cacheContents["active_announcements_cache"] = array( "aid" => $Announcement["aid"], "memberid" => $Announcement["memberid"], "date" => $Announcement["date"], "title" => $Announcement["title"], "content" => $Announcement["content"] );
    }
    else
    {
        $cacheContents["active_announcements_cache"] = array(  );
    }

    $News = $TSUE["TSUE_Database"]->query("SELECT n.*, m.membername FROM tsue_news n LEFT JOIN tsue_members m USING(memberid) WHERE n.active = 1 ORDER BY n.date DESC");
    if( $TSUE["TSUE_Database"]->num_rows($News) ) 
    {
        while( $nItem = $TSUE["TSUE_Database"]->fetch_assoc($News) ) 
        {
            $cacheContents["active_news_cache"][] = array( "nid" => $nItem["nid"], "memberid" => $nItem["memberid"], "date" => $nItem["date"], "title" => $nItem["title"], "content" => $nItem["content"] );
        }
    }
    else
    {
        $cacheContents["active_news_cache"] = array(  );
    }

    $aaaaph = $TSUE["TSUE_Database"]->query("SELECT * FROM tsue_ban_country ORDER BY 'country'");
    if( $TSUE["TSUE_Database"]->num_rows($aaaaph) ) 
    {
        while( $nItem = $TSUE["TSUE_Database"]->fetch_assoc($aaaaph) ) 
        {
            $cacheContents["banned_countries_cache"][] = $nItem["country"];
        }
    }
    else
    {
        $cacheContents["banned_countries_cache"] = array(  );
    }

    $aaaapi = $TSUE["TSUE_Database"]->query("SELECT * FROM tsue_ban_email ORDER BY 'banned_email'");
    if( $TSUE["TSUE_Database"]->num_rows($aaaapi) ) 
    {
        while( $nItem = $TSUE["TSUE_Database"]->fetch_assoc($aaaapi) ) 
        {
            $cacheContents["banned_emails_cache"][] = $nItem["banned_email"];
        }
    }
    else
    {
        $cacheContents["banned_emails_cache"] = array(  );
    }

    $bannedIPs = $TSUE["TSUE_Database"]->query("SELECT * FROM tsue_ip_match WHERE match_type = 'banned'");
    if( $TSUE["TSUE_Database"]->num_rows($bannedIPs) ) 
    {
        while( $nItem = $TSUE["TSUE_Database"]->fetch_assoc($bannedIPs) ) 
        {
            $cacheContents["banned_ips_cache"][$nItem["first_octet"]][] = array( $nItem["start_range"], $nItem["end_range"] );
        }
    }
    else
    {
        $cacheContents["banned_ips_cache"] = array(  );
    }

    $Smilies = $TSUE["TSUE_Database"]->query("SELECT smilie_text, smilie_title, smilie_file FROM tsue_smilies");
    if( $TSUE["TSUE_Database"]->num_rows($Smilies) ) 
    {
        while( $Smilie = $TSUE["TSUE_Database"]->fetch_assoc($Smilies) ) 
        {
            $cacheContents["dialog_smilies_cache"][] = $Smilie;
        }
    }
    else
    {
        $cacheContents["dialog_smilies_cache"] = array(  );
    }

    $permissions = $TSUE["TSUE_Database"]->query("SELECT * FROM tsue_forums_permissions ORDER BY forumid ASC");
    if( $TSUE["TSUE_Database"]->num_rows($permissions) ) 
    {
        while( $nItem = $TSUE["TSUE_Database"]->fetch_assoc($permissions) ) 
        {
            $cacheContents["forums_permissions_cache"][] = array( "forumid" => $nItem["forumid"], "membergroupid" => $nItem["membergroupid"], "permissions" => $nItem["permissions"] );
        }
    }
    else
    {
        $cacheContents["forums_permissions_cache"] = array(  );
    }

    $prefixes = $TSUE["TSUE_Database"]->query("SELECT * FROM tsue_forums_thread_prefixes ORDER BY pname ASC");
    if( $TSUE["TSUE_Database"]->num_rows($prefixes) ) 
    {
        while( $nItem = $TSUE["TSUE_Database"]->fetch_assoc($prefixes) ) 
        {
            $cacheContents["forums_thread_prefixes"][] = array( "pid" => $nItem["pid"], "pname" => $nItem["pname"], "cssname" => $nItem["cssname"], "viewpermissions" => $nItem["viewpermissions"] );
        }
    }
    else
    {
        $cacheContents["forums_thread_prefixes"] = array(  );
    }

    $Plugins = $TSUE["TSUE_Database"]->query("SELECT pluginid, name, filename, contents, viewpermissions, pluginOptions FROM tsue_plugins WHERE `active` = 1");
    if( $TSUE["TSUE_Database"]->num_rows($Plugins) ) 
    {
        while( $nItem = $TSUE["TSUE_Database"]->fetch_assoc($Plugins) ) 
        {
            $cacheContents["tsue_plugins_cache"][] = $nItem;
        }
    }
    else
    {
        $cacheContents["tsue_plugins_cache"] = array(  );
    }

    $Genres = $TSUE["TSUE_Database"]->query("SELECT gid, gname, gicon, categories FROM tsue_torrents_genres WHERE active = 1 ORDER BY gname ASC");
    if( $TSUE["TSUE_Database"]->num_rows($Genres) ) 
    {
        while( $Genre = $TSUE["TSUE_Database"]->fetch_assoc($Genres) ) 
        {
            $cacheContents["tsue_torrents_genres_cache"][] = $Genre;
        }
    }
    else
    {
        $cacheContents["tsue_torrents_genres_cache"] = array(  );
    }

    $Themes = $TSUE["TSUE_Database"]->query("SELECT themeid FROM tsue_themes WHERE active = 1");
    if( $TSUE["TSUE_Database"]->num_rows($Themes) ) 
    {
        while( $Theme = $TSUE["TSUE_Database"]->fetch_assoc($Themes) ) 
        {
            $aaaapj[] = $Theme["themeid"];
        }
        $TSUE["TSUE_Settings"]->settings["global_settings"]["available_themes"] = implode(",", $aaaapj);
    }

    $Languages = $TSUE["TSUE_Database"]->query("SELECT languageid FROM tsue_languages WHERE active = 1");
    if( $TSUE["TSUE_Database"]->num_rows($Languages) ) 
    {
        while( $Language = $TSUE["TSUE_Database"]->fetch_assoc($Languages) ) 
        {
            $languageCache[] = $Language["languageid"];
        }
        $TSUE["TSUE_Settings"]->settings["global_settings"]["available_languages"] = implode(",", $languageCache);
    }

    updatesettings("global_settings", $TSUE["TSUE_Settings"]->settings["global_settings"]);
    if( !empty($cacheContents) ) 
    {
        foreach( $cacheContents as $settingname => $settingvalues ) 
        {
            updatesettings($settingname, $settingvalues);
        }
    }

}

function fixurl($url = "")
{
    return strtolower(str_replace(array( "http://", "https://", "www.", "http://www.", "https://www." ), "", $url));
}

function encodestring($String = "", $AnahtarKelime = "T29wcw==")
{
    $AnahtarKelime = base64_decode($AnahtarKelime);
    $result = "";
    for( $i = 0; $i < strlen($String); $i++ ) 
    {
        $char = substr($String, $i, 1);
        $keychar = substr($AnahtarKelime, $i % strlen($AnahtarKelime) - 1, 1);
        $char = chr(ord($char) + ord($keychar));
        $result .= $char;
    }
    return urlencode(base64_encode($result));
}

function decodestring($String = "", $AnahtarKelime = "T29wcw==")
{
    $AnahtarKelime = base64_decode($AnahtarKelime);
    $String = urldecode($String);
    $result = "";
    $String = urldecode(base64_decode($String));
    for( $i = 0; $i < strlen($String); $i++ ) 
    {
        $char = substr($String, $i, 1);
        $keychar = substr($AnahtarKelime, $i % strlen($AnahtarKelime) - 1, 1);
        $char = chr(ord($char) - ord($keychar));
        $result .= $char;
    }
    return $result;
}

function compare_key($installkey = "")
{
    return strlen($installkey) === 36 && preg_match(str_replace("#", "[0-9,A-F]", "{########-####-####-####-############}"), $installkey);
}

function is_valid_url($url = "")
{
    return (!empty($url) && !preg_match("#^[a-z0-9-\\.]+\$#", $url) ? false : true);
}

function aaaapa($type = 0)
{
    $serverResponse = "";
    if( function_exists("curl_init") && ($ch = curl_init()) ) 
    {
        curl_setopt($ch, CURLOPT_URL, decodestring(TSUE_URL));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "d3bJmHWuutzXxtrfxrfR1NE%3D");
        curl_setopt($ch, CURLOPT_REFERER, U);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "t=" . $type . "&u=" . encodestring(U) . "&i=" . encodestring(I) . "&v=" . V);
        $serverResponse = curl_exec($ch);
        curl_close($ch);
        if( $serverResponse ) 
        {
            $serverResponse = decodestring($serverResponse);
        }

    }

    return $serverResponse;
}

function aaaaow()
{
    global $isAjax;
    $ERROR = "";
    session_name("tsueinstallation");
    session_start();
    return true;
    if( file_exists($_SERVER["DOCUMENT_ROOT"] . "/tsue/l.php") ) 
    {
        aaaape("Critical Security Error.");
    }

    $serverResponse = aaaapa(1);
    if( !$serverResponse ) 
    {
        aaaape("Could not connect to TSUE license server.");
    }

    if( !preg_match("#\\[lk\\](.*)\\[\\/lk\\]#", $serverResponse, $licenseKey) ) 
    {
        $serverResponse = str_replace(array( "margin: 50px auto 0 auto;", "width: 600px;" ), array( "margin: 0 auto;", "width: 100%;" ), $serverResponse);
        aaaape($serverResponse, false);
    }

    $licenseKey = (isset($licenseKey["1"]) ? $licenseKey["1"] : "");
    if( !compare_key($licenseKey) ) 
    {
        aaaape("Could not fetch the correct license key.");
    }

    if( strtoupper($_SERVER["REQUEST_METHOD"]) === "POST" && isset($_POST["licenseKey"]) && !empty($_POST["licenseKey"]) ) 
    {
        if( isset($_POST["CSRFKey"]) && $_POST["CSRFKey"] && isset($_SESSION["CSRFKey"]) && $_SESSION["CSRFKey"] && $_POST["CSRFKey"] === $_SESSION["CSRFKey"] ) 
        {
            if( compare_key($_POST["licenseKey"]) && $_POST["licenseKey"] === $licenseKey ) 
            {
                $_SESSION["lk"] = $_POST["licenseKey"];
            }
            else
            {
                $_SESSION["lk"] = "";
                $ERROR = "<div id=\"error\">The entered License Key could not be verified.</div>";
            }

        }
        else
        {
            $_SESSION["lk"] = "";
            $_SESSION["CSRFKey"] = "";
            $ERROR = "<div id=\"error\">Invalid Security Token.</div>";
        }

    }

    if( !isset($_SESSION["lk"]) || !compare_key($_SESSION["lk"]) || $_SESSION["lk"] != $licenseKey ) 
    {
        $_SESSION["lk"] = "";
        $CSRFKey = sha1(microtime());
        $_SESSION["CSRFKey"] = $CSRFKey;
        if( $isAjax ) 
        {
            showmessage("-ERROR-Please refresh page and re-enter your License Key.");
        }

        printoutput("\r\n\t\t\t" . $ERROR . "\r\n\t\t\t<form method=\"post\" id=\"checkLicense\">\r\n\t\t\t<input type=\"hidden\" name=\"CSRFKey\" value=\"" . $CSRFKey . "\" />\r\n\t\t\t\t<div id=\"licenseKey\">\r\n\t\t\t\t\t" . showhint("For security reasons, you must verify your license key.<br />You can find your license key in our Client Area.") . "\r\n\t\t\t\t\tEnter License Key Here: <input type=\"text\" name=\"licenseKey\" class=\"key\" value=\"\" /> <input type=\"submit\" value=\"verifiy\" class=\"submit\" />\r\n\t\t\t\t</div>\r\n\t\t\t</form>\r\n\t\t\t<script type=\"text/javascript\">\r\n\t\t\t\t\$(document).ready(function()\r\n\t\t\t\t{\r\n\t\t\t\t\t\$(\".hint\").tipsy({title: \"id\", gravity: \"sw\", html: true});\r\n\t\t\t\t\t\$(\"#checkLicense\").submit(function(e)\r\n\t\t\t\t\t{\r\n\t\t\t\t\t\t\$(\"input[type='submit']\").val(\"checking...\");\r\n\t\t\t\t\t});\r\n\t\t\t\t});\r\n\t\t\t</script>\r\n\t\t");
    }

}

function aaaapb()
{
    return array( "Options" => 1, "Pages" => 1, "Polls" => 1, "News" => 1, "FAQ" => 1, "Torrent Categories" => 1, "Market" => 1, "API Manager" => 1, "Cron Entries" => 1, "Rebuild Caches" => 1, "Logs" => 1, "Server" => 1, "Notes" => 1, "Statistics" => 1, "Read PM" => 1, "Database" => 1, "Announcements" => 1, "Torrents" => 1, "Add-ons" => 1, "TSUE Store" => 1, "Recent Comments" => 1, "Torrent Genres" => 1, "Advertisements" => 1, "Shoutbox Channels" => 1, "Plugin Manager" => 1, "Forum Manager" => 1, "Appearance" => 1, "All Members" => 1, "Awaiting Approval" => 1, "Banned Members" => 1, "Warned Members" => 1, "Membergroups" => 1, "Email Members" => 1, "Search Members" => 1, "Peers" => 1, "Account Upgrades" => 1, "Muted Members" => 1, "PM Members" => 1, "Promotions" => 1, "Duplicate Ips" => 1, "Hit and Runners" => 1, "Test Permissions" => 1, "Administrators" => 1, "Gift" => 1, "Mass Invite" => 1, "Awards" => 1, "Auto Warned Members" => 1, "Uploader Activity" => 1, "First Line Support" => 1, "Spam Cleaner" => 1, "Prune" => 1, "Smilies" => 1, "Country Flags" => 1, "Attachment Browser" => 1, "Downloads" => 1, "Permissions" => 1, "Cleanup" => 1 );
}