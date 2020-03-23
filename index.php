<?php

include_once 'database.php';
include_once 'db_maintenance.php';  // perform database installation/upgrades

$conn = Database::getInstance();

$v_id = $_GET["id"];
$v_submit = $_GET["submit"];
$v_page = $_GET["p"];

function Colorify($q) {
	$q = explode("<br />", $q);
	foreach ($q as $qq) {
		if (strpos($qq, "• [") === 0 && strpos($qq, "• [Moderation]") !== 0 && strpos($qq, "• [0") !== 0 && strpos($qq, "• [1") !== 0 && strpos($qq, "• [2") !== 0) {
			$qq = str_replace("• [", "<span class=\"tn\">• [", $qq);
			$qq = str_replace("]", "]</span><span class=\"tc\">", $qq) . "</span><br />";
			echo $qq;
		} elseif (strpos($qq, "• [0") === 0 || strpos($qq, "• [1") === 0 || strpos($qq, "• [2") === 0) {
			$qq = explode("]", $qq);
			echo "<span class=\"tn\">" . $qq[0] . "]" . $qq[1] . "]</span><span class=\"tc\">" . $qq[2];
			if (isset($qq[3])) { echo $qq[3]; }
			echo "</span><br />";
		} elseif (strpos($qq, "• [Moderation]") === 0) {
			echo "<span class=\"mod\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "• [SERVER]") === 0) {
			echo "<span class=\"mod\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "just connected.") || strpos($qq, "has disconnected.") || strpos($qq, "No cheese for you! ^_^") || strpos($qq, "You just entered room") !== false) {
			echo "<span class=\"connect\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "is now your shaman, follow her!") !== false) {
			echo "<span class=\"bluesham\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "name is already taken") !== false) {
			echo "<span class=\"errors\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "You have joined the channel") !== false) {
			echo "<span class=\"chat1\" style=\"color: #F0C5FE;\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "had just unlocked the") !== false) {
			echo "<span class=\"special\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "< [") !== false) {
			echo "<span class=\"wout\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "> [") !== false) {
			echo "<span class=\"wintext\">";
			$qq = str_replace("[", "[<span class=\"winname\">", $qq);
			echo str_replace("]", "</span>]", $qq);
			echo "</span><br />";
		} elseif (strpos($qq, "[Shaman ") !== false) {
			$qq = str_replace("[Shaman ", "<span class=\"names\">[Shaman ", $qq);
			echo str_replace("]", "]</span><span class=\"bluesham\"> ", $qq) . "</span><br />";
		} elseif (strpos($qq, "[") === 0) {
			$qq = str_replace("[", "<span class=\"names\">[", $qq);
			echo str_replace("]", "]</span>", $qq) . "<br />";
		} else {
			echo $qq . "<br />";
		}
	}
}

function mynl2br($text) {
	return strtr($text, array("\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />')); 
}
?>
<!DOCTYPE html>
<head>
    <title>TFM Quotes bin</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="icon" type="image/png" href="favicon.png">
</head>
<body><div class="form-style-1">
<div style="display:inline-block;"><h2><a href="./?submit"><span class="name">[Tactcat]</span></a> Hello, welcome to my <a href="." class="quoteslink" style="color: #ED67EA;border-bottom: 1px solid currentColor;">quotes</a> bin!</h2></div><input type="text" class="search" placeholder="search..." autocomplete="off" autofocus><font size="2"><a href="./?submit" style="color:pink"> » submit</a></font><hr>
<div id="content">

<?php

$postproc = false;
if (isset($_POST["quote"])) {
	$quote = mynl2br($_POST["quote"]);
	$quote = addslashes($quote);
	$quote = strip_tags($quote, '<br>');

	$sql = "INSERT INTO quotes (id, quote, postdate) VALUES (NULL, '$quote', CURRENT_TIMESTAMP)";
	if ($conn->query($sql)){
		$last_id = $conn->insert_id;
	    echo "Records added successfully. " . $last_id;
	} else{
	    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
	}

	header("Location: ./?id=$last_id");

} elseif (isset($v_submit)) {
?>
        Your Quote <span class="required">*</span>
     	<form method="post" method="index.php">
        <textarea name="quote" id="field5" class="field-long field-textarea"></textarea>
        <input type="submit" value="Submit" />
    </form>

<?php 
} else {
    $postproc = true;
}
?>
</div>
<script>
function delay(fn, ms) {
	let timer = 0
	return function(...args) {
		clearTimeout(timer)
		timer = setTimeout(fn.bind(this, ...args), ms || 0)
	}
}
jQuery.expr[':'].icontains = function(a, i, m) {
  return jQuery(a).text().toUpperCase()
      .indexOf(m[3].toUpperCase()) >= 0;
};
$(".search").keyup(delay(function(){$(".quote:not(:icontains('"+$(this).val()+"'))").parent().css("display","none");}, 500));
$(".search").keyup(delay(function(){$(".quote:icontains('"+$(this).val()+"')").parent().css("display","initial");console.log("showing "+$(this).val())}, 500));
$(".quoteslink").click(function(event) {
    ShowAll(true);
    history.pushState(null, null, '.');
    event.preventDefault();
});

function eventPostprocDone() {
    $(".idtext").click(function(event) {
        var id = $(this).data("id");
        ShowId(id, true);
        history.pushState({id: id}, null, '?id=' + id);
        event.preventDefault();
    });
}
    
function ShowId(id, push = false) {
    $.ajax({
        type: 'POST',
        url: 'quotes_get.php',
        data: {action: 'get', id: id},
        success: function(response){
            var quotes = JSON.parse(response);
            var shtml = "";

            if (quotes.length == 0) {
                shtml += "0 results";
            } else {
                let row = quotes[0];
				shtml += "<a href=\"./?id="+row.id+"\" class=\"idtext\" data-id=\""+row.id+"\"> Quote ID: "+row.id+"</a><div class=\"quote\">";
			    shtml += row.quote;
		    	shtml += "</div><small>date: " + row.postdate + "</small><br />";
			}
			$('#content').html(shtml);
			eventPostprocDone();
        }
    });
}

/*function ShowPage(num, push = false) {
    var page2 = num;
	var page3 = page2-1;
	var page4 = page2+1;

	$result = $conn->query("SELECT COUNT(*) FROM `quotes`");
	$row = $result->fetch_row();
	$maxpage = substr($row[0], 0, -1);
	
    $.ajax({
        type: 'POST',
        url: 'quotes_get.php',
        data: {action: 'get', limit: 10, skip: num*10},
        success: function(response){
            var quotes = JSON.parse(response);
            var shtml = "";

            if (quotes.length == 0) {
                shtml += "0 results";
            } else {
                shtml += "<div style=\"align:left;float:right;\"><a href=\"/.?p=0\">« <a href=\"./?p=$page3\">‹</a> $page2 <a href=\"./?p=$page4\">›</a> <a href=\"./?p=$maxpage\">»</a></div>";
                for (var i = 0; i < quotes.length; i++) {
                    let row = quotes[i];
                    shtml += "<div><a href=\"./?id="+row.id+"\" class=\"idtext\">Quote ID: "+row.id+"</a><div class=\"quote\">";
			        shtml += row.quote;
			        shtml += "</div><small>date: " + row.postdate + " </small><hr /></div>";
                }
			}
			$('#content').html(shtml);
			eventPostprocDone();
        }
    });

}*/

function ShowAll(push = false) {
    $.ajax({
        type: 'POST',
        url: 'quotes_get.php',
        data: {action: 'get'},
        success: function(response){
            var quotes = JSON.parse(response);
            var shtml = "";

            if (quotes.length == 0) {
                shtml += "0 results";
            } else {
                for (var i = 0; i < quotes.length; i++) {
                    let row = quotes[i];
                    shtml += "<div><a href=\"./?id="+row.id+"\" class=\"idtext\" data-id=\""+row.id+"\">Quote ID: "+row.id+"</a><div class=\"quote\">";
			        shtml += row.quote;
			        shtml += "</div><small>date: " + row.postdate + " </small><hr /></div>";
                }
			}
			$('#content').html(shtml);
			eventPostprocDone();
        }
    });
} 

<?php
if ($postproc) {
    if (isset($v_id)) {
    	echo "ShowId($v_id)";
    } elseif (isset($v_page)) { 
        echo "ShowPage($v_page);";
    } else {
        echo "ShowAll();";
    }
}
?>
</script>
</div>
</body>