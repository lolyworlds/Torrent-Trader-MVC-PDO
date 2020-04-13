<?php
//start add 19.01.2020 DrTech76, Exception handler verbose output
/**
* Prepares verbose output of the debug backtrace, for inclusion in trace and debug outputs and logging
* Parameters:
* @param backtrace - the output of a call to debug_backtrace() function
* @param depth - How many levels back to read the traces for
* Returns:
* @return string
**/

function get_verbose_backtrace($backtrace=null,$depth=0)
{
	$skipFirst=false;
	$output=array();
	if(!(isset($depth) and is_numeric($depth)))
	{//no limit
		$depth=0;
	}
	else
	{
		$depth=intval($depth);
	}
	
	
	if(!(isset($backtrace) and is_array($backtrace)))
	{
		if(function_exists("ReflectionFunction"))
		{
			$dbtRef=new ReflectionFunction("debug_backtrace");
			if($dbtRef->getNumberOfParameters()==2)
			{
				$backtrace=debug_backtrace(true,$depth);
			}
			else
			{
				$backtrace=debug_backtrace();
			}
		}
		else
		{
			$backtrace=debug_backtrace();
		}
		
		$skipFirst=true;//the first item will be this function so skip accordingly
	}
	
	foreach($backtrace as $currDepth=>$trace)
	{
		if($skipFirst===true and $currDepth===0)
		{
			continue;
		}
		
		if(!(isset($trace["file"]) or isset($trace["function"])))
		{
			continue;
		}
		
		$isObject=(isset($trace["class"]));
		$function=$trace["function"];
		
		//$output[]="Function: ReflectionMethod exists ".var_export(function_exists("ReflectionMethod"),true);
		//$output[]="Function: ReflectionFunction exists ".var_export(function_exists("ReflectionFunction"),true);
		if($isObject===true)
		{
			if(function_exists("ReflectionMethod"))
			{
				$funcParamsRef=new ReflectionMethod($trace["class"],$trace["function"]);
			}
			$function=$trace['class'].$trace['type'].$function;
		}
		else
		{
			if(function_exists("ReflectionFunction"))
			{
				$funcParamsRef=new ReflectionFunction($trace["function"]);
			}
		}
		
		$currTrace="\n[".$currDepth."] File: ".((isset($trace["file"]))?$trace["file"]:"Undefined")." Line: ".((isset($trace["line"]))?$trace["line"]:"Undefined");
		$currTrace.="\nFunction: ".$function;
		if(isset($trace["args"]))
		{
			$currTrace.="\nCall parameters";
		
			if(isset($funcParamsRef))
			{
				$funcParams=$funcParamsRef->getParameters();
				foreach($funcParams as $fpIndex=>$funcParam)
				{
					$currTrace.="\nParam: ".$funcParam["name"]." Passed value: ".((isset($trace["args"][$fpIndex]))?var_export($trace["args"][$fpIndex],true):"[Not passed]: ".(($funcParam->isDefaultValueAvailable()===true)?"[Using the Default value]".var_export($funcParam->getDefaultValue(),true):"[No Default value is available]"));
				}
			}
			else
			{
				foreach($trace["args"] as $fpIndex=>$funcParam)
				{
					$currTrace.="\nParam[".$fpIndex."]: Passed value: ".var_export($trace["args"][$fpIndex],true);
				}
			}
		}
		
		$output[]=$currTrace;
	}
	
	if(count($output)>0)
	{
		$output=array_merge(array("Call Stack"),$output);
	}
	
	return join("\n",$output);
}

//end add 19.01.2020 DrTech76, Exception handler verbose output

// Exception Log & Redirect
function handleUncaughtException($e){
    header("Location: exceptionerror.php"); 
    // Construct the error string
    $error = "\nUncaught Exception: ".($message=date("Y-m-d H:i:s - "));
    $error .= $e->getMessage()." in file ".$e->getFile()." on line ".$e->getLine()."\n";
	
    //start add 19.01.2020 DrTech76, add verbose trace and debug output
    $error.="\n".get_verbose_backtrace($e->getTrace());
    //end add 19.01.2020 DrTech76, add verbose trace and debug output
   
    // Log details of error in a file
    error_log($error, 3, "exception_log.txt");
}
