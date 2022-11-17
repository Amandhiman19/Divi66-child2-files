<?php
/*
 * Template Name: Subscrubed Users
 * Template Post Type: page
 */
 
require_once( $_SERVER['DOCUMENT_ROOT'] . '/ACCGQ/wp-config.php' );
global $wpdb;

function get_associate_race_by($userId1){
    $args = array(
        'numberposts'    => -1,
        'post_type'        => 'memberpressproduct',
        'meta_query'    => array(
            'relation'        => 'AND',
            array(
                'key'        => 'choose_promoter',
                'value'        => $userId1,
                'compare'    => 'LIKE'
            )
        )
    );
	$the_query = new WP_Query($args);
	return wp_list_pluck($the_query->posts, 'ID');
}
function isBothTrainingDoneByID($userId1){
	
    if (!$userId1) {  return false; }
	$trainingDone = get_user_meta($userId1, 'mepr_training_done', true);
    $practicalTraining = get_user_meta($userId1, 'mepr_practical_training', true);
	return $trainingDone && $practicalTraining;
}


if(isset($_POST["export-subscribed-lists"])) {
	 $user = wp_get_current_user();

	$subscribedArray = [];
	$args = array(
		'post_type' => 'racecircuit',
		'posts_per_page' => -1,
		'orderby' => 'title',
		'order' => 'ASC',
	);
	$team_args = array(
		'post_type' => 'teams',
		'posts_per_page' => -1,
		'orderby' => 'title',
		'order' => 'ASC',
	);
	$teamclasses = get_posts($args);
	foreach ($teamclasses as $teamclass) {
			$getTeamdata = get_field('team_class');
		
		}

	if (in_array('promoters', (array)$user->roles)) {
		$getRace = get_associate_race_by($user->ID);
		if (!$getRace) { $getRace = [0];}
		$args['meta_query']    = array(
			'relation'        => 'AND',
			array(
				'key'        => 'race_type',
				'value'        => $getRace,
				'compare'    => 'IN'
			),
		);
	}
	$racecircuits = get_posts($args);

	if ($racecircuits) {
		foreach ($racecircuits as $racecircuit) {
		   $getTeam = get_field('associate_team', $racecircuit->ID);
           //echo "<pre>"; print_r($getTeam['label']); die;
			$team_class = get_field('team_class', $racecircuit->ID);
		
			$avant_tribord = get_field('avant_tribord', $racecircuit->ID);
			$race_captain = get_field('race_captain', $racecircuit->ID);
			$avant_babord = get_field('avant_babord', $racecircuit->ID);
			$arriere_tribord = get_field('arriere_tribord', $racecircuit->ID);
			$arriere_babord = get_field('arriere_babord', $racecircuit->ID);
			$barreur = get_field('barreur', $racecircuit->ID);
			$race_type = get_field('race_type', $racecircuit->ID);
			
			$team_class = get_post( $getTeam['value'] )->team_class;
			$team_canoe = get_post( $getTeam['value'] )->canoe_list;
			$avant_tribordFormated = isBothTrainingDoneByID($avant_tribord['ID']);
			$avant_babordFormated = isBothTrainingDoneByID($avant_babord['ID']);
			$arriere_tribordFormated = isBothTrainingDoneByID($arriere_tribord['ID']);
			$arriere_babordFormated = isBothTrainingDoneByID($arriere_babord['ID']);
			$barreurFormated = isBothTrainingDoneByID($barreur['ID']);

            // $table_name = $wpdb->prefix . 'posts';
            // $sqlposts = "SELECT * FROM $table_name WHERE ID = $getTeam['value'] ORDER BY id DESC LIMIT 1";
            // $getpost  = $wpdb->get_results($sqlposts);
            // $associate_team = $getpost[0]->post_title;
            // echo "<pre>"; print_r($associate_team); die;
			$arrayFormated = array();
			if(!empty($avant_tribordFormated)){$arrayFormated[] ="Yes";}else{$arrayFormated[] ="NO";}
			if(!empty($avant_babordFormated)){$arrayFormated[] ="Yes";}else{$arrayFormated[] ="NO";}
			if(!empty($arriere_tribordFormated)){$arrayFormated[] ="Yes";}else{$arrayFormated[] ="NO";}
			if(!empty($arriere_babordFormated)){$arrayFormated[] ="Yes";}else{$arrayFormated[] ="NO";}
			if(!empty($barreurFormated)){$arrayFormated[] ="Yes";}else{$arrayFormated[] ="NO";}
			 $arrayFormatedValue = implode(",",$arrayFormated);
			$subscribedArray[] = [
				'id' =>  $racecircuit->ID,
				'race_type' => $race_type->post_title,
				'team_id' => $getTeam['label'],
				'number_of_the_canoe' => $team_canoe,
				'race_captain' => $race_captain ? $race_captain['user_firstname'] . ' ' . $race_captain['user_lastname'] : "",
				'team_class' => $team_class,
				'avant_tribord' => $avant_tribord['user_firstname'] . ' ' . $avant_tribord['user_lastname'],
				'avant_babord' =>  $avant_babord['user_firstname'] . ' ' . $avant_babord['user_lastname'],
				'arriere_tribord' => $arriere_tribord['user_firstname'] . ' ' . $arriere_tribord['user_lastname'],
				'arriere_babord' =>$arriere_babord['user_firstname'] . ' ' . $arriere_babord['user_lastname'],
				'barreur' => $barreur['user_firstname'] . ' ' . $barreur['user_lastname'],
				'arrayFormatedV' => $arrayFormatedValue
			];
		}
	}

	 // Create Excel File
   include_once($_SERVER['DOCUMENT_ROOT']."/ACCGQ/wp-content/themes/Divi66-child2/PHPXLSXWritermaster/xlsxwriter.class.php");
	ini_set('display_errors', 0);
	ini_set('log_errors', 1);
	error_reporting(E_ALL & ~E_NOTICE);

	$filename = "subscribed_".date("Y-m-d H:i:s").".xlsx";
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');

	$Throws = array(array('ID', 'Race', 'Team','Canoe number','Captain','Classe','Avant tribord', 'Avant babord', 'Arrière tribord', 'Avant tribord', 'Arrière babord','Formation terminé'));
	$writer = new XLSXWriter();
	$writer->setAuthor('Some Author'); 
	$styles1 = array( 'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');
	foreach($Throws as $throw){
		$writer->writeSheetRow('Sheet1', $throw ,$styles1);
	}
	foreach($subscribedArray as $row){
		$writer->writeSheetRow('Sheet1', $row);
	}	
	$writer->writeToStdOut();
	exit(0);
   
}




