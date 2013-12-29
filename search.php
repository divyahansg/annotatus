<html>		
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>

<link rel="stylesheet" href="stuff.css">
<style>
	@import url(http://fonts.googleapis.com/css?family=Lato:300,400,700);
	body {
		background: url('bg.jpg') no-repeat center center fixed; ;
    	font-family: 'Lato', Calibri, Arial, sans-serif;
    }
    #title {
  background: #fff;
  border: 1px solid  rgba(0, 0, 0, .2);
  padding: 12px;
  position: relative;
  font-size: 30px;
  
  width: 600px;
  font-family: "HelveticaNeue-Light", "HelveticaNeue", Helvetica, Arial, sans-serif;
  
  -webkit-transition: all .2s ease;
  -moz-transition: all .2s ease;
  -ms-transition: all .2s ease;
  -o-transition: all .2s ease;
  transition: all .2s ease;
}
ul {


}
a {
	color:white;
}
</style>
	<body>
			<div  style="text-align:center; padding-top:25px; color:white; font-size:250%;font-family: 'HelveticaNeue-Light', 'HelveticaNeue', Helvetica, Arial, sans-serif;padding-bottom:10px">
				What book do you want to annotate?  
				<br>
			</div>
			<div style="text-align:center">
				<input type="text" id="title" name="book_name"><br><br>
				<input type="submit" id="submit" value="Submit" class="btn btn-1 btn-1a">
			</div>
			<div id="list" style="font-weight:bold;text-align:center; padding-top:50px; font-size:200%; color:white;font-family: 'HelveticaNeue-Light', 'HelveticaNeue', Helvetica, Arial, sans-serif">
			</div>
				<?php
					$sess = uniqid();
				?>
			<div style="position:fixed;bottom:0px;right:0px">
				<div style="font-style:italic; padding:10px; background-color:black; color:white;font-size:400%">Annotatus</div>
			</div>
	</body>
	<script>
		var sess = '<?php echo $sess ?>';
		function loader(id) {
			$.getJSON('https://api.pearson.com/penguin/classics/v1/books/'+id+'?apikey=HIDDEN', function(data)  {
				var x = data.book.articles[0].id;
				console.log("sess="+sess);
				console.log("x="+x);
				window.location = 'session.php?sess='+sess+'&page='+x;
			});
		}

		$("#submit").on('click', function() {
				$.getJSON('http://api.pearson.com/penguin/classics/v1/books?title='+encodeURIComponent($('#title').val())+'&limit=10&apikey=HIDDEN', function(data) {
					for(var b in data.books)
						var x = data.books[b];
						console.log(x.title);
						console.log(sess);
						$('#list').append("<div style='background-color:black; padding:10px'><a style='color:white' onclick='loader(&quot;"+x.id+"&quot;)' id='"+x.id+"' href='#'>"+x.title+" by "+x.authors[0].full_name + "</a></div>");
						//x.id
				});
		}); 
	</script>
</html>