<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<head>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
<script src="http://assets.annotateit.org/annotator/v1.1.0/annotator-full.min.js"></script>
<link rel="stylesheet" href="http://assets.annotateit.org/annotator/v1.1.0/annotator.min.css">
<script type='text/javascript' src='https://cdn.firebase.com/v0/firebase.js'></script>
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css"></link>
<script type='text/javascript' src="bootstrap/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="toastr.min.css"></link>
<script type='text/javascript' src="toastr.min.js"></script>
<style>
body {background:white; color:#000000;overflow:hidden;}
html,body {height:100%}
#rec,#res,#def {
border: 1px solid black; 
padding: 5px;
margin-bottom:5px;
width: 90%;
font-size:90%;
}
</style>
</head>
<body>
<div class="row">
<div class="" style="width:65%;height:90%;position:fixed;padding-left:25px;margin-right:50px;overflow-y: scroll;font:15px helvetica,sans-serif;line-height:170%;">
<?php
	$ch = curl_init(); 
	$page = $_GET['page'];
    curl_setopt($ch, CURLOPT_URL, "https://api.pearson.com/penguin/classics/v1/articles/".$page."?apikey=HIDDEN"); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $output = curl_exec($ch); 
    curl_close($ch);    
    $js =json_decode($output);
    //echo $output;
    echo '<h2>'.$js->{'article'}->{'book'}->{'title'}.'</h2><hr>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	foreach($js->{'article'}->{'content'} as $val) {
    	foreach($val as $a) {
        	foreach($a as $b) {
        		if(mb_substr($b->{'text'}, -1) == '.')
        			echo $b->{'text'};
        		else
        			echo $b->{'text'};
        	}
        	echo '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }
    }
    //echo '<br>';	
    $bookid = $js->{'article'}->{'book'}->{'id'};
    $pagetitle = $js->{'article'}->{'title'};
?>
<ul class="pager">
  <li class="previous">
    <a href="#">&larr; Back</a>
  </li>
  <li class="next">
    <a href="#">Next &rarr;</a>
  </li>
</ul>
</div>
<div class="" style="width:32%;height:100%; position:fixed; right:0px; top:40px">
	<ul class="nav nav-tabs" id="myTab" >
  		<li class="active"><a href="#recent" data-toggle="tab">Recent</a></li>
  		<li><a href="#search" data-toggle="tab">Search</a></li>
  		<li><a href="#chat" data-toggle="tab">Chat</a></li>
      <li><a href="#define" data-toggle="tab">Define</a></li>
      <li><a href="#navigate" data-toggle="tab">Navigate</a></li>

	</ul>
 
	<div class="tab-content">
  		<div class="tab-pane active" id="recent" style="overflow-y: scroll;height:500px"></div>
  		<div class="tab-pane" id="search">
  			<div id="searchresults" style="overflow-y: scroll;height:400px">
  			</div>
  			<br>
  			<input type="text" id="searchbox" style="width:350px"  placeholder="Hit Enter to submit" >
  		</div>
  		<div class="tab-pane" id="chat" style="min-height:500px;">
  			<div id="messagebox" style="overflow-y: scroll;height:400px"></div>
  			<br>
  			<input type="text" id="inputbox" style="width:350px"  placeholder="Hit Enter to submit" >
  		</div>
      <div class="tab-pane" id="navigate" style="overflow-y: scroll;height:500px">
        <?php
          $ch = curl_init(); 
          curl_setopt($ch, CURLOPT_URL, "https://api.pearson.com/penguin/classics/v1/books/".$bookid."?apikey=HIDDEN"); 
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
          $output = curl_exec($ch); 
          curl_close($ch);    
          $js =json_decode($output);
          foreach($js->{'book'}->{'articles'} as $val) {
            echo "<a href='?sess=".$_GET['sess']."&page=".$val->{'id'}."'>".$val->{'title'}.'</a><br><br>';
          }
        ?>
      </div>
      <div class="tab-pane" id="define" style="overflow-y: scroll;height:500px">
          <div id="defineresults" style="overflow-y: scroll;height:400px">
          </div>
          <input type="text" id="definebox" style="width:350px" placeholder="Hit Enter to submit" >
      </div>
	</div>
 
	<script>
  		$(function () {
    		$('#myTab a:last').tab('show');
  		});
	</script>
</div>
</div>

