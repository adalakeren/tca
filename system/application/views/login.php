
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Aplikasi Cipta Krida Bahari</title>
        <style type="text/css">
            @import url(<?=base_url()?>includes/login_files/admin_login.css );
            #Layer1 {
                position:absolute;
                left:22px;
                top:86px;
                width:702px;
                height:169px;
                z-index:1;
                overflow: scroll;
            }
            .style1 {color: #CC0000}
            .style2 {color: #FFFFFF}
            .style4 {
                color: #FF9900;
                font-weight: bold;
                font-size: 16px;
            }
            .style5 {color: #99FF99}
            .style15 {	font-family: Arial, Helvetica, sans-serif;
                       font-size: 10px;
            }
            .style16 {color: #FF0000}
        </style>
        <script type="text/javascript" src="<?=base_url()?>includes/browserCheck.js"></script>

    </head>
    <body>
        <div id="wrapper">
            <div id="header">
                <div id="mambo"><img alt="Logo"
                                     src="<?=base_url()?>includes/login_files/header_text.png" /></div>
            </div>
        </div>
        <p/>
        <div class=login>
            <div class=login-form><img alt=Login src="<?=base_url()?>includes/login_files/login.gif"/>
                <form method="POST" id="loginForm" name="loginForm" class="KT_tngformerror" action="<?=base_url()?>index.php/admin_core/user_accesssLogin">
                    <div class=form-block>
                        <div class=inputlabel>Username</div>
                        <div>
                            <input type="text" name="txtUser" id="txtUser" value="" class=inputbox size=15 />
                        </div>
                        <div class=inputlabel>Password</div>
                        <div>
                            <input class=inputbox type="password" name="txtPassword" id="txtPassword" value="" size="15" />
                        </div>
                        <div class=inputlabel>Remember me:
                            <input type="checkbox" name="chkRemember" id="chkRemember" value="1" />
                        </div>
                        <div align=left>
                            <input type="submit" name="btnLogin" id="btnLogin" value="Login" />
                        </div>
                    </div>
                </form>
            </div>
            <div class=login-text>
                <div class=ctr><img height=64 alt=security src="<?=base_url()?>includes/login_files/security.png"
                                    width=64/></div>
                <P>Welcome to Cash Advance</P>
                <P>Use a valid username and password to gain access to the administration
                    console.</P>
            </div>
            <div class=clr></div>
        </div>
        <div align="center">
            <style type="text/css">
                <!--
                .style14 {
                    font-size: 10px;
                    color: #0000FF;
                }
                .style15 {
                    font-family: Arial, Helvetica, sans-serif;
                    font-size: 10px;
                }
                .style16 {color: #FF0000}
                -->
            </style>

            <script type="text/javascript">
            <!--
            document.write('<p class="accent">You\'re using ' + BrowserDetect.browser + ' ' + BrowserDetect.version + ' on ' + BrowserDetect.OS + '<br/> Please contact the administrator if the browser has a version under 3.0 </p> ');
            // -->
            </script>
            <!--div class="footer"-->
            <div align="center">
                <p><br />
                    <span class="style15">&copy; Copyright 2011 PT Cipta Krida Bahari<br />
                        Code by : <span class="style16">Arya Wirawan.</span> <br />
                Department IT &amp; Development, Ext. 7112</span></p>
            </div>
            <!--end "footer"-->
        </div>
    </body>
</html>