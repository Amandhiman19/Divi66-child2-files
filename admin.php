<?php
/*
 * Template Name: Admin Users
 * Template Post Type: page
 */
require_once( $_SERVER['DOCUMENT_ROOT'] . '/ACCGQ/wp-config.php' );
global $wpdb;
function getAllTransactionsByIdNew($userId, $limit = 1000){
		$transactions = [];
		if (!$userId || $userId == '') return $transactions;
		global $wpdb;
		$table_name = $wpdb->prefix . 'mepr_transactions';
		$sql = "SELECT * FROM $table_name WHERE user_id = $userId ORDER BY id DESC LIMIT $limit";
		return $wpdb->get_results($sql);
}

if(isset($_POST["export-teams"])) {
  //exportAllTeams($teams);
	global $wpdb;
	$args = array(
        'post_type' => 'teams',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'title',
        'order' => 'ASC'
    );
	$teams = get_posts($args);
    $teamArray = array();
		$counter =1; 
		foreach($teams as $value){
			$teamArray[$counter]['id'] =$value->ID;
			$teamArray[$counter]['post_title'] =$value->post_title;
			$teamArray[$counter]['number_of_the_canoe'] = get_post_meta( $value->ID, 'number_of_the_canoe', true );
			$counter = $counter + 1;
		}
	include_once($_SERVER['DOCUMENT_ROOT']."/ACCGQ/wp-content/themes/Divi66-child2/PHPXLSXWritermaster/xlsxwriter.class.php");
	ini_set('display_errors', 0);
	ini_set('log_errors', 1);
	error_reporting(E_ALL & ~E_NOTICE);

	$filename = "teams_".date("Y-m-d H:i:s").".xlsx";
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');

	$Throws = array(array('ID','Team Name','Number Of Canoe'));
    $writer = new XLSXWriter();
	$writer->setAuthor('Some Author'); 
	$styles1 = array( 'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');
	foreach($Throws as $throw){
		$writer->writeSheetRow('Sheet1', $throw,$styles1);
	}
	foreach($teamArray as $row){
		$writer->writeSheetRow('Sheet1', $row);
	}	
	$writer->writeToStdOut();
	exit(0);
	
}
if (isset($_POST["export-users"])) {
	//exportAllUsers($users);
	global $wpdb;
	$Sqlusers ="SELECT * FROM wp_users";
	$users = $wpdb->get_results($Sqlusers);
	$userArray = array();
	$counter =1; 
	foreach($users as $value){
		$user_id = $value->ID;
		$table_name = $wpdb->prefix . 'mepr_transactions';
		$sqltranscation = "SELECT * FROM $table_name WHERE user_id = $user_id ORDER BY id DESC LIMIT 1";
		$gettranscation  = $wpdb->get_results($sqltranscation);
		
		$userArray[$user_id]['ID'] =$counter;
		$userArray[$user_id]['first_name'] = get_user_meta( $user_id, 'first_name', true );
		$userArray[$user_id]['last_name'] = get_user_meta( $user_id, 'last_name', true );
		$userArray[$user_id]['user_email'] =$value->user_email;
		
		$userArray[$user_id]['sex'] = get_user_meta( $user_id, 'mepr_sexe', true );
		
        

        $training_done = get_user_meta( $user_id, 'mepr_training_done', true );
        $practical_training = get_user_meta( $user_id, 'mepr_practical_training', true );

        if(!empty($training_done)){ $training_doneV = "Yes";}else{ $training_doneV = "No" ;}
        if(!empty($practical_training)){ $practical_trainingV = "Yes";}else{ $practical_trainingV = "No" ;}

        $userArray[$user_id]['training_done'] = $training_doneV;
        $userArray[$user_id]['practical_training'] = $practical_trainingV;

		if(count($gettranscation)>0){$member ="Yes";}else{$member ='No';}
		if(!empty($gettranscation[0]->created_at)){$created_at =$gettranscation[0]->created_at;
		}else{$created_at ='';}
		$userArray[$user_id]['member'] = $member; 
		$userArray[$user_id]['last_payment'] = $created_at;
		$userArray[$user_id]['city'] = get_user_meta( $user_id, 'mepr-address-city', true );
		$associate_team= get_user_meta( $user_id, 'mepr_associate_team', true );

        $table_name = $wpdb->prefix . 'posts';
        $sqlposts = "SELECT * FROM $table_name WHERE ID = $associate_team ORDER BY id DESC LIMIT 1";
        $getpost  = $wpdb->get_results($sqlposts);
        $userArray[$user_id]['associate_team'] = $getpost[0]->post_title;
        //echo "<pre>"; print_r($userArray); die;

        //if(!empty($associate_team)){ $associate_team_v = "Yes";}else{ $associate_team_v = "No" ;}
        //$userArray[$user_id]['associate_team'] = $associate_team_v;
		$counter = $counter + 1;
	}
	
		
	include_once($_SERVER['DOCUMENT_ROOT']."/ACCGQ/wp-content/themes/Divi66-child2/PHPXLSXWritermaster/xlsxwriter.class.php");
	ini_set('display_errors', 0);
	ini_set('log_errors', 1);
	error_reporting(E_ALL & ~E_NOTICE);

	$filename = "users_".date("Y-m-d H:i:s").".xlsx";
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
    
   
	$Throws = array(array('ID','First Name' ,'Last Name' ,'Email' ,'Sex' ,'Theoric Training','Practical Training','Member','Last Payment','City','Associate Team'));
    $writer = new XLSXWriter();
	$writer->setAuthor('Some Author'); 
	$styles1 = array( 'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');
	foreach($Throws as $throw){
		$writer->writeSheetRow('Sheet1', $throw,$styles1);
	}
	foreach($userArray as $row){
		$writer->writeSheetRow('Sheet1', $row);
	}	
	$writer->writeToStdOut();
	exit(0);
}
if (!isCurrentUserAdmin() && !isCurrentUserTrainer() && !isCurrentUserPromoter()) {
	// redirect to home page if user is not admin
    wp_redirect(home_url() . '/my-account/my-profile');
    exit;
}

