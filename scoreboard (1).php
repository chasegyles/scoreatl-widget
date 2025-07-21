<?
/*
Template Name: Scoreboard 
*/
	$functions_code = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/__score_admin/global/functions.php");
	$functions_code = str_replace("get_post(","wui_get_post(",$functions_code);
	$functions_code = str_replace(array("<?php","<?","?>"),"",$functions_code);
	eval($functions_code);

	require_once($_SERVER['DOCUMENT_ROOT'] . "/__score_admin/global/request/select.categories.with.weekly.view.php");
	
	$classification = get_query("classification");

	if(strpos($classification, "|") === false){
		$classification = "";
		$region = "";
	}
	else{
		$region = explode("|", $classification );
		if($region[0] == "classification"){
			$classification = (int) $region[1];
			$region = 0;
		}
		elseif($region[0] == "region"){
			$stmt = $pdo->query("SELECT classification_id FROM _SCORE_REGIONS WHERE id = " . (int) $region[1]);
			$classification = $stmt->fetchColumn();
			$region = (int) $region[1];
		}
	}
	$classification_id = $classification;
	$region_id = $region;
	
	$sort   = get_query("sort");
	$search = get_query("search");

	$filter_states = get_query("filter_states");
	if(!$filter_states) $filter_states = "GA";
	$array_filter_states = explode("|", $filter_states);
	$filter_states = get_query("filter_states");

	require_once($_SERVER['DOCUMENT_ROOT'] . "/__score_admin/global/global_vars.php");
	require_once($_SERVER['DOCUMENT_ROOT'] . "/__score_admin/global/request/select.category.php");
	require_once($_SERVER['DOCUMENT_ROOT'] . "/__score_admin/global/request/select.weeks.from.cat.php");
	require_once($_SERVER['DOCUMENT_ROOT'] . "/__score_admin/global/request/select.week.from.cat.current.php");
	require_once($_SERVER['DOCUMENT_ROOT'] . "/__score_admin/global/request/select.week.php");
	require_once($_SERVER['DOCUMENT_ROOT'] . "/__score_admin/global/request/select.games.in.week.php");

	$category_id = get_query("category_id");
	$week_id = get_query("week_id");
	
	if(!$category_id){
		$category_id = $categories_with_weekly_view[0]['id'];
	}
	
	$query_category_id = $category_id;

	mysqli_stmt_execute($stmt_select_weeks_from_cat);
	mysqli_stmt_store_result($stmt_select_weeks_from_cat);
	
	mysqli_stmt_bind_result($stmt_select_weeks_from_cat, $slt_week_id, $slt_week_date_start);

	while(mysqli_stmt_fetch($stmt_select_weeks_from_cat)){
		$weeks[count($weeks)] = array();
		$weeks[count($weeks) - 1]['id']         = $slt_week_id;
		$weeks[count($weeks) - 1]['date_start'] = $slt_week_date_start;
	}
	
	if(!$week_id){
		require($_SERVER['DOCUMENT_ROOT'] . "/__score_admin/global/request/data/select.week.from.cat.current.php");
		$query_week_id = $week_id;
	}
	else{
		$query_week_id = $week_id;
		mysqli_stmt_execute($stmt_select_week);
		mysqli_stmt_store_result($stmt_select_week);
			
		mysqli_stmt_bind_result($stmt_select_week, $week_id, $week_date_start, $week_category_id, $week_deleted, $week_created, $week_modified);
		mysqli_stmt_fetch($stmt_select_week);
	}
	
	$query_category_id = $week_category_id;
	mysqli_stmt_execute($stmt_select_category);
	mysqli_stmt_store_result($stmt_select_category);
	
	mysqli_stmt_bind_result($stmt_select_category, $category_id, $category_name, $category_priority, $category_created, $category_modified);
	mysqli_stmt_fetch($stmt_select_category);
	
	mysqli_stmt_execute($stmt_select_games_in_week);
	mysqli_stmt_store_result($stmt_select_games_in_week);
	
	mysqli_stmt_bind_result($stmt_select_games_in_week, $game_id, $game_date, $game_home_team_name, $game_home_team_wins, $game_home_team_losses, $game_home_team_score, $game_away_team_name, $game_away_team_wins, $game_away_team_losses, $game_away_team_score, $game_period, $game_time_remaining, $game_notes, $game_prominent, $game_of_the_week, $game_priority);
	$games = array();
	while(mysqli_stmt_fetch($stmt_select_games_in_week)){
		$games[] = array();
		$games[count($games) - 1]['id']				  = $game_id;
		$games[count($games) - 1]['date']             = $game_date;
		$games[count($games) - 1]['home_team_name']   = $game_home_team_name;
		$games[count($games) - 1]['home_team_wins']   = $game_home_team_wins;
		$games[count($games) - 1]['home_team_losses'] = $game_home_team_losses;
		$games[count($games) - 1]['home_team_score']  = $game_home_team_score;
		$games[count($games) - 1]['away_team_name']   = $game_away_team_name;
		$games[count($games) - 1]['away_team_wins']   = $game_away_team_wins;
		$games[count($games) - 1]['away_team_losses'] = $game_away_team_losses;
		$games[count($games) - 1]['away_team_score']  = $game_away_team_score;
		$games[count($games) - 1]['period']           = $game_period;
		$games[count($games) - 1]['time_remaining']   = $game_time_remaining;
		$games[count($games) - 1]['notes']            = $game_notes;
		$games[count($games) - 1]['prominent']        = $game_prominent;
		$games[count($games) - 1]['game_of_the_week'] = $game_of_the_week;
		$games[count($games) - 1]['game_priority']    = $game_priority;

		if(is_numeric($games[count($games) - 1]['period'])){
			$games[count($games) - 1]['period'] = $vendor_game_status_options[$games[count($games) - 1]['period']][0];
		}
	}
	$ad_section = "highschool";

	$query = "SELECT * FROM _SCORE_CLASSIFICATIONS ORDER BY priority";
	$classifications = $pdo->query($query)->fetchAll();

	$query = "SELECT * FROM _SCORE_REGIONS WHERE classification_id = :classification_id ORDER By priority";
	$stmt_select_regions = $pdo->prepare($query);

