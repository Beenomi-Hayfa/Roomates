<?php
// ini_set('display_errors', 0);
// ini_set('display_startup_errors', 1);
error_reporting(0);
@ini_set("max_execution_time", 0);
@ini_set("memory_limit", "-1");
@set_time_limit(0);
require_once "config.php";
require_once "assets/libraries/DB/vendor/autoload.php";
require_once "assets/libraries/PayPal/vendor/paypal/rest-api-sdk-php/lib.php";

$wo           = array();
// Connect to SQL Server
$sqlConnect   = $wo["sqlConnect"] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);
// create new mysql connection
$mysqlMaria   = new Mysql;
// Handling Server Errors
$ServerErrors = array();
if (mysqli_connect_errno()) {
    $ServerErrors[] = "Failed to connect to MySQL: " . mysqli_connect_error();
}
if (!function_exists("curl_init")) {
    $ServerErrors[] = "PHP CURL is NOT installed on your web server !";
}
if (!extension_loaded("gd") && !function_exists("gd_info")) {
    $ServerErrors[] = "PHP GD library is NOT installed on your web server !";
}
if (!extension_loaded("zip")) {
    $ServerErrors[] = "ZipArchive extension is NOT installed on your web server !";
}
$query = mysqli_query($sqlConnect, "SET NAMES utf8mb4");
if (isset($ServerErrors) && !empty($ServerErrors)) {
    foreach ($ServerErrors as $Error) {
        echo "<h3>" . $Error . "</h3>";
    }
    die();
}
$baned_ips = Wo_GetBanned("user");
if (in_array($_SERVER["REMOTE_ADDR"], $baned_ips)) {
    exit();
}
$config    = Wo_GetConfig();
$db        = new MysqliDb($sqlConnect);
$all_langs = Wo_LangsNamesFromDB();
foreach ($all_langs as $key => $value) {
    $insert = false;
    if (!in_array($value, array_keys($config))) {
        $db->insert(T_CONFIG, array(
            "name" => $value,
            "value" => 1
        ));
        $insert = true;
    }
}
if ($insert == true) {
    $config = Wo_GetConfig();
}
if (isset($_GET["theme"]) && in_array($_GET["theme"], array(
    "default",
    "sunshine",
    "wowonder",
    "wondertag"
))) {
    $_SESSION["theme"] = $_GET["theme"];
}
if (isset($_SESSION["theme"]) && !empty($_SESSION["theme"])) {
    $config["theme"] = $_SESSION["theme"];
    if ($_SERVER["REQUEST_URI"] == "/v2/wonderful" || $_SERVER["REQUEST_URI"] == "/v2/wowonder") {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
    }
}
// Config Url
$config["theme_url"] = $site_url . "/themes/" . $config["theme"];
$config["site_url"]  = $site_url;
$wo["site_url"]      = $site_url;
$config["wasabi_site_url"]         = "https://s3.wasabisys.com";
if (!empty($config["wasabi_bucket_name"])) {
    $config["wasabi_site_url"] = "https://s3.wasabisys.com/".$config["wasabi_bucket_name"];
}
$s3_site_url         = "https://test.s3.amazonaws.com";
if (!empty($config["bucket_name"])) {
    $s3_site_url = "https://{bucket}.s3.amazonaws.com";
    $s3_site_url = str_replace("{bucket}", $config["bucket_name"], $s3_site_url);
}
$config["s3_site_url"] = $s3_site_url;
$s3_site_url_2         = "https://test.s3.amazonaws.com";
if (!empty($config["bucket_name_2"])) {
    $s3_site_url_2 = "https://{bucket}.s3.amazonaws.com";
    $s3_site_url_2 = str_replace("{bucket}", $config["bucket_name_2"], $s3_site_url_2);
}
$config["s3_site_url_2"]   = $s3_site_url_2;
$wo["config"]              = $config;
$ccode                     = Wo_CustomCode("g");
$ccode                     = is_array($ccode) ? $ccode : array();
$wo["config"]["header_cc"] = !empty($ccode[0]) ? $ccode[0] : "";
$wo["config"]["footer_cc"] = !empty($ccode[1]) ? $ccode[1] : "";
$wo["config"]["styles_cc"] = !empty($ccode[2]) ? $ccode[2] : "";

