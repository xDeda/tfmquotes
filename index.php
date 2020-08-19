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
    <link rel="stylesheet" type="text/css" href="tfmcolors.css">
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

window.onpopstate = function(event) {
    if (event.state == null)
        ShowAll();
    else
        ShowId(event.state.id);
}

$(".quoteslink").click(function(event) {
    ShowAll(histType.PUSH);
    event.preventDefault();
});

function eventPostprocDone() {
    $(".idtext").click(function(event) {
        var id = $(this).data("id");
        ShowId(id, histType.PUSH);
        event.preventDefault();
    });
}

const histType = {
    NONE: 0,
    PUSH: 1,
    REPLACE: 2,
}

function ShowId(id, push = histType.NONE) {
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
			    shtml += Colorify(row.quote);
		    	shtml += "</div><small>date: " + row.postdate + "</small><br />";
			}
			$('#content').html(shtml);
			eventPostprocDone();
			if (push == histType.PUSH)
			    history.pushState({id: id}, null, '?id=' + id);
			else if (push == histType.REPLACE)
			    history.replaceState({id: id}, null, '?id=' + id);
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

function ShowAll(push = histType.NONE) {
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
			        shtml += Colorify(row.quote);
			        shtml += "</div><small>date: " + row.postdate + " </small><hr /></div>";
                }
			}
			$('#content').html(shtml);
			eventPostprocDone();
			if (push == histType.PUSH)
			    history.pushState(null, null, '.');
        }
    });
}

var ignfmt = {
    NONE: 0,
    LINK: 1 << 0,
    COLOR: 1 << 1,
}
ignfmt.CLICKABLE = ignfmt.LINK | ignfmt.COLOR;