ob_start();
?>
<?php get_header(); ?>

	<div id="content" class="page col-full">
			<div id="main" class="fullwidth">
<?php

	$html = ob_get_contents();
	ob_end_clean();

	$html = str_replace("//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js", "http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js", $html);
	$html = str_replace("<script type='text/javascript' src='http://www.scoreatl.com/wordpress/wp-includes/js/jquery/jquery.js?ver=1.8.3'></script>", "", $html);
	echo $html;
?>

<!--script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js" type="text/javascript"></script-->
<script type="text/javascript" src="/scripts/jquery-combo/jquery.ba-outside-events.min.js"></script>
<script type="text/javascript" src="/scripts/jquery-combo/jquery.combo.js"></script>
<link type="text/css" rel="stylesheet" href="/scripts/jquery-combo/style.css" />

<script type="text/javascript">
$(document).ready(function(){
	console.log($('#slt_classification'));
	$('#slt_classification').combo({
		selectId: 'slt_classification',
		select_categories: true,
		callbacks: {
			afterChoose: function(combo) { $('#btn_submit_filter').click(); }
		}
       });
	$('#slt_sort').combo({
		selectId: 'slt_sort',
		select_categories: true,
		width:50,
		callbacks: {
			afterChoose: function(combo) { $('#btn_submit_filter').click(); }
		}
       });

	$("#update_scoreboard").submit(function(){
		$("select[name=classification]:first").remove();
		$("select[name=sort]:first").remove();
	});

	var additional_selector = "";
	if(!$.browser.mozilla){
		additional_selector = ", .combo:first > table .combo-center";
	}
	$("#link_filter_by_region" + additional_selector).click(function(e){
		e.preventDefault(); 
		window.setTimeout(function(){ 
			$('#update_scoreboard .filters .combo:first .combo-arrow:first').click(); 
			window.setTimeout(function(){ 
				$('#update_scoreboard .filters .combo:first .combo-wrapper:first .combo-list:first ul li img').click(); 
			}, 0);
		}, 0);
	});

	$(".state-filters .filter_box").click(function(e){
		if($(this).hasClass("active_filter")){
			$current_filter_box = $(this);
			var current_filter_states = $("#filter_states").attr("value");
			current_filter_states = current_filter_states.split("|");
			$(current_filter_states).each(function(index, val){
				if(val == $current_filter_box.html()){
					current_filter_states.splice(index, 1);
				}
			});
			$("#filter_states").attr("value", current_filter_states.join("|"));
			$('#btn_submit_filter').click();
		}
		else{
			var current_filter_states = $("#filter_states").attr("value");
			if(current_filter_states == ""){
				current_filter_states = Array();
			}
			else{
				current_filter_states = current_filter_states.split("|");
			}
			current_filter_states[current_filter_states.length] = $(this).html();
			$("#filter_states").attr("value", current_filter_states.join("|"));
			$('#btn_submit_filter').click();
		}
	});
});
</script>
<style type="text/css">
#scoreboard-header{
	overflow:inherit;
}