get_header();
$user = wp_get_current_user();
if (!isCurrentUserAdmin() && !isCurrentUserPromoter()) {
	// redirect to home page if user is not admin
    wp_redirect(home_url() . '/my-account/my-profile');
    exit;
}

$teams = [];

// get racecircuit 
$args = array(
    'post_type' => 'racecircuit',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',

);



$team_args = array(
    'post_type' => 'teams',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',

);

$teamclasses = get_posts($args);

    foreach ($teamclasses as $teamclass) {
        
        $getTeamdata = get_field('team_class');
     // print_r($teamclass);
    }

// get currentuser role
if (in_array('promoters', (array)$user->roles)) {
    $getRace = get_associate_race($user->ID);
    
    // set 0 if empty
    if (!$getRace) {
        $getRace = [0];
    }

    $args['meta_query']    = array(
        'relation'        => 'AND',
        array(
            'key'        => 'race_type',
            'value'        => $getRace,
            'compare'    => 'IN'
        ),
    );
}

$racecircuits = get_posts($args);

if ($racecircuits) {
    foreach ($racecircuits as $racecircuit) {
        // if teams already exists, not add it again
        $getTeam = get_field('associate_team', $racecircuit->ID);
        $team_class = get_field('team_class', $racecircuit->ID);
      //  print_r($getTeam);
        $avant_tribord = get_field('avant_tribord', $racecircuit->ID);
        $race_captain = get_field('race_captain', $racecircuit->ID);
        $avant_babord = get_field('avant_babord', $racecircuit->ID);
        $arriere_tribord = get_field('arriere_tribord', $racecircuit->ID);
        $arriere_babord = get_field('arriere_babord', $racecircuit->ID);
        $barreur = get_field('barreur', $racecircuit->ID);
        $race_type = get_field('race_type', $racecircuit->ID);

        // if not race_type and tea is not exists
        $teams[] = [
            'id' =>  $racecircuit->ID,
            'team_id' => $getTeam['value'],
            'team_label' => $getTeam['label'],
            'avant_tribord' => [
                'id' => $avant_tribord['ID'],
                'name' => $avant_tribord['user_firstname'] . ' ' . $avant_tribord['user_lastname'],
                'training' => isBothTrainingDone($avant_tribord['ID'])
            ],
            'avant_babord' => [
                'id' => $avant_babord['ID'],
                'name' => $avant_babord['user_firstname'] . ' ' . $avant_babord['user_lastname'],
                'training' => isBothTrainingDone($avant_babord['ID'])
            ],
            'race_captain' => [
                'id' => $race_captain ? $race_captain['ID'] : "",
                'name' => $race_captain ? $race_captain['user_firstname'] . ' ' . $race_captain['user_lastname'] : "",
                'email' => $race_captain ? $race_captain['user_email'] : "",
                'training' => isBothTrainingDone($race_captain['ID'])
            ],
            'arriere_tribord' => [
                'id' => $arriere_tribord['ID'],
                'name' => $arriere_tribord['user_firstname'] . ' ' . $arriere_tribord['user_lastname'],
                'training' => isBothTrainingDone($arriere_tribord['ID'])
            ],
            'arriere_babord' => [
                'id' => $arriere_babord['ID'],
                'name' => $arriere_babord['user_firstname'] . ' ' . $arriere_babord['user_lastname'],
                'training' => isBothTrainingDone($arriere_babord['ID'])
            ],
            'barreur' => [
                'id' => $barreur['ID'],
                'name' => $barreur['user_firstname'] . ' ' . $barreur['user_lastname'],
                'training' => isBothTrainingDone($barreur['ID'])
            ],
            'race_type' => $race_type->post_title,
        ];
    }
}