get_header();

$profileTabs = [
    'users'   => __('All Users', 'divi-child'),
    'teams'  => __('All Teams', 'divi-child'),
];

$users = get_users();
$teams = getAllTeams();

/*if (isset($_POST["export-users"])) {
	
    exportAllUsers($users);
}*/



?>
<div id="main-content">
    <div class="container">
        <div id="content-area" class="clearfix">
            <h1 class="main_title"><?php the_title(); ?></h1>
        </div>
        <div class="account-area profile-contente">
            <!-- tabs section here -->
            <?php
            // profile
            $activeTab = 'admin';
            include_once get_stylesheet_directory() . '/my-account/tabs.php';
            ?>

            <!-- content here -->
            <div class="profile-area">
                <div class="entry-content mb-10">
                    <div class="tabs">
                        <ul class="flex">
                            <?php
                            foreach ($profileTabs as $tab => $title) {
                                echo '<li class="' . $tab . '" data-tab="' . $tab . '">' . $title . '</li>';
                            }
                            ?>
                        </ul>

                        <script type="module">
                            jQuery(function($) {
                                // check params
                                let params = getParams('tab');
                                const availbale = ['users', 'teams'];
                                if (!availbale.includes(params)) {
                                    setParams('tab', 'users');
                                    params = 'users';
                                }

                                $('.tabs ul li').removeClass('active');
                                $('.tabs ul li.' + params).addClass('active');
                                var tab_id = $('.tabs ul li.' + params).attr('data-tab');
                                jQuery('.tabs').find('.admin-tab').removeClass('active');
                                jQuery('#' + tab_id).addClass('active');
                                $('.tab-area .tab-content[data-tab="' + params.tab + '"]').addClass('active');

                                jQuery('.tabs ul li').click(function(e) {
                                    e.preventDefault();
                                    jQuery('.tabs ul li').removeClass('active');
                                    jQuery(this).addClass('active');
                                    var tab_id = jQuery(this).attr('data-tab');
                                    jQuery('.tabs').find('.admin-tab').removeClass('active');
                                    jQuery('#' + tab_id).addClass('active');
                                    setParams('tab', tab_id);
                                });
                            });
                        </script>


                        <!-- list of users -->
                        <div id="users" class="admin-tab">
                            <form action="" method="post" id="ExportForm">
                                <button  type="submit" id="btnExport" name='export-users' value="Export Users" class="btn btn-primary export-users">
                                    <?php echo esc_html__('Export Users', 'divi-child'); ?>
                                </button>
							</form>
                            
                            <?php

                            if (count($users) > 0) { ?>
                                <div class="user-lists">
                                    <table id="all-users">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th class="order"><?php echo esc_html__("User Name", 'divi-child'); ?></th>
                                                <th class="order"><?php echo esc_html__("User Email", 'divi-child'); ?></th>
                                                <th class="order"><?php echo esc_html__("User Role", 'divi-child'); ?></th>
                                                <?php

                                                if (isCurrentUserPromoter()) { ?>
                                                    <th class="order"><?php echo esc_html__("Is training Done?", 'divi-child'); ?></th>
                                                <?php } else { ?>
                                                    <th class="order"><?php echo esc_html__("Theoric training", 'divi-child'); ?></th>
                                                    <th class="order"><?php echo esc_html__("Practical training", 'divi-child'); ?></th>
                                                <?php } ?>
                                                <th class="order"><?php echo esc_html__("Last Payment", 'divi-child'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($users as $key => $user) {
                                                $getTransaction = getAllTransactionsById($user->ID, 1);
                                                $training_done = get_user_meta($user->ID, 'mepr_training_done', true);
                                                $pr_training_done = get_user_meta($user->ID, 'mepr_practical_training', true);
                                            ?>

                                                <tr class="user mb-10">
                                                    <td class="user-id">
                                                        <?php echo $key + 1; ?>
                                                    </td>

                                                    <td class="user-name">
                                                        <?php echo $user->display_name; ?>
                                                    </td>
                                                    <td class="user-email">
                                                        <?php echo $user->user_email; ?>
                                                    </td>
                                                    <td class="Mmmber">
                                                        <?php
                                                        if (!empty($getTransaction)) {
                                                            echo esc_html__('Yes', 'divi-child');
                                                        } else {
                                                            echo esc_html__('No', 'divi-child');
                                                        }
                                                        ?>
                                                    </td>

                                                    <?php
                                                    if (isCurrentUserPromoter()) { ?>
                                                        <td class="thr-training-done" title="If you change it will auto update">
                                                            <?php echo $training_done && $pr_training_done ? esc_html__('Yes', 'divi-child') : esc_html__('No', 'divi-child'); ?>
                                                        </td>
                                                    <?php
                                                    } ?>

                                                    <?php
                                                    if (isCurrentUserAdmin() || isCurrentUserTrainer()) { ?>
                                                        <td class="training-done" title="If you change it will auto update">
                                                            <input type="checkbox" class="mepr_training_done" name="mepr_training_done" id="mepr_training_done-<?php echo $user->ID; ?>" <?php echo $training_done ? "checked=checked" : ""; ?> data-user-id="<?php echo $user->ID; ?>">
                                                            <?php echo $training_done ? "checked" : "unchecked"; ?>
                                                        </td>
                                                    <?php
                                                    } ?>
                                                    </td>
                                                    <?php
                                                    if (isCurrentUserAdmin() || isCurrentUserTrainer()) { ?>
                                                        <td class="training-done" title="If you change it will auto update">
                                                            <input type="checkbox" class="mepr_practical_training" name="mepr_practical_training" id="mepr_practical_training-<?php echo $user->ID; ?>" <?php echo $pr_training_done ? "checked=checked" : ""; ?> data-user-id="<?php echo $user->ID; ?>">
                                                            <?php echo $pr_training_done ? "checked" : "unchecked"; ?>
                                                        </td>
                                                    <?php } ?>

                                                    <td class="Mmmber">
                                                        <?php
                                                        if (!empty($getTransaction) > 0) {
                                                            echo $getTransaction[0]->created_at;
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php
                            } else {
                                echo '<p>' . esc_html__('No users found', 'divi-child') . '</p>';
                            }

                            ?>
                        </div>
                        <div id="teams" class="admin-tab">
						<form  action="" method="post">
                            <button  type="submit" id="btnExport" name='export-teams' value="Export teams" class="btn btn-primary export-teams">
                                    <?php echo esc_html__('Export Teams', 'divi-child'); ?>
                                </button>
								</form>
                          
                            <?php

                            if (count($teams) > 0) { ?>
                                <div class="user-lists">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th class="order"><?php echo esc_html__("Team Name", 'divi-child') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($teams as $key => $team) {
                                            ?>
                                                <tr class="team">
                                                    <td class="team-id">
                                                        <?php echo $key + 1; ?>
                                                    </td>

                                                    <td class="team-name">
                                                        <?php echo $team->post_title; ?>
                                                    </td>

                                                </tr>
                                            <?php
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php
                            } else {
                                echo '<p>' . esc_html__('No Team found', 'divi-child') . '</p>';
                            }

                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
jQuery(document).ready(function() {
	jQuery('body').on('click', '.export-teams', function() {
		H5_loading.show();
		setTimeout(function() {H5_loading.hide();}, 2000);
     });
	 jQuery('body').on('click', '.export-users', function() {
		H5_loading.show();
		setTimeout(function() { H5_loading.hide();}, 3000);
     });

});
</script>
<?php
get_footer();