#content-wrapper{
	overflow:visible;
}

#bottom-footer-ad{
	clear:left;
}

#filters{
	z-index:100;
}

<?php
/*	if(strpos($_SERVER['HTTP_USER_AGENT'], "Firefox") !== false){
?>

.combo table, .combo table td{
	height:17px !important;
}

<?php
	}*/
?>
</style>

			
			<div id="content-wrapper" class="clearfix">
				<div id="scoreboard">
					<div id="scoreboard-header">
						<h2>Change Sport</h2>
						<form id="update_scoreboard" method="get" action="." style="display:inline;">
						<div id="sport">
							<select id="slt_sport" name="category_id" onchange="$('#current_week_id').attr('value', ''); $('#btn_submit_filter').click();">
<?php
	foreach($categories_with_weekly_view as $category){
?>
								<option value="<?= $category['id'] ?>" <?= $category_id == $category['id'] ? "selected=\"selected\"" : "" ?>><?= $category['name'] ?></option>
<?php
	}
?>
							</select>
						</div>
						<h3>Week of <?= date("n/j/Y", strtotime($week_date_start)) ?></h3>
						<div id="previous-scores">
							<select id="slt_previous_weeks" name="week_id" onchange="$('#current_week_id').attr('value', this.options[this.selectedIndex].value); $('#btn_submit_filter').click();">
								<option selected="selected" value="">See Previous Scores</option>
<?php
	foreach($weeks as $week){
?>
								<option value="<?= $week['id'] ?>"><?= date("n/j/Y", strtotime($week['date_start'])) ?></option>
<?php
	}
?>
							</select>
						</div>
						<div class="filters" style="clear:left;">
							Filter By:

		<select id="slt_classification" name="classification">
			<optgroup label="&nbsp;"></optgroup>
			<script type="text/javascript"> $("#slt_classification optgroup:last").data("value", ""); </script>		

<?php 
	foreach($classifications as $classification){
?>
			<optgroup label="Class <?= $classification['name'] ?>">
			<script type="text/javascript"> $("#slt_classification optgroup:last").data("value", "classification|<?= $classification['id'] ?>"); </script>
<?php
		if($classification_id == $classification['id']){
?>
			<script type="text/javascript"> $("#slt_classification optgroup:last").data("selected", "selected"); </script>
<?php
	}
?>
<?php
		$stmt_select_regions->bindValue(":classification_id", $classification['id']);
		$stmt_select_regions->execute();
		$regions = $stmt_select_regions->fetchAll();
		foreach($regions as $key=>$region){
?>
			<option value="region|<?= $region['id'] ?>" <?php if($region_id == $region['id']){ ?>selected="selected"<?php } ?>>Region <?= $region['name'] ?></option>
<?php
		}
?>
			</optgroup>
<?php
	}
?>
		</select>

