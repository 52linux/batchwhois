<?php 

use Iodev\Whois\Factory;
error_reporting(E_ALL || ~E_NOTICE);
   if(isset($_POST["d"]) && $_POST["d"])
   {
      
     require 'vendor/autoload.php';


     $d=$_POST["d"];
    // $whoisinfo = new Phois\Whois\Whois($d);
     //var_export();
     //var_export($whoisinfo->expTime());
    // $info=$whoisinfo->info();
      
  $whois = Factory::get()->createWhois();
  try{
  $info = $whois->loadDomainInfo($d);
  //var_export($info);
  /*
  print_r([
      'Domain created' => date("Y-m-d", $info->creationDate),
      'Domain expires' => date("Y-m-d", $info->expirationDate),
      'Domain owner' => $info->owner,
  ]);
  */
  if($info){
  	 $regdate=date("Y-m-d", $info->creationDate)?date("Y-m-d", $info->creationDate):0;
  	 $expdate=$info->expirationDate?date("Y-m-d", $info->expirationDate):0;
     $whoisdata=array(
      "whois"=>$info,
      "domain"=>$d,
      "regdate"=>$regdate,
      "expdate"=>$expdate,
      "leftdate"=>cal_leftdates($info->creationDate,$info->expirationDate)
     );
     echo json_encode($whoisdata);
     
  }else{

  	//域名可注册
  	 $whoisdata=array(
      "whois"=>"unregistered",
      "domain"=>$d,
      "regdate"=>"可以注册",
      "expdate"=>"",
      "leftdate"=>""
     );
     echo json_encode($whoisdata);

  }

 die();

  }
  catch (Exception $e) {
     $whoisdata=array(
      "whois"=>"error",
      "domain"=>$d,
      "regdate"=>"error",
      "expdate"=>"error",
      "leftdate"=>"error"
     );

      echo json_encode($whoisdata);
      die();
  }
   
  }

function cal_leftdates($s,$e)
{
  if($s==0 || $e==0) return 0;
  $days = intval(abs(($e - $s) / 86400));
  return $days;
}
   
?>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <title>whois</title>
  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
   <style>
   	 
   	 #myTb 
        {
            width:98%;
            border:1px solid #aaa;
            
            text-align:left;


        }
        


   </style>
  <script src="https://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
<script>
  var timeout = 1;//每条记录查询延时时间，单位：秒
var domain = "";
var strArray = "";
function getInfo()
{
    domain = document.getElementById("domain").value;
    strArray = domain.split(navigator.appName=="Netscape"?"\n":"\r\n");

    //$("#res").html("");
    $("#myTb").html("")
    $("#myTb").append("<tr><th>域名</th><th>注册时间</th><th>过期时间</th><th>日子</th></tr>")
    chaba(0);
}


   function chaba(i)
   {
      var lines = $('#domain').val().split('\n');
      var s = "";
      var tbBody = ""
    

      var num = strArray[i].replace(new RegExp(" ","g"),"").replace(new RegExp("\t","g"),"");
 			
 			if(num!="")
 			{
				//console.log(lines[i])
				 
		           $.ajax({
			           type: "POST",
			           timeout : 10000, 
			           dataType:"json",
			           url: "whois.php",
			           data: "d=" + lines[i] + "&output=json",
			           jsonpCallback:"querycallback",
			           success: function(msg){
					             console.log(msg)
					            // $("#res").html($("#res").html()  + msg.domain + "注册时间:" + msg.regdate + "过期时间:" + msg.expdate  + "还剩:" + msg.leftdate + "<br />");

					             tbBody += "<tr ><td>" + msg.domain + "</td>" + "<td>" + msg.regdate + "</td>" + "<td>" + msg.expdate + "</td>"+ "<td>" + msg.leftdate + "</td>" +"</tr>";
                        		 $("#myTb").append(tbBody);

					             i++;
					             if(i < strArray.length) {
					                   setTimeout("chaba(" + i + ")", timeout * 1000);
					              }
					             },
		          		error:function(msg){
		            		console.log(msg)
		         	 }

		        });
         }else{
         	 i++;
			 if(i < strArray.length) {
			    chaba(i);
			 }
					             

         }

     
       

   }
  </script>

 </head>
<body>
  <form enctype="multipart/form-data" method="post">
      <textarea cols=30 rows=10 name="domain" id="domain">baidu.com
sohu.com
yahoo.com
163.com
google.com</textarea>
<br/>
      <input type="button" name="" value="查吧" onclick="getInfo()">
  </form>
<br/>   
<table id="myTb" class="myTb">
<tr><th>域名</th><th>注册时间</th><th>过期时间</th><th>日子</th></tr>
</table>
 

</body>
</html>