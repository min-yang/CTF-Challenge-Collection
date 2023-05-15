<?
const DB_NAME = 'demo_pmd'; 
const CLASS_PRE = 'pmd';
define('LIB_DIR', 'CLASS_LIB_');

function build($DbName, $Pre, $Table, $dbh){
	$ClassName = strtoupper($Pre).'_'.ucfirst($Table);
	$result = $dbh->query('SHOW FULL COLUMNS FROM '.$Pre.'_'.$Table);
	$arr = $result->fetchAll ( PDO::FETCH_ASSOC );
	$PrimaryKey = '';
	//$Date = date('Ymd');
	$Date = date('20101010');
	$Field = $Assignment = $AddValue = '';	
	foreach($arr as $k => $v){
		if($v['Key'] == 'PRI') $PrimaryKey = $v['Field'];
		$Field .= 'public $'.$v['Field'].";\n	";
		$Assignment .= '$this->'.$v['Field'].' = $rs[\''.$v['Field']."'];\n		";
		if($v['Key'] != 'PRI'){				
			$AddValue .=  '\''.$v['Field'].'\' => $this->'.$v['Field'].",\n		";
		}
	}		
	$tmp = file_get_contents('lib.temp');
	$str = str_replace(array('{Date}', '{Table}', '{PrimaryKey}', '{Field}', '{Assignment}', '{AddValue}' ,'{ClassName}'), array($Date, $Table, $PrimaryKey, $Field, $Assignment, $AddValue, $ClassName), $tmp);
	echo LIB_DIR.$DbName.'/'.$ClassName.'.php';
	file_put_contents(LIB_DIR.$DbName.'/'.$ClassName.'.php', $str);
}
if(!empty($_POST)){
	$DbName = isset($_POST['DbName']) ? $_POST['DbName'] : DB_NAME;
	$Pre = isset($_POST['Pre']) ? $_POST['Pre'] : CLASS_PRE;
	header("Content-type: text/html; charset=gbk");
	try {
	    $dbh = new PDO('mysql:host=192.168.1.55;dbname='.$DbName, 'Rongyi', 'Rongyi1234!@#$');
	    $dbh->exec( "SET NAMES utf-8");
	} catch (PDOException $e) {
	    print "Error!: " . $e->getMessage() . "<br/>";
	    die();
	}
	$ret = '';
	switch($_POST['Method']){
		case 'Tables';
			$result = $dbh->query('show tables;');
			$arr = $result->fetchAll ( PDO::FETCH_ASSOC );
			$ret = json_encode($arr);
			break;
		case 'Build':		
			if(!is_dir(LIB_DIR.$DbName)) mkdir ( LIB_DIR.$DbName, 0777 );			
			if($_POST['Table'] != 'All'){				
				$_POST['Table'] = substr($_POST['Table'], strlen($Pre.'_'));
				build($DbName, $Pre, $_POST['Table'], $dbh);	
			}else{
				$result = $dbh->query('show tables;');
				$arr = $result->fetchAll ( PDO::FETCH_ASSOC );
				$OtherInterface = array();
				$OtherPublic = array();
				$OtherInclude = array();
				foreach($arr as $k => $v){
					$Table = substr($v['Tables_in_'.$DbName], strlen($Pre.'_'));
					$OtherPublic[] = 'public $'.ucfirst($Table).'Obj;';
					$OtherInclud[] = '$this->'.ucfirst($Table).'Obj = '.strtoupper($Pre).'_'.ucfirst($Table).'::get_instance();';		
					$OtherInterface[] = 'use Model\\'.strtoupper($Pre).'_'.ucfirst($Table).';';
					build($DbName, $Pre, $Table, $dbh);
				}
				$OtherStr = implode("\n", $OtherInterface)."\n\n";
				$OtherStr .= implode("\n", $OtherPublic)."\n\n";
				$OtherStr .= implode("\n", $OtherInclud)."\n\n";
				file_put_contents(LIB_DIR.$DbName.'/other.php', $OtherStr);
			}		
			break;
	}
	echo $ret;
	exit;
}


?>
<!DOCTYPE html>
<html>
<head>
	<title>类库生成器</title>
	<meta charset="UTF-8">	
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.css" rel="stylesheet">
	<style type="text/css">
		a:link{
			padding: 0px 10px 0px 10px;
		}
	</style>
</head>
<body style="padding-top: 100px;">
	<div class="container" style="width: 300px;">
	<h1>类库生成器</h1>
	<form>
		<div class="form-group">
		    <label for="exampleInputEmail1">数据库名：</label>
		    <input class="form-control" id="dbname" value="<?=DB_NAME?>">
		</div>
		<div class="form-group">
		<label for="exampleInputEmail1">类库前缀：</label>
		<div class="input-group">
		    
		    <input class="form-control" id="classPre" value="<?=CLASS_PRE?>">
			<span class="input-group-btn">
				<button class="btn btn-default" type="button" id="flushBtn">刷新表</button>
			</span>
			</div>
		</div>
		<div class="form-group">
		    <label for="exampleInputEmail1">选择生成表：</label>
		    <select id="table" class="form-control">
				
			</select>
		</div>	
		<button type="button" id="build" class="btn btn-success btn-block">生成</button>
	</form>
	</div>
<script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript">
	$(function(){
		flushFunc();
		$('#flushBtn').click(function(){
			flushFunc();
		})
		$('#build').click(function(){
			$.post('build.php', {'Method' : 'Build', 'Table' : $('#table').val(), 'DbName' : $('#dbname').val(), 'Pre' : $('#classPre').val()}, function(data){
				//console.log(data)
				alert('生成完成');
			})
		})
	})
	var flushFunc = function(){
		$.post('build.php', {'Method' : 'Tables', 'DbName' : $('#dbname').val(), 'Pre' : $('#classPre').val()}, function(data){
			$('#table').html('');
			$('#table').append('<option value="All">全部表</option>');
			$.each(data, function(k, v){
				$('#table').append('<option>'+v["Tables_in_"+$('#dbname').val()]+'</option>');
			})
		}, 'json')
	}
</script>
</body>
</html>