$wo["script_version"]      = $wo["config"]["version"];
$http_header               = "http://";
if (!empty($_SERVER["HTTPS"])) {
    $http_header = "https://";
}
$wo["actual_link"] = $http_header . $_SERVER["HTTP_HOST"] . urlencode($_SERVER["REQUEST_URI"]);
// Define Cache Vireble
$cache             = new Cache();
if (!is_dir("cache")) {
    $cache->Wo_OpenCacheDir();
}
$wo["purchase_code"] = "";
if (!empty($purchase_code)) {
    $wo["purchase_code"] = $purchase_code;
}
// Login With Url
$wo["facebookLoginUrl"]   = $config["site_url"] . "/login-with.php?provider=Facebook";
$wo["twitterLoginUrl"]    = $config["site_url"] . "/login-with.php?provider=Twitter";
$wo["googleLoginUrl"]     = $config["site_url"] . "/login-with.php?provider=Google";
$wo["linkedInLoginUrl"]   = $config["site_url"] . "/login-with.php?provider=LinkedIn";
$wo["VkontakteLoginUrl"]  = $config["site_url"] . "/login-with.php?provider=Vkontakte";
$wo["instagramLoginUrl"]  = $config["site_url"] . "/login-with.php?provider=Instagram";
$wo["QQLoginUrl"]         = $config["site_url"] . "/login-with.php?provider=QQ";
$wo["WeChatLoginUrl"]     = $config["site_url"] . "/login-with.php?provider=WeChat";
$wo["DiscordLoginUrl"]    = $config["site_url"] . "/login-with.php?provider=Discord";
$wo["MailruLoginUrl"]     = $config["site_url"] . "/login-with.php?provider=Mailru";
$wo["OkLoginUrl"]         = $config["site_url"] . "/login-with.php?provider=OkRu";
// Defualt User Pictures
$wo["userDefaultAvatar"]  = "upload/photos/d-avatar.jpg";
$wo["userDefaultFAvatar"] = "upload/photos/f-avatar.jpg";
$wo["userDefaultCover"]   = "upload/photos/d-cover.jpg";
$wo["pageDefaultAvatar"]  = "upload/photos/d-page.jpg";
$wo["groupDefaultAvatar"] = "upload/photos/d-group.jpg";
// Get LoggedIn User Data
$wo["loggedin"]           = false;
$langs                    = Wo_LangsNamesFromDB();
if (Wo_IsLogged() == true) {
    $session_id         = !empty($_SESSION["user_id"]) ? $_SESSION["user_id"] : $_COOKIE["user_id"];
    $wo["user_session"] = Wo_GetUserFromSessionID($session_id);
    $wo["user"]         = Wo_UserData($wo["user_session"]);
    if (!empty($wo["user"]["language"])) {
        if (in_array($wo["user"]["language"], $langs)) {
            $_SESSION["lang"] = $wo["user"]["language"];
        }
    }
    if ($wo["user"]["user_id"] < 0 || empty($wo["user"]["user_id"]) || !is_numeric($wo["user"]["user_id"]) || Wo_UserActive($wo["user"]["username"]) === false) {
        header("Location: " . Wo_SeoLink("index.php?link1=logout"));
    }
    $wo["loggedin"] = true;
} else {
    $wo["userSession"] = getUserProfileSessionID();
}
if (!empty($_GET["c_id"]) && !empty($_GET["user_id"])) {
    $application = "windows";
    if (!empty($_GET["application"])) {
        if ($_GET["application"] == "phone") {
            $application = Wo_Secure($_GET["application"]);
        }
    }
    $c_id             = Wo_Secure($_GET["c_id"]);
    $user_id          = Wo_Secure($_GET["user_id"]);
    $check_if_session = Wo_CheckUserSessionID($user_id, $c_id, $application);
    if ($check_if_session === true) {
        $wo["user"]          = Wo_UserData($user_id);
        $session             = Wo_CreateLoginSession($user_id);
        $_SESSION["user_id"] = $session;
        setcookie("user_id", $session, time() + 10 * 365 * 24 * 60 * 60);
        if ($wo["user"]["user_id"] < 0 || empty($wo["user"]["user_id"]) || !is_numeric($wo["user"]["user_id"]) || Wo_UserActive($wo["user"]["username"]) === false) {
            header("Location: " . Wo_SeoLink("index.php?link1=logout"));
        }
        $wo["loggedin"] = true;
    }
}
if (!empty($_POST["user_id"]) && (!empty($_POST["s"]) || !empty($_POST["access_token"]))) {
    $application  = "windows";
    $access_token = !empty($_POST["s"]) ? $_POST["s"] : $_POST["access_token"];
    if (!empty($_GET["application"])) {
        if ($_GET["application"] == "phone") {
            $application = Wo_Secure($_GET["application"]);
        }
    }
    if ($application == "windows") {
        $access_token = $access_token;
    }
    $s                = Wo_Secure($access_token);
    $user_id          = Wo_Secure($_POST["user_id"]);
    $check_if_session = Wo_CheckUserSessionID($user_id, $s, $application);
    if ($check_if_session === true) {
        $wo["user"] = Wo_UserData($user_id);
        if ($wo["user"]["user_id"] < 0 || empty($wo["user"]["user_id"]) || !is_numeric($wo["user"]["user_id"]) || Wo_UserActive($wo["user"]["username"]) === false) {
            $json_error_data = array(
                "api_status" => "400",
                "api_text" => "failed",
                "errors" => array(
                    "error_id" => "7",
                    "error_text" => "User id is wrong."
                )
            );
            header("Content-type: application/json");
            echo json_encode($json_error_data, JSON_PRETTY_PRINT);
            exit();
        }
        $wo["loggedin"] = true;
    } else {
        $json_error_data = array(
            "api_status" => "400",
            "api_text" => "failed",
            "errors" => array(
                "error_id" => "6",
                "error_text" => "Session id is wrong."
            )
        );
        header("Content-type: application/json");
        echo json_encode($json_error_data, JSON_PRETTY_PRINT);
        exit();
    }
}
// Language Function
if (isset($_GET["lang"]) and !empty($_GET["lang"])) {
    if (in_array($_GET["lang"], array_keys($wo["config"])) && $wo["config"][$_GET["lang"]] == 1) {
        $lang_name = Wo_Secure(strtolower($_GET["lang"]));
        if (in_array($lang_name, $langs)) {
            Wo_CleanCache();
            $_SESSION["lang"] = $lang_name;
            if ($wo["loggedin"] == true) {
                mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `language` = '" . $lang_name . "' WHERE `user_id` = " . Wo_Secure($wo["user"]["user_id"]));
            }
        }
    }
}
if ($wo["loggedin"] == true && $wo["config"]["cache_sidebar"] == 1) {
    if (!empty($_COOKIE["last_sidebar_update"])) {
        if ($_COOKIE["last_sidebar_update"] < time() - 120) {
            Wo_CleanCache();
        }
    } else {
        Wo_CleanCache();
    }
}
if (empty($_SESSION["lang"])) {
    $_SESSION["lang"] = $wo["config"]["defualtLang"];
}
$wo["language"]      = $_SESSION["lang"];
$wo["language_type"] = "ltr";
// Add rtl languages here.
$rtl_langs           = array(
    "arabic"
);
if (!isset($_COOKIE["ad-con"])) {
    setcookie("ad-con", htmlentities(json_encode(array(
        "date" => date("Y-m-d"),
        "ads" => array()
    ))), time() + 10 * 365 * 24 * 60 * 60);
}
$wo["ad-con"] = array();
if (!empty($_COOKIE["ad-con"])) {
    $wo["ad-con"] = json_decode(html_entity_decode($_COOKIE["ad-con"]));
    $wo["ad-con"] = ToArray($wo["ad-con"]);
}
if (!is_array($wo["ad-con"]) || !isset($wo["ad-con"]["date"]) || !isset($wo["ad-con"]["ads"])) {
    setcookie("ad-con", htmlentities(json_encode(array(
        "date" => date("Y-m-d"),
        "ads" => array()
    ))), time() + 10 * 365 * 24 * 60 * 60);
}
if (is_array($wo["ad-con"]) && isset($wo["ad-con"]["date"]) && strtotime($wo["ad-con"]["date"]) < strtotime(date("Y-m-d"))) {
    setcookie("ad-con", htmlentities(json_encode(array(
        "date" => date("Y-m-d"),
        "ads" => array()
    ))), time() + 10 * 365 * 24 * 60 * 60);
}
if (!isset($_COOKIE["_us"])) {
    setcookie("_us", time() + 60 * 60 * 24, time() + 10 * 365 * 24 * 60 * 60);
}
if ((isset($_COOKIE["_us"]) && $_COOKIE["_us"] < time()) || 1) {
    setcookie("_us", time() + 60 * 60 * 24, time() + 10 * 365 * 24 * 60 * 60);
    $expired_stories = $db->where("expire", time(), "<")->get(T_USER_STORY);
    foreach ($expired_stories as $key => $value) {
        $db->where("story_id", $value->id)->delete(T_STORY_SEEN);
    }
    @mysqli_query($sqlConnect, "DELETE FROM " . T_USER_STORY_MEDIA . " WHERE `expire` < " . time());
    @mysqli_query($sqlConnect, "DELETE FROM " . T_USER_STORY . " WHERE `expire` < " . time());
}
// checking if corrent language is rtl.
foreach ($rtl_langs as $lang) {
    if ($wo["language"] == strtolower($lang)) {
        $wo["language_type"] = "rtl";
    }
}
// Icons Virables
$error_icon   = '<i class="fa fa-exclamation-circle"></i> ';
$success_icon = '<i class="fa fa-check"></i> ';
// Include Language File
$wo["lang"]   = Wo_LangsFromDB($wo["language"]);
if (file_exists("assets/languages/extra/" . $wo["language"] . ".php")) {
    require "assets/languages/extra/" . $wo["language"] . ".php";
}
if (empty($wo["lang"])) {
    $wo["lang"] = Wo_LangsFromDB();
}
$wo["second_post_button_icon"] = $config["second_post_button"] == "wonder" ? '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-info"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="8"></line></svg>' : '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-thumbs-down"><path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zm7-13h2.67A2.31 2.31 0 0 1 22 4v7a2.31 2.31 0 0 1-2.33 2H17"></path></svg>';
$theme_settings                = array();
$theme_settings["theme"]       = "wowonder";
if (file_exists("./themes/" . $config["theme"] . "/layout/404/dont-delete-this-file.json")) {
    $theme_settings = json_decode(file_get_contents("./themes/" . $config["theme"] . "/layout/404/dont-delete-this-file.json"), true);
}
if ($theme_settings["theme"] == "wonderful") {
    $wo["second_post_button_icon"] = $config["second_post_button"] == "wonder" ? "exclamation-circle" : "thumb-down";
}
$wo["second_post_button_text"]  = $config["second_post_button"] == "wonder" ? $wo["lang"]["wonder"] : $wo["lang"]["dislike"];
$wo["second_post_button_texts"] = $config["second_post_button"] == "wonder" ? $wo["lang"]["wonders"] : $wo["lang"]["dislikes"];
$wo["marker"]                   = "?";
if ($wo["config"]["seoLink"] == 0) {
    $wo["marker"] = "&";
}
require_once "assets/includes/data.php";
$wo["emo"]                           = $emo;
$wo["profile_picture_width_crop"]    = 150;
$wo["profile_picture_height_crop"]   = 150;
$wo["profile_picture_image_quality"] = 70;
$wo["redirect"]                      = 0;