function ignFormat(name, flags = ignfmt.COLOR) {
    const r = /^([A-Za-z_\d]+)(#\d{4})$/;
    var ret = name;
    if (name.match(r)) {
        let c = "igntag";
        if (flags & ignfmt.COLOR)
            c += " igncolor";
        ret = name.replace(r, `$1<span class="${c}">$2</span>`);
    }
    if (flags & ignfmt.LINK) {
        let link = "https://atelier801.com/profile?pr=" + encodeURIComponent(name);
        ret = `<a target="_blank" href="${link}" class="tfmlink">${ret}</a>`;
    }
    return ret;
}

function Colorify(str, bt_format = true /* buildtool is the superior module */) {
    var sp = str.split("\n");
    var nstrs = [];
    for (let i = 0; i < sp.length; i++) {
        let s = sp[i];
        
        /* buildtool text formatting */
        if (bt_format && s[0] == "Ξ") {
            if (s.match(/(?:left|entered) the room/)
                    || s.match(/is (?:banned|an admin) now!/)) {
                s = `<span class="J">${s}</span>`;
            } else if (s.match(/\[.+?\] !/)) {
                s = `<span class="G">${s}</span>`;
            }
        } else
        
        do {
            let m = null;
            
            // (Dis)connect message
            m = s.match(/^(• )?(?:\[([0-9:]+)\] )?([A-Za-z_#\d]+) (has disconnected|just connected).$/);
            if (m) {
                let is_tribe = m[1];
                let time = m[2];
                let name = ignFormat(m[3]);
                let rest = m[4];
                s = "";
                if (is_tribe) {
                    s = `<span class="tribe1">• `;
                    if (time) {
                        s += `[${time}] `;
                    }
                } else {
                    s = `<span class="BL">`;
                }
                s += `${name} ${rest}.</span>`;
                break;
            }
            
            /* Room entry */
            m = s.match(/^You just entered room .+$/);
            if (m) {
                s = `<span class="BL">${m[0]}</span>`;
                s = s.replace(/(\/room RoomName)/, `<span class="V">$1</span>`);
                break;
            }
            
            /* Moderation / Admin */
            /* will conflict if admin texts in tribe or is not #0001 */
            m = s.match(/^• \[(?:Moderation|.+#0001)\] .+$/);
            if (m) {
                s = `<span class="ROSE">${m[0]}</span>`;
                break;
            }
            
            /* SERVER message */
            m = s.match(/^• \[SERVER\].+$/);
            if (m) {
                s = `<span class="ROSE"><b>${m[0]}</b></span>`;
                break;
            }
            
            /* Tribe chat */
            /* will conflict if #0001 speaks in tribe */
            m = s.match(/^• (?:\[([0-9:]+)\] )?\[([A-Za-z_#\d]+)\](.+)$/);
            if (m) {
                let time = m[1];
                let name = ignFormat(m[2], ignfmt.CLICKABLE);
                let rest = m[3];
                s = `<span class="tribe1">• `;
                if (time) {
                    s += `[${time}] `;
                }
                s += `[${name}]</span><span class="T">${rest}</span>`;
                break;
            }
            
            /* Whispers */
            m = s.match(/^(<|>) (?:\[([0-9:]+)\] )?(?:\[(.{2})\] )?\[([A-Za-z_#\d]+)\](.+)$/);
            if (m) {
                let symbol = m[1];
                let time = m[2];
                let commu = m[3];
                let name = ignFormat(m[4], ignfmt.CLICKABLE);
                let rest = m[5];
                let is_outgoing = symbol=="<";
                
                let col_class = (is_outgoing ? "out" : "in") + "_whisper";
                name = is_outgoing ? name : `<span class="in_whisper_name">${name}</span>`;
                
                s = `<span class="${col_class}">${symbol} `;
                if (time) {
                    s += `[${time}] `;
                }
                if (commu) {
                    s += `[${commu}] `;
                }
                s += `[${name}]${rest}</span>`;
                break;
            }
            
            /* Chat channel */
            m = s.match(/^((?:\[[0-9:]+\] )?\[•])( You have joined the channel.+)$/);
            if (m) {
                s = `<span class="chat1">${m[1]}</span><span class="chat2">${m[2]}</span>`;
                break;
            }
            m = s.match(/^(?:\[([0-9:]+)\] )?\[(.{2})] \[([A-Za-z_#\d]+)\](.+)$/);
            if (m) {
                let time = m[1];
                let commu = m[2];
                let name = ignFormat(m[3], ignfmt.CLICKABLE);
                let rest = m[4];
                s = `<span class="chat1">`;
                if (time) {
                    s += `[${time}] `;
                }
                if (commu) {
                    s += `[${commu}] `;
                }
                s += `[${name}]</span><span class="chat2">${rest}</span>`;
                break;
            }
            
            /* Now your shaman! */
            m = s.match(/^([A-Za-z_#\d]+)( is now your shaman, follow her!)$/);
            if (m) {
                let name = ignFormat(m[1], ignfmt.NONE);
                let rest = m[2]
                s = `<span class="CH">${name}</span><span class="BL">${rest}</span>`;
                break;
            }
            
            /* Thanks shaman */
            m = s.match(/^Thanks to ([A-Za-z_#\d]+)(, we gathered \d cheese!)$/);
            if (m) {
                let name = ignFormat(m[1], ignfmt.NONE);
                let rest = m[2]
                s = `<span class="BL">Thanks to <span class="V">${name}</span>${rest}</span>`;
                break;
            }
            
            /* Cheeeeeese *-* */
            m = s.match(/^Cheeeeeese \*-\* \(([0-9]+(?:\.[0-9]+)?s)\)$/);
            if (m) {
                s = `<span class="BL">Cheeeeeese *-* (<span class="V">${m[1]}</span>)</span>`;
                break;
            }
            
            /* System message */
            m = s.match(/^(?:\[([0-9:]+)\] )?\[•\](.+)$/);
            if (m) {
                let time = m[1];
                let system = ignFormat("•", ignfmt.CLICKABLE);
                let rest = m[2];
                s = `<span class="V">`;
                if (time) {
                    s += `[${time}] `;
                }
                s += `[${system}]</span><span class="BL">${m[2]}</span>`;
                break;
            }
            
            /* You're the shaman! */
            m = s.match(/^You're the shaman! Help your disciples to get the cheese!$/);
            if (m) {
                s = `<span class="J">${m[0]}</span>`;
                break;
            }
            
            /* No cheese for you */
            m = s.match(/^No cheese for you! \^_\^$/);
            if (m) {
                s = `<span class="BL">${m[0]}</span>`;
                break;
            }
            
            /* Basic room text */
            m = s.match(/^(?:\[([0-9:]+)\] )?\[([A-Za-z_#\d]+)\](.+)$/);
            if (m) {
                let time = m[1];
                let name = ignFormat(m[2], ignfmt.CLICKABLE);
                let rest = m[3];
                s = `<span class="V">`;
                if (time) {
                    s += `[${time}] `;
                }
                s += `[${name}]</span>${rest}`;
                break;
            }
        } while (false);
        
        nstrs.push(s);
    }

    return nstrs.join("\n");
}

<?php
if ($postproc) {
    if (isset($v_id)) {
    	echo "ShowId($v_id, histType.REPLACE)";
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