?>

<div id="main-content">
    <div class="container">
        <div id="content-area" class="clearfix">
            <h1 class="main_title"><?php the_title(); ?></h1>
            <div class="entry-content mb-10">
                <?php the_content(); ?>
            </div>
        </div>
        <div class="account-area profile-contente">
            <!-- tabs section here -->
            <?php
            // profile
            $activeTab = 'subscribed-lists';
            include_once get_stylesheet_directory() . '/my-account/tabs.php';
            ?>

            <!-- content here -->
            <div class="profile-area">
                <form method="post" class="export">
                    <button type="submit" id="btnExport" name='export-subscribed-lists' value="Export Lists" class="btn btn-primary export-subscribed">
                        <?php echo esc_html__('Export Lists', 'divi-child'); ?>
                    </button>
                </form>
                <!-- list of subscribed users -->
                <div class="all-teams entry-content">

                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th class="order"><?php echo esc_html__('Race', 'divi-child'); ?></th>
                                <th class="order"><?php echo esc_html__('Team', 'divi-child'); ?></th>
                                <th class="order"><?php echo esc_html__('Captain', 'divi-child'); ?></th>
                                <th class="order"><?php echo esc_html__('Classe','divi-child'); ?>
                                    
                                </th>
                                <th><?php echo esc_html__('Position', 'divi-child'); ?></th>
                                <th><?php echo esc_html__('Is Training Done?', 'divi-child'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($teams) {
                                foreach ($teams as $key => $team) {
                                    
                             //$team_class = get_field('team_class', $teams->ID);
                             
                             $team_class = get_post( $team['team_id'] )->team_class;

                             $team_canoe = get_post( $team['team_id'] )->canoe_list;

                           //  print_r( $team_class);
                            ?>
                                    <tr>
                                        <td class="id"><?php echo $key + 1; ?></td>
                                        <td class="race-name"><?php echo $team['race_type']; ?></td>
                                        <td class="team-name"><?php echo $team['team_label']; ?> - <?php echo $team_canoe; ?></td>
                                        <td class="team-name">
                                            <?php echo $team['race_captain']['name']; ?><br />
                                            <?php echo $team['race_captain']['email']; ?>
                                        </td>
                                            <td class="classe">
                                            <div class="avant_tribord mb-2">
                                                <?php
                                                echo $team_class; ?>
                                            </div>
                                            
                                        
                                        
                                        </td>
                                        <td class="positions">
                                            <div class="avant_tribord mb-2">
                                                <?php
                                                echo 'Avant tribord: ' . $team['avant_tribord']['name']; ?>
                                            </div>
                                            <div class="avant_babord mb-2">
                                                <?php
                                                echo 'Avant babord: ' . $team['avant_babord']['name']; ?>
                                            </div>
                                            <div class="arriere_tribord mb-2">
                                                <?php
                                                echo 'Arrière tribord: ' . $team['arriere_tribord']['name']; ?>
                                            </div>
                                            <div class="arriere_babord mb-2">
                                                <?php
                                                echo 'Avant tribord: ' . $team['arriere_babord']['name']; ?>
                                            </div>
                                            <div class="barreur mb-2">
                                                <?php
                                                echo 'Arrière babord: ' . $team['barreur']['name']; ?>
                                            </div>
                                        </td>
                                       
                                        <td class="training">
                                            <div class="mb-2">
                                                <?php
                                                echo $team['avant_tribord']['training'] ? esc_html__('Yes', 'divi-child') : esc_html__('No', 'divi-child'); ?>
                                            </div>
                                            <div class="mb-2">
                                                <?php
                                                echo $team['avant_babord']['training'] ? esc_html__('Yes', 'divi-child') : esc_html__('No', 'divi-child'); ?>
                                            </div>
                                            <div class="mb-2">
                                                <?php
                                                echo $team['arriere_tribord']['training'] ? esc_html__('Yes', 'divi-child') : esc_html__('No', 'divi-child'); ?>
                                            </div>
                                            <div class="mb-2">
                                                <?php
                                                echo $team['arriere_babord']['training'] ? esc_html__('Yes', 'divi-child') : esc_html__('No', 'divi-child'); ?>
                                            </div>
                                            <div class="mb-2">
                                                <?php
                                                echo $team['barreur']['training'] ? esc_html__('Yes', 'divi-child') : esc_html__('No', 'divi-child'); ?>
                                            </div>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {

                                 echo esc_html__('No subscribed teams found', 'divi-child'); 

                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
jQuery(document).ready(function() {
	jQuery('body').on('click', '.export-subscribed', function() {
		H5_loading.show();
		setTimeout(function() {H5_loading.hide();}, 2000);
     });
	

});
</script>
<?php
get_footer();