$wo["update_cache"]                  = "";
if (!empty($wo["config"]["last_update"])) {
    $update_cache = time() - 21600;
    if ($update_cache < $wo["config"]["last_update"]) {
        $wo["update_cache"] = "?" . sha1(time());
    }
}

// night mode
if (empty($_COOKIE["mode"])) {
    setcookie("mode", "day", time() + 10 * 365 * 24 * 60 * 60, "/");
    $_COOKIE["mode"] = "day";
    $wo["mode_link"] = "night";
    $wo["mode_text"] = $wo["lang"]["night_mode"];
} else {
    if ($_COOKIE["mode"] == "day") {
        $wo["mode_link"] = "night";
        $wo["mode_text"] = $wo["lang"]["night_mode"];
    }
    if ($_COOKIE["mode"] == "night") {
        $wo["mode_link"] = "day";
        $wo["mode_text"] = $wo["lang"]["day_mode"];
    }
}
if (!empty($_GET["mode"])) {
    if ($_GET["mode"] == "day") {
        setcookie("mode", "day", time() + 10 * 365 * 24 * 60 * 60, "/");
        $_COOKIE["mode"] = "day";
        $wo["mode_link"] = "night";
        $wo["mode_text"] = $wo["lang"]["night_mode"];
    } elseif ($_GET["mode"] == "night") {
        setcookie("mode", "night", time() + 10 * 365 * 24 * 60 * 60, "/");
        $_COOKIE["mode"] = "night";
        $wo["mode_link"] = "day";
        $wo["mode_text"] = $wo["lang"]["day_mode"];
    }
}
include_once "assets/includes/onesignal_config.php";
if (!empty($_GET["access"]) || empty($_COOKIE["access"])) {
    include_once "assets/includes/paypal_config.php";
    setcookie("access", "1", time() + 24 * 60 * 60, "/");
}
if ($wo["config"]["last_notification_delete_run"] <= time() - 60 * 60 * 24) {
    mysqli_multi_query($sqlConnect, " DELETE FROM " . T_NOTIFICATION . " WHERE `time` < " . (time() - 60 * 60 * 24 * 5) . " AND `seen` <> 0");
    mysqli_query($sqlConnect, "UPDATE " . T_CONFIG . " SET `value` = '" . time() . "' WHERE `name` = 'last_notification_delete_run'");
}
// manage packages
$wo["pro_packages"]       = Wo_GetAllProInfo();
$wo["pro_packages_types"] = array(
    "1" => "star",
    "2" => "hot",
    "3" => "ultima",
    "4" => "vip",
);
// manage packages
$star_package_duration    = 604800; // week in seconds
$hot_package_duration     = 2629743; // month in seconds
$ultima_package_duration  = 31556926; // year in seconds
$vip_package_duration     = 0; // life time package
$time_array               = array(
    "week" => $star_package_duration,
    "month" => $hot_package_duration,
    "year" => $ultima_package_duration,
    "unlimited" => $vip_package_duration
);
if (in_array($wo["pro_packages"]["star"]["time"], array_keys($time_array))) {
    $star_package_duration = $time_array[$wo["pro_packages"]["star"]["time"]];
}
if (in_array($wo["pro_packages"]["hot"]["time"], array_keys($time_array))) {
    $hot_package_duration = $time_array[$wo["pro_packages"]["hot"]["time"]];
}
if (in_array($wo["pro_packages"]["ultima"]["time"], array_keys($time_array))) {
    $ultima_package_duration = $time_array[$wo["pro_packages"]["ultima"]["time"]];
}
if (in_array($wo["pro_packages"]["vip"]["time"], array_keys($time_array))) {
    $vip_package_duration = $time_array[$wo["pro_packages"]["vip"]["time"]];
}
// $seconds_in_day = (60*60*24);
// $star_package_duration   = $seconds_in_day * $wo['pro_packages']['star']['time']; // week in seconds
// $hot_package_duration    = $seconds_in_day * $wo['pro_packages']['hot']['time']; // month in seconds
// $ultima_package_duration = $seconds_in_day * $wo['pro_packages']['ultima']['time']; // year in seconds
// $vip_package_duration    = $seconds_in_day * $wo['pro_packages']['vip']['time']; // life time package
try {
    $wo["genders"]             = Wo_GetGenders($wo["language"], $langs);
    $wo["page_categories"]     = Wo_GetCategories(T_PAGES_CATEGORY);
    $wo["group_categories"]    = Wo_GetCategories(T_GROUPS_CATEGORY);
    $wo["blog_categories"]     = Wo_GetCategories(T_BLOGS_CATEGORY);
    $wo["products_categories"] = Wo_GetCategories(T_PRODUCTS_CATEGORY);
    $wo["job_categories"]      = Wo_GetCategories(T_JOB_CATEGORY);
    $wo["reactions_types"]     = Wo_GetReactionsTypes();
}
catch (Exception $e) {
    $wo["genders"]             = array();
    $wo["page_categories"]     = array();
    $wo["group_categories"]    = array();
    $wo["blog_categories"]     = array();
    $wo["products_categories"] = array();
    $wo["job_categories"]      = array();
    $wo["reactions_types"]     = array();
}
Wo_GetSubCategories();
$wo["config"]["currency_array"]        = (array) json_decode($wo["config"]["currency_array"]);
$wo["config"]["currency_symbol_array"] = (array) json_decode($wo["config"]["currency_symbol_array"]);
$wo["config"]["providers_array"]       = (array) json_decode($wo["config"]["providers_array"]);
if (!empty($wo["config"]["exchange"])) {
    $wo["config"]["exchange"] = (array) json_decode($wo["config"]["exchange"]);
}
$wo["currencies"] = array();
foreach ($wo["config"]["currency_symbol_array"] as $key => $value) {
    $wo["currencies"][] = array(
        "text" => $key,
        "symbol" => $value
    );
}
if (!empty($_GET["theme"])) {
    Wo_CleanCache();
}
$wo["post_colors"] = array();
if ($wo["config"]["colored_posts_system"] == 1) {
    $wo["post_colors"] = Wo_GetAllColors();
}
if ($wo["loggedin"] == true && $wo["user"]["is_pro"]) {
    $notify = false;
    $remove = false;
    if ($wo["config"]["pro"]) {
        switch ($wo["user"]["pro_type"]) {
            case "1":
                if ($wo["pro_packages"]["star"]["time"] != "unlimited") {
                    $end_time = $wo["user"]["pro_time"] + $star_package_duration;
                    if ($end_time > time() && $end_time <= time() + 60 * 60 * 24 * 3) {
                        $notify = true;
                    } elseif ($end_time <= time()) {
                        $remove = true;
                    }
                }
                break;
            case "2":
                if ($wo["pro_packages"]["hot"]["time"] != "unlimited") {
                    $end_time = $wo["user"]["pro_time"] + $hot_package_duration;
                    if ($end_time > time() && $end_time <= time() + 60 * 60 * 24 * 3) {
                        $notify = true;
                    } elseif ($end_time <= time()) {
                        $remove = true;
                    }
                }
                break;
            case "3":
                if ($wo["pro_packages"]["ultima"]["time"] != "unlimited") {
                    $end_time = $wo["user"]["pro_time"] + $ultima_package_duration;
                    if ($end_time > time() && $end_time <= time() + 60 * 60 * 24 * 3) {
                        $notify = true;
                    } elseif ($end_time <= time()) {
                        $remove = true;
                    }
                }
                break;
            case "4":
                if ($wo["pro_packages"]["vip"]["time"] != "unlimited") {
                    $end_time = $wo["user"]["pro_time"] + $vip_package_duration;
                    if ($end_time > time() && $end_time <= time() + 60 * 60 * 24 * 3) {
                        $notify = true;
                    } elseif ($end_time <= time()) {
                        $remove = true;
                    }
                }
                break;
        }
    }
    if ($notify == true) {
        $start     = date_create(date("Y-m-d H:i:s", time()));
        $end       = date_create(date("Y-m-d H:i:s", $end_time));
        $diff      = date_diff($end, $start);
        $left_time = "";
        if (!empty($diff->d)) {
            $left_time = $diff->d . " " . $wo["lang"]["day"];
        } elseif (!empty($diff->h)) {
            $left_time = $diff->h . " " . $wo["lang"]["hour"];
        } elseif (!empty($diff->i)) {
            $left_time = $diff->i . " " . $wo["lang"]["minute"];
        }
        $day       = date("d");
        $month     = date("n");
        $year      = date("Y");
        $query_one = " SELECT COUNT(*) AS count FROM " . T_NOTIFICATION . " WHERE `recipient_id` = " . $wo["user"]["id"] . " AND DAY(FROM_UNIXTIME(time)) = '{$day}' AND MONTH(FROM_UNIXTIME(time)) = '{$month}' AND YEAR(FROM_UNIXTIME(time)) = '{$year}' AND `type` = 'remaining'";
        $query     = mysqli_query($sqlConnect, $query_one);
        if ($query) {
            $fetched_data = mysqli_fetch_assoc($query);
            if ($fetched_data["count"] < 1) {
                $db->insert(T_NOTIFICATION, array(
                    "recipient_id" => $wo["user"]["id"],
                    "type" => "remaining",
                    "text" => str_replace("{{time}}", $left_time, $wo["lang"]["remaining_text"]),
                    "url" => "index.php?link1=home",
                    "time" => time()
                ));
            }
        }
    }
    if ($remove == true) {
        $update      = Wo_UpdateUserData($wo["user"]["id"], array(
            "is_pro" => 0
        ));
        $user_id     = $wo["user"]["id"];
        $mysql_query = mysqli_query($sqlConnect, "UPDATE " . T_PAGES . " SET `boosted` = '0' WHERE `user_id` = {$user_id}");
        $mysql_query = mysqli_query($sqlConnect, "UPDATE " . T_POSTS . " SET `boosted` = '0' WHERE `user_id` = {$user_id}");
        $mysql_query = mysqli_query($sqlConnect, "UPDATE " . T_POSTS . " SET `boosted` = '0' WHERE `page_id` IN (SELECT `page_id` FROM " . T_PAGES . " WHERE `user_id` = {$user_id})");
    }
}
