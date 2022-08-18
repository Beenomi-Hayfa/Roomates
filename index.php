<?php
// +--------------------------------------\----------------------------------+
// | @author Deen Doughouz (DoughouzForest)
// | @author_url 1: http://www.wowonder.com
// | @author_url 2: http://codecanyon.net/user/doughouzforest
// | @author_email: wowondersocial@gmail.com
// +------------------------------------------------------------------------+
// | WoWonder - The Ultimate Social Networking Platform
// | Copyright (c) 2017 WoWonder. All rights reserved.
// +------------------------------------------------------------------------+
require_once('assets/init.php');
if ($wo['loggedin'] == true) {
    $update_last_seen = Wo_LastSeen($wo['user']['user_id']);
} else if (!empty($_SERVER['HTTP_HOST'])) {
}
if (!empty($_GET)) {
    foreach ($_GET as $key => $value) {
        if (!is_array($value)) {
            $value      = preg_replace('/on[^<>=]+=[^<>]*/m', '', $value);
            $value      = preg_replace('/\((.*?)\)/m', '', $value);
            $_GET[$key] = strip_tags($value);
        }
    }
}
if (!empty($_REQUEST)) {
    foreach ($_REQUEST as $key => $value) {
        if (!is_array($value)) {
            $value          = preg_replace('/on[^<>=]+=[^<>]*/m', '', $value);
            $_REQUEST[$key] = strip_tags($value);
        }
    }
}
if (!empty($_POST)) {
    foreach ($_POST as $key => $value) {
        if (!is_array($value)) {
            $value       = preg_replace('/on[^<>=]+=[^<>]*/m', '', $value);
            $_POST[$key] = strip_tags($value);
        }
    }
}
if (!empty($_GET['ref']) && $wo['loggedin'] == false && !isset($_COOKIE['src'])) {
    $get_ip = get_ip_address();
    if (!isset($_SESSION['ref']) && !empty($get_ip)) {
        $_GET['ref'] = Wo_Secure($_GET['ref']);
        $ref_user_id = Wo_UserIdFromUsername($_GET['ref']);
        $user_date   = Wo_UserData($ref_user_id);
        if (!empty($user_date)) {
            if (ip_in_range($user_date['ip_address'], '/24') === false && $user_date['ip_address'] != $get_ip) {
                $_SESSION['ref'] = $user_date['username'];
            }
        }
    }
}
if (!isset($_COOKIE['src'])) {
    @setcookie('src', '1', time() + 31556926, '/');
}
$page = '';
if ($wo['loggedin'] == true && !isset($_GET['link1'])) {
    $page = 'home';
} elseif (isset($_GET['link1'])) {
    $page = $_GET['link1'];
}
if ((!isset($_GET['link1']) && $wo['loggedin'] == false) || (isset($_GET['link1']) && $wo['loggedin'] == false && $page == 'home')) {
    $page = 'welcome';
}
if ($wo['config']['maintenance_mode'] == 1) {
    if ($wo['loggedin'] == false) {
        if ($page == 'admincp' || $page == 'admin-cp') {
            $page = 'welcome';
        } else {
            $page = 'maintenance';
        }
    } else {
        if (Wo_IsAdmin() === false) {
            $page = 'maintenance';
        }
    }
}
if (!empty($_GET['m'])) {
    $page = 'welcome';
}
if ((!$wo['loggedin'] || ($wo['loggedin'] && $wo['user']['banned'] != 1))) {
    if ($wo['config']['membership_system'] == 1) {
        if ($wo['loggedin'] == true) {
            if ($wo['user']['is_pro'] != 0 || Wo_IsAdmin()) {
                switch ($page) {
                    case 'maintenance':
                        include('sources/maintenance.php');
                        break;
                    case 'get_news_feed':
                        include('sources/get_news_feed.php');
                        break;
                    case 'video-call':
                        include('sources/video.php');
                        break;
                    case 'video-call-api':
                        include('sources/video_call_api.php');
                        break;
                    case 'home':
                        include('sources/home.php');
                        break;
                    case 'welcome':
                        include('sources/welcome.php');
                        break;
                    case 'register':
                        include('sources/register.php');
                        break;
                    case 'confirm-sms':
                        include('sources/confirm_sms.php');
                        break;
                    case 'confirm-sms-password':
                        include('sources/confirm_sms_password.php');
                        break;
                    case 'forgot-password':
                        include('sources/forgot_password.php');
                        break;
                    case 'reset-password':
                        include('sources/reset_password.php');
                        break;
                    case 'start-up':
                        include('sources/start_up.php');
                        break;
                    case 'activate':
                        include('sources/activate.php');
                        break;
                    case 'search':
                        include('sources/search.php');
                        break;
                    case 'timeline':
                        include('sources/timeline.php');
                        break;
                    case 'pages':
                        include('sources/my_pages.php');
                        break;
                    case 'suggested-pages':
                        include('sources/suggested_pages.php');
                        break;
                    case 'liked-pages':
                        include('sources/liked_pages.php');
                        break;
                    case 'joined_groups':
                        include('sources/joined_groups.php');
                        break;
                    case 'go-pro':
                        include('sources/go_pro.php');
                        break;
                    case 'page':
                        include('sources/page.php');
                        break;


                    case 'most_liked':
                        include('sources/most_liked.php');
                        break;
                    case 'groups':
                        include('sources/my_groups.php');
                        break;
                    case 'suggested-groups':
                        include('sources/suggested_groups.php');
                        break;
                    case 'group':
                        include('sources/group.php');
                        break;

                    case 'create-group':
                        if (Wo_IsAdmin()) {
                        include('sources/create_group.php');
                        }
                        break;
                    case 'group-setting':
                        include('sources/group_setting.php');
                        break;
                    case 'create-page':
                        include('sources/create_page.php');
                        break;
                    case 'setting':
                        include('sources/setting.php');
                        break;
                    case 'page-setting':
                        include('sources/page_setting.php');
                        break;
                    case 'messages':
                        include('sources/messages.php');
                        break;
                    case 'logout':
                        include('sources/logout.php');
                        break;
                    case '404':
                        include('sources/404.php');
                        break;
                        
                    case 'post':
                        include('sources/story.php');
                        break;


                    case 'saved-posts':
                        include('sources/savedPosts.php');
                        break;
                    case 'hashtag':
                        include('sources/hashtag.php');
                        break;
                    case 'terms':
                        include('sources/term.php');
                        break;
                   
                    
                    case 'contact-us':
                        include('sources/contact.php');
                        break;
                    case 'user-activation':
                        include('sources/user_activation.php');
                        break;
                    case 'upgraded':
                        include('sources/upgraded.php');
                        break;
                    case 'oops':
                        include('sources/oops.php');
                        break;
                    case 'boosted-pages':
                        include('sources/boosted_pages.php');
                        break;
                    case 'boosted-posts':
                        include('sources/boosted_posts.php');
                        break;
                    case 'new-product':
                        include('sources/new_product.php');
                        break;
                    case 'edit-product':
                        include('sources/edit_product.php');
                        break;
                    case 'products':
                        include('sources/products.php');
                        break;
                    case 'my-products':
                        include('sources/my_products.php');
                        break;
                    case 'site-pages':
                        include('sources/site_pages.php');
                        break;
                   
                    case 'furniture-products':
                        include('sources/furniture_products.php');
                        break;
                    


                    case 'oauth':
                        include('sources/oauth.php');
                        break;
                    case 'app_api':
                        include('sources/apps_api.php');
                        break;
                    case 'authorize':
                        include('sources/authorize.php');
                        break;
                    case 'app-setting':
                        include('sources/app_setting.php');
                        break;
                    case 'developers':
                        include('sources/developers.php');
                        break;
                    case 'create-app':
                        include('sources/create_app.php');
                        break;
                    case 'app':
                        include('sources/app_page.php');
                        break;
                    case 'apps':
                        include('sources/apps.php');
                        break;
                    case 'sharer':
                        include('sources/sharer.php');
                        break;

                   

                    case 'advertise':
                        include('sources/ads/ads.php');
                        break;
                    case 'wallet':
                        include('sources/ads/wallet.php');
                        break;
                    case 'send_money':
                        include('sources/ads/send_money.php');
                        break;
                    case 'create-ads':
                        include('sources/ads/create_ads.php');
                        break;
                    case 'edit-ads':
                        include('sources/ads/edit_ads.php');
                        break;
                    case 'chart-ads':
                        include('sources/ads/chart_ads.php');
                        break;
                    case 'manage-ads':
                        include('sources/ads/admin.php');
                        break;
                    case 'create-status':
                        include('sources/status/create.php');
                        break;
                    case 'friends-nearby':
                        include('sources/friends_nearby.php');
                        break;
                    case 'more-status':
                        include('sources/status/more-status.php');
                        break;
                    case 'unusual-login':
                        include('sources/unusual-login.php');
                        break;
                    case 'jobs':
                        include('sources/jobs.php');
                        break;
                    case 'common_things':
                        include('sources/common_things.php');
                        break;

                  
                   
                    case 'create_funding':
                        include('sources/create_funding.php');
                        break;

                   

                    case 'edit_fund':
                        include('sources/edit_fund.php');
                        break;
                    case 'show_fund':
                        include('sources/show_fund.php');
                        break;

                  

                    case 'refund':
                        include('sources/refund.php');
                        break;

                   

                    case 'nearby_shops':
                        include('sources/nearby_shops.php');
                        break;
                    case 'nearby_business':
                        include('sources/nearby_business.php');
                        break;
                    case 'live':
                        include('sources/live.php');
                        break;
                    case 'checkout':
                        include('sources/checkout.php');
                        break;
                    case 'purchased':
                        include('sources/purchased.php');
                        break;
                    case 'customer_order':
                        include('sources/customer_order.php');
                        break;
                    case 'orders':
                        include('sources/orders.php');
                        break;
                    case 'order':
                        include('sources/order.php');
                        break;
                    case 'reviews':
                        include('sources/reviews.php');
                        break;
                    case 'open_to_work_posts':
                        include('sources/open_to_work_posts.php');
                        break;
                    case 'withdrawal':
                        include('sources/withdrawal.php');
                        break;
                }
            } else {
                switch ($page) {
                    case 'maintenance':
                        include('sources/maintenance.php');
                        break;
                    case 'go-pro':
                        include('sources/go_pro.php');
                        break;
                    case 'welcome':
                        include('sources/welcome.php');
                        break;
                    case 'register':
                        include('sources/register.php');
                        break;
                    case 'confirm-sms':
                        include('sources/confirm_sms.php');
                        break;
                    case 'confirm-sms-password':
                        include('sources/confirm_sms_password.php');
                        break;
                    case 'forgot-password':
                        include('sources/forgot_password.php');
                        break;
                    case 'reset-password':
                        include('sources/reset_password.php');
                        break;
                    case 'activate':
                        include('sources/activate.php');
                        break;
                    case 'logout':
                        include('sources/logout.php');
                        break;
                    case '404':
                        include('sources/404.php');
                        break;
                    case 'contact-us':
                        include('sources/contact.php');
                        break;
                    case 'user-activation':
                        include('sources/user_activation.php');
                        break;
                    case 'upgraded':
                        include('sources/upgraded.php');
                        break;
                    case 'oops':
                        include('sources/oops.php');
                        break;
                    case 'oauth':
                        include('sources/oauth.php');
                        break;
                    case 'app_api':
                        include('sources/apps_api.php');
                        break;
                    case 'authorize':
                        include('sources/authorize.php');
                        break;
                    case 'app-setting':
                        include('sources/app_setting.php');
                        break;
                    case 'developers':
                        include('sources/developers.php');
                        break;
                    case 'create-app':
                        include('sources/create_app.php');
                        break;
                    case 'app':
                        include('sources/app_page.php');
                        break;
                    case 'apps':
                        include('sources/apps.php');
                        break;
                    case 'unusual-login':
                        include('sources/unusual-login.php');
                        break;
                    case 'terms':
                        include('sources/term.php');
                        break;
                    case 'site-pages':
                        include('sources/site_pages.php');
                        break;
                }
            }
        } else {
            switch ($page) {
                case 'maintenance':
                    include('sources/maintenance.php');
                    break;
                case 'welcome':
                    include('sources/welcome.php');
                    break;
                case 'register':
                    include('sources/register.php');
                    break;
                case 'confirm-sms':
                    include('sources/confirm_sms.php');
                    break;
                case 'confirm-sms-password':
                    include('sources/confirm_sms_password.php');
                    break;
                case 'forgot-password':
                    include('sources/forgot_password.php');
                    break;
                case 'reset-password':
                    include('sources/reset_password.php');
                    break;
                case 'activate':
                    include('sources/activate.php');
                    break;
                case 'logout':
                    include('sources/logout.php');
                    break;
                case '404':
                    include('sources/404.php');
                    break;
                case 'contact-us':
                    include('sources/contact.php');
                    break;
                case 'user-activation':
                    include('sources/user_activation.php');
                    break;
                case 'upgraded':
                    include('sources/upgraded.php');
                    break;
                case 'oops':
                    include('sources/oops.php');
                    break;
                case 'oauth':
                    include('sources/oauth.php');
                    break;
                case 'app_api':
                    include('sources/apps_api.php');
                    break;
                case 'authorize':
                    include('sources/authorize.php');
                    break;
                case 'app-setting':
                    include('sources/app_setting.php');
                    break;
                case 'developers':
                    include('sources/developers.php');
                    break;
                case 'create-app':
                    include('sources/create_app.php');
                    break;
                case 'app':
                    include('sources/app_page.php');
                    break;
                case 'apps':
                    include('sources/apps.php');
                    break;
                case 'unusual-login':
                    include('sources/unusual-login.php');
                    break;
                case 'terms':
                    include('sources/term.php');
                    break;
            }
        }
    } else {
        switch ($page) {
            case 'maintenance':
                include('sources/maintenance.php');
                break;
            case 'get_news_feed':
                include('sources/get_news_feed.php');
                break;
            case 'video-call':
                include('sources/video.php');
                break;
            case 'video-call-api':
                include('sources/video_call_api.php');
                break;
            case 'home':
                include('sources/home.php');
                break;
            case 'welcome':
                include('sources/welcome.php');
                break;
            case 'register':
                include('sources/register.php');
                break;
            case 'confirm-sms':
                include('sources/confirm_sms.php');
                break;
            case 'confirm-sms-password':
                include('sources/confirm_sms_password.php');
                break;
            case 'forgot-password':
                include('sources/forgot_password.php');
                break;
            case 'reset-password':
                include('sources/reset_password.php');
                break;
            case 'start-up':
                include('sources/start_up.php');
                break;
            case 'activate':
                include('sources/activate.php');
                break;
            case 'search':
                include('sources/search.php');
                break;
            case 'timeline':
                include('sources/timeline.php');
                break;
            case 'pages':
                include('sources/my_pages.php');
                break;
            case 'suggested-pages':
                include('sources/suggested_pages.php');
                break;
            case 'liked-pages':
                include('sources/liked_pages.php');
                break;
            case 'joined_groups':
                include('sources/joined_groups.php');
                break;
            case 'go-pro':
                include('sources/go_pro.php');
                break;
            case 'page':
                include('sources/page.php');
                break;
           
            case 'most_liked':
                include('sources/most_liked.php');
                break;
            case 'groups':
                include('sources/my_groups.php');
                break;
            case 'suggested-groups':
                include('sources/suggested_groups.php');
                break;
            case 'group':
                include('sources/group.php');
                break;
            case 'create-group':
                if (Wo_IsAdmin()) {
                    include('sources/create_group.php');
                    }
                break;
            case 'group-setting':
                include('sources/group_setting.php');
                break;
            case 'create-page':
                include('sources/create_page.php');
                break;
            case 'setting':
                include('sources/setting.php');
                break;
            case 'page-setting':
                include('sources/page_setting.php');
                break;
            case 'messages':
                include('sources/messages.php');
                break;
            case 'logout':
                include('sources/logout.php');
                break;
            case '404':
                include('sources/404.php');
                break;
            case 'post':
                include('sources/story.php');
                break;
           
            case 'saved-posts':
                include('sources/savedPosts.php');
                break;
            case 'hashtag':
                include('sources/hashtag.php');
                break;
            case 'terms':
                include('sources/term.php');
                break;
            
           
           
            case 'contact-us':
                include('sources/contact.php');
                break;
            case 'user-activation':
                include('sources/user_activation.php');
                break;
            case 'upgraded':
                include('sources/upgraded.php');
                break;
            case 'oops':
                include('sources/oops.php');
                break;
            case 'boosted-pages':
                include('sources/boosted_pages.php');
                break;
            case 'boosted-posts':
                include('sources/boosted_posts.php');
                break;
            case 'new-product':
                include('sources/new_product.php');
                break;
            case 'edit-product':
                include('sources/edit_product.php');
                break;
            case 'products':
                include('sources/products.php');
                break;
            case 'my-products':
                include('sources/my_products.php');
                break;
            case 'site-pages':
                include('sources/site_pages.php');
                break;
          
           

            

            case 'oauth':
                include('sources/oauth.php');
                break;
            case 'app_api':
                include('sources/apps_api.php');
                break;
            case 'authorize':
                include('sources/authorize.php');
                break;
            case 'app-setting':
                include('sources/app_setting.php');
                break;
            case 'developers':
                include('sources/developers.php');
                break;
            case 'create-app':
                include('sources/create_app.php');
                break;
            case 'app':
                include('sources/app_page.php');
                break;
            case 'apps':
                include('sources/apps.php');
                break;
            case 'sharer':
                include('sources/sharer.php');
                break;
         
            case 'advertise':
                include('sources/ads/ads.php');
                break;
            case 'wallet':
                include('sources/ads/wallet.php');
                break;
            case 'send_money':
                include('sources/ads/send_money.php');
                break;
            case 'create-ads':
                include('sources/ads/create_ads.php');
                break;
            case 'edit-ads':
                include('sources/ads/edit_ads.php');
                break;
            case 'chart-ads':
                include('sources/ads/chart_ads.php');
                break;
            case 'manage-ads':
                include('sources/ads/admin.php');
                break;
            case 'create-status':
                include('sources/status/create.php');
                break;
            case 'friends-nearby':
                include('sources/friends_nearby.php');
                break;
            case 'more-status':
                include('sources/status/more-status.php');
                break;
            case 'unusual-login':
                include('sources/unusual-login.php');
                break;
            case 'jobs':
                include('sources/jobs.php');
                break;
            case 'common_things':
                include('sources/common_things.php');
                break;


            case 'edit_fund':
                include('sources/edit_fund.php');
                break;
            case 'show_fund':
                include('sources/show_fund.php');
                break;
         
            case 'refund':
                include('sources/refund.php');
                break;
         
            case 'nearby_shops':
                include('sources/nearby_shops.php');
                break;
            case 'nearby_business':
                include('sources/nearby_business.php');
                break;
            case 'live':
                include('sources/live.php');
                break;
            case 'checkout':
                include('sources/checkout.php');
                break;
            case 'purchased':
                include('sources/purchased.php');
                break;
            case 'customer_order':
                include('sources/customer_order.php');
                break;
            case 'orders':
                include('sources/orders.php');
                break;
            case 'order':
                include('sources/order.php');
                break;
            case 'reviews':
                include('sources/reviews.php');
                break;
            case 'open_to_work_posts':
                include('sources/open_to_work_posts.php');
                break;
            case 'withdrawal':
                include('sources/withdrawal.php');
                break;
        }
    }
} else {
    switch ($page) {
        case 'maintenance':
            include('sources/maintenance.php');
            break;
        case 'welcome':
            include('sources/welcome.php');
            break;
        case 'register':
            include('sources/register.php');
            break;
        case 'confirm-sms':
            include('sources/confirm_sms.php');
            break;
        case 'confirm-sms-password':
            include('sources/confirm_sms_password.php');
            break;
        case 'forgot-password':
            include('sources/forgot_password.php');
            break;
        case 'reset-password':
            include('sources/reset_password.php');
            break;
        case 'activate':
            include('sources/activate.php');
            break;
        case 'logout':
            include('sources/logout.php');
            break;
        case '404':
            include('sources/404.php');
            break;
        case 'contact-us':
            include('sources/contact.php');
            break;
        case 'user-activation':
            include('sources/user_activation.php');
            break;
        case 'oops':
            include('sources/oops.php');
            break;
        case 'unusual-login':
            include('sources/unusual-login.php');
            break;
        case 'banned':
            include('sources/banned.php');
            break;
        case 'home':
            include('sources/banned.php');
            break;
        default:
            include('sources/banned.php');
            break;
    }
}
if (empty($wo['content'])) {
    if ($wo['config']['membership_system'] == 1 && $wo['loggedin'] == true) {
        include('sources/go_pro.php');
    } else {
        include('sources/404.php');
    }
}
echo Wo_Loadpage('container');
mysqli_close($sqlConnect);
unset($wo);
?>
