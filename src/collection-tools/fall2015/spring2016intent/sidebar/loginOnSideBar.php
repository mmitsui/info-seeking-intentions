<?php
header("Location: ../login.php?redirect=sidebar/sidebar.php");
exit(1);
//	session_name('XULSession'); // Set session name
	session_start();
	require_once('../core/Base.class.php');
	require_once('../core/Util.class.php');
	$base = Base::getInstance();
        if ($base->isSessionActive()){
					Util::getInstance()->saveAction('Migrating from loginOnSideBar',0,$base);
					//echo "Redirecting to sidebar.php";
          header("Location: sidebar.php");
        }else {

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
			<link rel="Coagmento icon" type="image/x-icon" href="http://www.coagmento.org/spring2016intent/img/favicon.ico">
			<title>Coagmento</title>
			<link rel="stylesheet" href="css/styles.css" type="text/css" />
			<style type="text/css">
				#container{
					width: 300px;
					margin: 0px auto;
				}
			</style>
			</head>
			<body>
    <div id="container">
    	    <form action="loginOnSideBarAux.php" method=post>
    			<table>
    				<tr><td align=center colspan=2 <span style="font-weight:bold;">Login to your Account</span></td></tr>
    				<tr><td colspan=2><br/></td></tr>
    				<tr><td> Username </td><td> <input name="userName" type="text" size=20 /></td></tr>
    				<tr><td> Password </td><td> <input name="password" type="password" size=20 /></td></tr>
    				<tr><td colspan=2 align="center"><input type="submit" value="Login"/></td></tr>
    				<tr><td colspan=2><br/></td></tr>
    			</table></form>
    			<table>
    				<tr><td>Forget your password? <a style="color:blue;text-decoration:underline;cursor:pointer;font-size:12px;" onclick="javascript:window.open('http://coagmento.org/spring2016intent/services/generatePassword.php','Forgot Password','directories=no, toolbar=no, location=no, status=no, menubar=no, resizable=no,scrollbars=yes,width=520,height=300,left=400');return false;">Click here</a> to generate a new one.</td></tr>
    				<tr><td colspan=2><br/></td></tr>
    				<tr><td>
    	                    </td>
    	                    <td>

    	                    </td>
    	                </tr>
    			</table>
    </div>
                         </body>
                        </html>
<?php
    }
?>
