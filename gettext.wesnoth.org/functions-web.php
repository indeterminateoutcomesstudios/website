<?php

if (!defined('IN_WESNOTH_LANGSTATS'))
{
	die(1);
}

function ui_self_link($disable_condition, $text, $href)
{
	if ($disable_condition)
	{
		echo '<b>' . $text . '</b>';
	}
	else
	{
		echo '<a href="' . htmlspecialchars($href) . '">' . $text . '</a>';
	}
}

/**
 * Prints the timestamp element.
 */
function ui_last_update_timestamp($date)
{
	echo '<div id="lastmod" class="fr">Last updated on ' . date('r', $date) . '</div>';
}

/**
 * Prints a box with the specified error message.
 */
function ui_error($message)
{
	echo '<div class="error-message"><span class="message-heading">Error</span><br />' . $message . '</div>';
}

/**
 * Prints a box with the specified instruction message.
 */
function ui_message($message)
{
	echo '<div class="ui-message">' . $message . '</div>';
}

function ui_catalog_link_internal($textdomain, $lang, &$lang_label, &$show_lang_code)
{
	$path_fragment = '';

	if ($lang === null)
	{
		$path_fragment = $textdomain . '.pot';

		if ($lang_label === null)
		{
			$lang_label = "Template catalog";
		}
	}
	else
	{
		$path_fragment = $lang . '.po';

		if ($lang_label === null)
		{
			$lang_label = "&lt;unspecified language <code>$lang</code>&lt;";
			$show_lang_code = false;
		}
	}

	return $path_fragment;
}

function ui_mainline_catalog_link($branch, $textdomain, $lang = null, $lang_label = null, $show_lang_code = false)
{
	global $mainline_file_url_prefix;

	$path = '/' . $branch . '/po/' . $textdomain . '/' .
	        ui_catalog_link_internal($textdomain, $lang, $lang_label, $show_lang_code);

	echo '<a class="textdomain-file" href="' . htmlspecialchars($mainline_file_url_prefix . $path) .
	     '">' . $lang_label . '</a>';

	if ($show_lang_code)
	{
		echo ' (<code>' . $lang . '</code>)';
	}
}

function ui_addon_catalog_link($repo, $textdomain, $lang = null, $lang_label = null, $show_lang_code = false)
{
	$path = '/' . $repo . '/master/po/' .
	        ui_catalog_link_internal($textdomain, $lang, $lang_label, $show_lang_code);

	echo '<a class="textdomain-file" href="' . htmlspecialchars('https://raw.github.com/wescamp' . $path) .
	     '">' . $lang_label . '</a>';

	if ($show_lang_code)
	{
		echo ' (<code>' . $lang . '</code>)';
	}
}

/**
 * Prints the statistics headers common to textdomain and team views.
 */
function ui_column_headers()
{
	?><th class="translated">Translated</th>
	<th class="translated percent">%</th>
	<th class="fuzzy">Fuzzy</th>
	<th class="fuzzy percent">%</th>
	<th class="untranslated">Untranslated</th>
	<th class="untranslated percent">%</th>
	<th class="total">Total</th>
	<th class="graph">Graph</th><?php
}

/**
 * Prints the statistics columns HTML.
 *
 * @param $strcount   (int) String count for this row.
 * @param $translated (int) Number of translated strings.
 * @param $fuzzy      (int) Number of fuzzy strings.
 * @param $pot_total  (int) Template string count. This is used instead of
 *                          $strcount to calculate percentages and the bar
 *                          graph if provided. Used for the language view
 *                          (because apparently people wanted to see the number
 *                          of translated + fuzzy strings on the Total column
 *                          for some reason?)
 */
function ui_stat_columns($strcount, $translated, $fuzzy, $pot_total = null)
{
	if ($pot_total === null)
	{
		$pot_total = $strcount;
	}

	$untranslated    = $strcount - $translated - $fuzzy;
	$pc_translated   = 100 * $translated / $pot_total;
	$pc_fuzzy        = 100 * $fuzzy / $pot_total;
	$pc_untranslated = 100 * $untranslated / $pot_total;

	$fmt = "%0.2f";

	echo '<td class="translated">'   . $translated . '</td>' .
	     '<td class="percent">'      . sprintf($fmt, $pc_translated) . '</td>' .
	     '<td class="fuzzy">'        . $fuzzy . '</td>' .
	     '<td class="percent">'      . sprintf($fmt, $pc_fuzzy) . '</td>' .
	     '<td class="untranslated">' . $untranslated . '</td>' .
	     '<td class="percent">'      . sprintf($fmt, $pc_untranslated) . '</td>' .
	     '<td class="strcount">'     . $strcount . '</td>' .
	     '<td class="graph">';

	$graph_width   = 240; // px
	$graph_trans   = sprintf("%d", $translated * $graph_width / $pot_total);
	$graph_fuzzy   = sprintf("%d", $fuzzy * $graph_width / $pot_total);
	$graph_untrans = $graph_width - $graph_trans - $graph_fuzzy;

	$graph_class_sections = [
		"green" => $graph_trans,
		"blue"  => $graph_fuzzy,
		"red"   => $graph_untrans,
	];

	foreach ($graph_class_sections as $class => $width)
	{
		if ($width > 0)
		{
			echo '<span class="stats-bar ' . $class . '-bar" style="width:' .
			     $width . 'px"></span>';
		}
	}

	echo '</td>';
}