<?php /*							<select id="slt_classification" name="classification_old" onchange="//document.getElementById('current_week_id').name='week_id'; document.getElementById('update_scoreboard').submit();" style="display:none;">
								<option value=""></option>
<?php
	foreach($classifications as $classification){
?>
								<option value="<?= $classification['id'] ?>" <?php if($classification_id == $classification['id']){ ?> selected="selected"<?php } ?>>Class <?= $classification['name'] ?></option>				
<?php
	}
?>
							</select><?php */ ?>
							<input type="hidden" id="current_week_id" name="current_week_id" value="<?= $week_id ?>" />

							| Sort By:
                                                 <select id="slt_sort" name="sort" onchange="//document.getElementById('current_week_id').name='week_id'; document.getElementById('update_scoreboard').submit();">
								<option value="home" <?php if($sort == "home"){ ?>selected="selected"<?php } ?>>Home</option>
								<option value="away" <?php if($sort == "away"){ ?>selected="selected"<?php } ?>>Away</option>
								<option value=""></option>
							</select>
							| Search:
							<input type="text" name="search" value="<?= str_replace('"', "&quot;", $search) ?>" ?>
							<input id="btn_submit_filter" type="submit" value="Go" onclick="document.getElementById('current_week_id').name='week_id';" />
							<span style="font-size:10px;">(<a id="link_filter_by_region" href="#">Filter by region</a>)</span>
						</div>
						<div class="filters state-filters">
							<div class="filter_label">
								State(s):
							</div>
<?php
	foreach($active_states as $state){
?>
							<div class="filter_box <?php if(in_array($state, $array_filter_states)){ ?>active_filter<?php } ?>"><?= $state ?></div>
<?php
	}
?>
							<input id="filter_states" type="hidden" name="filter_states" value="<?= $filter_states ?>" />
						</div>
						</form>
					</div>
					<div id="scoreboard-content">
						<!-- SCOREBOARD BOX -->
<?php
/*						<div class="scoreboard-box game-of-the-week" <?php if($key%3 == 2){ ?>style="margin-right:0; !important"<?php } ?>>
							<p class="game-info"><strong>Game of the Week</strong> for Friday 09.05.2008 07:30 PM</p>
							<div class="team-scores clearfix top-score">
								<div class="team"><h3>Prince Avenue</h3><span>(7-2)</span></div>
								<div class="score"><p>7</p></div>
							</div>
							<div class="team-scores clearfix">
								<div class="team"><h3>Fellowship</h3><span>(6-3)</span></div>
								<div class="score"><p>21</p></div>
							</div>
							<div class="extra-info">
								<p>2nd quarter</p>
							</div>
							<div class="notes">
								<p><strong>Notes:</strong> What a Game!</p>
							</div>
						</div>
*/
?>
<?php
	if(count($games)){
		foreach($games as $key => $game){
?>
						<div class="scoreboard-box <?php if($game['game_of_the_week'] && false){ ?>game-of-the-week<?php } ?>" <?php if($key%3 == 2){ ?>style="margin-right:0; !important"<?php } ?>>
							<p class="game-info"><?= $category_name ?> <?= date("l m.d.Y g:i A", strtotime($game['date'])) ?></p>
							<div class="team-scores clearfix top-score">
								<div class="team"><h3><?= $game['away_team_name'] ?></h3><span>(<?= $game['away_team_wins'] ?>-<?= $game['away_team_losses'] ?>)</span></div>
								<div class="score"><p><?= $game['away_team_score'] ?></p></div>
							</div>
							<div class="team-scores clearfix">
								<div class="team"><h3><?= $game['home_team_name'] ?></h3><span>(<?= $game['home_team_wins'] ?>-<?= $game['home_team_losses'] ?>)</span></div>
								<div class="score"><p><?= $game['home_team_score'] ?></p></div>
							</div>
							<div class="extra-info">
								<p><?= $game['period'] ?> <?= $game['time_remaining'] ?> <?= $game['time_remaining'] ? "remaining" : "" ?></p>
							</div>
							<div class="notes">
								<p><strong>Notes:</strong> <?= $game['notes'] ?></p>
							</div>
						</div>
						<?php // BOX ENDS HERE AND NEW BOX BEINGS BELOW ?>
<?php
		}
	}
	else{
?>
						<em>Sorry, no scores are available for this week.  Check back later.</em>
<?php
	}
?>
					</div>				
				</div>
			</div>
		</div>
	</div>


</div><!-- /#content -->

<?php get_footer(); ?>