<?php

include_once 'database.php';
include_once 'db_maintenance.php';  // perform database installation/upgrades

$conn = Database::getInstance();

$v_id = $_GET["id"];
$v_submit = $_GET["submit"];
$v_page = $_GET["p"];

?>
<!DOCTYPE html>
<head>
    <title>TFM Quotes bin</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="icon" type="image/png" href="favicon.png">
</head>
<body><div class="form-style-1">
<div style="display:inline-block;"><h2><a href="./?submit"><span class="name">[Tactcat]</span></a> Hello, welcome to my <a href="." class="quoteslink" style="color: #ED67EA;border-bottom: 1px solid currentColor;">quotes</a> bin!</h2></div><input type="text" class="search" placeholder="search..." autocomplete="off" autofocus><font size="2"><a href="./?submit" style="color:white"> » submit</a></font><hr>
<div id="content">

<?php

$postproc = false;
if (isset($_POST["quote"])) {
    $quote = strip_tags($_POST["quote"]);
    $quote = preg_replace('/\r\n?/', "\n", $quote);  // convert CRLF to unix LF
	$quote = $conn->escape_string($quote);
	
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
			    shtml += row.quote.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + '<br>' + '$2');
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