</body>
<script>
$(document).ready(function() {
	var a;
	var annotate_map = new Object();
		jQuery(function ($) {
		
			var sess = "<?php echo $_GET['sess']; ?>";
      		var book = "<?php echo $bookid ?>";
      		var page = "<?php echo $_GET['page']; ?>";
      		var pagetitle = "<?php echo $pagetitle ?>";
			var name = prompt("Please enter your name","Harry Potter");
			var userID = Math.random().toString(36).substring(2);
			var firebase = new Firebase('7132068859.firebaseio.com/'+sess+"/"+book+"/ANNO");
			var firebase_chat = new Firebase('7132068859.firebaseio.com/'+sess+"/"+book+"/CHAT");
    		a = new Annotator(document.body)
    		a.addPlugin('Tags');
    		a.addPlugin( 'Filter', {
  				filters: [
    				{
      					label: 'Tag',
      					property: 'tags',
      					isFiltered: function (input, tags) {
        					if (input && tags && tags.length) {
          						var keywords = input.split(/\s+/g);
          						for (var i = 0; i < keywords.length; i += 1) {
            						for (var j = 0; j < tags.length; j += 1) {
              							if (tags[j].indexOf(keywords[i]) !== -1) {
                							return true;
              							}
            						}
          						}
        					}
        					return false;
    					}
    				}
  				]
			});
			
			Annotator.Plugin.HelloWorld = (function() {
  				function HelloWorld(element, options) {
    				this.element = element;
    				this.options = options;
    				console.log("Hello World!");
  				}
  				HelloWorld.prototype.pluginInit = function() {
    				      this.annotator
    				      	.subscribe("beforeAnnotationCreated", function (annotation) {
            					console.info("The annotation: %o has just been created!", annotation)
          					})
          					.subscribe("annotationCreated", function (annotation) {
          						if(annotation.userID == null) {
          							annotation.userID= userID;
    				      			annotation.name = name;
    				      			annotation.page = page;
    				      			annotation.pagetitle = pagetitle;
    				      			var p = firebase.push();
    				      			annotation.fbID = p.name();
    				      			var h = annotation.highlights;
    				      			delete annotation.highlights;
          							p.set(annotation);
          							annotation.highlights = h;
          						}
          						annotate_map[annotation.fbID] = annotation;
    							
            					console.info("The annotation: %o has just been created!", annotation)
          					})
          					.subscribe("annotationUpdated", function (annotation) {
            					console.info("The annotation: %o has just been updated!", annotation)
          					})
          					.subscribe("annotationDeleted", function (annotation) {
          						annotate_map[annotation.fbID] = null;
          						firebase.child(annotation.fbID).remove();
            					console.info("The annotation: %o has just been deleted!", annotation)
          					});
  				};
 	 			return HelloWorld;
			})();
			
			a.addPlugin('HelloWorld');
			
			firebase.on('child_added', function(data) {
				var v = data.val();
				var c = "";
				if(v.tags != null)
					c = v.tags.join();
				$('#recent').prepend("<div id='rec'><b>User:</b> " + v.name + "<br><b>Section: </b><a href='session.php?sess="+sess+"&page="+v.page+"'>"+v.pagetitle+ "</a><br><b>Quote:</b> <i>\"" + v.quote + "\"</i><br><b>Note:</b> " + v.text+"<br><b>Tags: </b>"+c+"</div>");
				
				if(v.userID == userID)
					return;
				if(v.page != page) {
					annotate_map[data.name()] = v;
					return;
				}
				a.setupAnnotation(v);
			});
			firebase.on('child_removed', function(data) {
				var v = data.val();
				if(v.page != page) return;
				if(annotate_map[v.fbID] == null)
					return;
				a.deleteAnnotation(annotate_map[v.fbID]);
			});
			
			firebase_chat.on('child_added', function(data) {
				var v= data.val();
				$('#messagebox').append("<b>"+v.name+"</b>: "+v.message+'<br>');
				var objDiv = document.getElementById("messagebox");
				objDiv.scrollTop = objDiv.scrollHeight;
			});
			
			$('#inputbox').keypress(function(event) {
				if (event.keyCode == 13) {
    				firebase_chat.push({"name":name, "message":$(this).val()});
    				$(this).val('');
    			}
			});
			
			$('#searchbox').keypress(function(event) {
				var temp = $('#searchbox').val();
				if (event.keyCode == 13) {
					$('#searchresults').empty();
					//console.log(annotate_map);
					for(var x in annotate_map) {
						var a = annotate_map[x].quote;
						var b = annotate_map[x].text;
						var c;
						if(annotate_map[x].tags != null)
							var c = annotate_map[x].tags.join();
						else
							var c = "";
						var v = annotate_map[x];
						console.log("c=",c);
						if(a.indexOf(temp) >= 0 || b.indexOf(temp) >= 0 || c.indexOf(temp) >= 0)
							$('#searchresults').append("<div id='res'><b>User:</b> " + v.name + "<br><b>Section: </b><a href='session.php?sess="+sess+"&page="+v.page+"'>"+v.pagetitle+ "</a><br><b>Quote:</b> <i>\"" + v.quote + "\"</i><br><b>Note:</b> " + v.text+"<br><b>Tags</b>: "+c+" </div>");
					}
    			}
			});

      $('#definebox').keypress(function(event) {
        if(event.keyCode == 13) {
          var v = $('#definebox').val();
          $.getJSON("http://api.pearson.com/v2/dictionaries/ldoce5/entries?headword="+v+"&apikey=HIDDEN", function(data)  {
            if(data.results[0] == undefined) return;
            var word = data.results[0].headword;
            var pos = data.results[0].part_of_speech;
            var definition = data.results[0].senses[0].definition;
            $('#defineresults').prepend("<div id='def'><b>Word:</b> " + word + "<br><b>Part of Speech:</b> " + pos + "<br><b>Definition: </b>" + definition + "</div>");
            var v = $('#definebox').val('');
          });
        }
      });

		});
	});
</script>

</html>