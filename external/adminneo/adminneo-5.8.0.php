<?php
/**
 * AdminNeo - Powerful database manager in a single PHP file
 * v5.8.0
 *
 * Compiled with
 * drivers:   mssql, mysql
 * languages: all
 * themes:    all
 * config:    no
 *
 * @link https://www.adminneo.org/
 *
 * @author Peter Knut
 * @author Jakub Vrana (https://www.vrana.cz/)
 *
 * @copyright 2007-2025 Jakub Vrána
 * @copyright 2024-2025 Peter Knut
 *
 * @license Apache License, Version 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @license GNU General Public License, version 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 */namespace
AdminNeo;use
Exception;use
stdClass;use
PDO;use
PDOStatement;use
mysqli;use
mysqli_result;abstract
class
Plugin{protected$admin;protected$config;protected$settings;protected$locale;function
inject($_a,Config$Wb,Settings$N,Locale$Eg){$this->admin=$_a;$this->config=$Wb;$this->settings=$N;$this->locale=$Eg;}}abstract
class
Origin
extends
Plugin{private$errors=[];private
static$instance=null;static
function
create(array$Wb=[],array$Zi=[]){if(self::$instance)die("Admin instance already exists.\n");$_a=new
static();if(!$Wb&&file_exists("adminneo-config.php")){$Wb=include_once("adminneo-config.php");if(!is_array($Wb)){$Wb=[];$xg="href=https://github.com/adminneo-org/adminneo#configuration ".target_blank();$_a->addError(lang(0,"<b>adminneo-config.php</b>")." <a $xg>".lang(1)."</a>");}}$Wb=new
Config($Wb);$N=new
Settings($Wb);if(!$Zi&&file_exists("adminneo-plugins.php")){$Zi=include_once("adminneo-plugins.php");if(!is_array($Zi)){$Zi=[];$xg="href=https://github.com/adminneo-org/adminneo#plugins ".target_blank();$_a->addError(lang(0,"<b>adminneo-plugins.php</b>")." <a $xg>".lang(1)."</a>");}}self::$instance=$Zi?new
Pluginer($_a,$Zi):$_a;$_a->inject(self::$instance,$Wb,$N,Locale::get());foreach($Zi
as$Yi)$Yi->inject(self::$instance,$Wb,$N,Locale::get());return
self::$instance;}static
function
get(){if(!self::$instance)die("Admin instance not found. Create instance by Admin::create() method at first.\n");return
self::$instance;}protected
function
__construct(){}function
getConfig(){return$this->config;}function
getSettings(){return$this->settings;}abstract
function
getOperators();function
getLikeOperator(){return
Driver::get()->getLikeOperator();}function
getRegexpOperator(){return
null;}function
init(){}function
addError($j){$this->errors[]=$j;}function
getErrors(){return$this->errors;}abstract
function
getServiceTitle();function
getCredentials(){$M=$this->config->getServer(SERVER);return[$M?$M->getServer():SERVER,$_GET["username"],get_password()];}function
verifyDefaultPassword($E){$Pe=$this->config->getDefaultPasswordHash();if($Pe===null||$Pe==="")return
lang(2);elseif(!password_verify($E,$Pe))return
lang(3);return
true;}function
authenticate($U,$E){if($E==""){$Pe=$this->config->getDefaultPasswordHash();if($Pe===null)return
lang(4,target_blank());else
return$Pe==="";}return
true;}function
getPrivateKey($ic=false){return
get_private_key($ic);}function
getBruteForceKey(){return$_SERVER["REMOTE_ADDR"];}function
getServerName($M,$Pj=true,$Qd=null){if($M==""){if(!$Pj)return"";$M=Connection::exists()?Connection::get()->getDefaultServerName():"";if($M=="")return$Qd!==null?$Qd:lang(5);$_k=null;}else$_k=$this->config->getServer($M);return$_k?$_k->getName():preg_replace('~^https?://~',"",$M);}abstract
function
getDatabase();function
getDatabases($je=true){$g=$this->filterListWithWildcards(get_databases($je),$this->config->getHiddenDatabases(),false,Driver::get()->getSystemDatabases());if(DB!=""&&!in_array(DB,$g))array_unshift($g,DB);return$g;}function
getSchemas($Dh=false){$Ue=$this->config->getHiddenSchemas();if($Dh&&!in_array("__system",$Ue))$Ue[]="__system";$kk=$this->filterListWithWildcards(schemas(),$Ue,false,Driver::get()->getSystemSchemas());if(isset($_GET["ns"])&&$_GET["ns"]!=""&&!in_array($_GET["ns"],$kk))array_unshift($kk,$_GET["ns"]);return$kk;}function
getCollations(array$Tf=[]){$Zm=$this->config->getVisibleCollations();$de=$Zm?array_merge($Zm,$Tf):[];return$this->filterListWithWildcards(collations(),$de,true);}private
function
filterListWithWildcards(array$Y,array$de,$Vf,array$ul=[]){if(!$Y||!$de)return$Y;$s=array_search("__system",$de);if($s!==false){unset($de[$s]);$de=array_merge($de,$ul);}array_walk($de,function(&$X){$X=str_replace('\\*',".*",preg_quote($X,"~"));});$Si='~^('.implode("|",$de).')$~';return$this->filterListWithPattern($Y,$Si,$Vf);}private
function
filterListWithPattern(array$Y,$Si,$Vf){$H=[];foreach($Y
as$u=>$X){if(is_array($X)){if($kl=$this->filterListWithPattern($X,$Si,$Vf))$H[$u]=$kl;}elseif(($Vf&&preg_match($Si,$X))||(!$Vf&&!preg_match($Si,$X)))$H[$u]=$X;}return$H;}abstract
function
getQueryTimeout();function
sendHeaders(){}function
updateCspHeader(array&$nc){}function
printFavicons(){$Gb=validate_color_variant($this->config->getColorVariant());echo"<link rel='icon' type='image/x-icon' href='",link_files("favicon-$Gb.ico",[]),"' sizes='32x32'>\n","<link rel='icon' type='image/svg+xml' href='",link_files("favicon-$Gb.svg",[]),"'>\n","<link rel='apple-touch-icon' href='",link_files("apple-touch-icon-$Gb.png",[]),"'>\n";}abstract
function
printToHead();function
getCssUrls(){$Gm=$this->config->getCssUrls();foreach(["adminneo.css","adminneo-light.css","adminneo-dark.css"]as$n){if(file_exists($n))$Gm[]="$n?v=".filemtime($n);}return$Gm;}function
isLightModeForced(){return$this->isColorSchemeForced(false);}function
isDarkModeForced(){return$this->isColorSchemeForced(true);}private
function
isColorSchemeForced($sc){$jh=$sc?Settings::$ColorSchemeDark:Settings::$ColorSchemeLight;$kh=$sc?Settings::$ColorSchemeLight:Settings::$ColorSchemeDark;$Zd=file_exists("adminneo-$jh.css");$ae=file_exists("adminneo-$kh.css");if($Zd&&!$ae)return
true;return$this->settings->getColorScheme()==$jh&&!($Zd
xor$ae);}function
getJsUrls(){$Gm=$this->config->getJsUrls();$n="adminneo.js";if(file_exists($n))$Gm[]="$n?v=".filemtime($n);return$Gm;}abstract
function
printLoginForm();function
getLoginFormRow($Ud,$dg,$k){if($dg)return"<tr><th>$dg</th><td>$k</td></tr>\n";else
return"$k\n";}function
printLogout(){echo"<div class='logout'>","<form action='' method='post'>\n","<span title='",lang(6),"'>",h($_GET["username"]),"</span>","<input type='submit' class='button' name='logout' value='",lang(7),"' id='logout'>",input_token(),"</form>","</div>\n";}function
getTableName(array$yl){return
h($yl["Name"]);}abstract
function
getFieldName(array$k,$C=0);function
formatComment($Ob){return
h($Ob);}abstract
function
printTableMenu(array$yl,$yf);function
getForeignKeys($P){return
foreign_keys($P);}function
getBackwardKeys($P,$wl){if(!$this->settings->isRelationLinks())return[];$K=backward_keys($P);$Xf=[];foreach($K
as$J){$r=$J["table_schema"].".".$J["table_name"];$Xf[$r]["schema"]=$J["table_schema"];$Xf[$r]["table"]=$J["table_name"];$Xf[$r]["constraints"][$J["constraint_name"]][$J["column_name"]]=$J["referenced_column_name"];}foreach($Xf
as$r=>$u){$A=$this->admin->getTableName(table_status1($u["table"],true));if($A!=""){$mk=preg_quote($wl);$wk="(:|\\s*-)?\\s+";$Xf[$r]["name"]=(preg_match("(^$mk$wk(.+)|^(.+?)$wk$mk\$)iu",$A,$z)?$z[2].$z[3]:$A);}else
unset($Xf[$r]);}return$Xf;}function
printBackwardKeys(array$Wa,array$J){foreach($Wa
as$u){foreach($u["constraints"]as$ac){$Ug=preg_replace('~&ns=[^&]+&~',"&ns=".urldecode($u["schema"])."&",ME);$x=$Ug.'select='.urlencode($u["table"]);$q=0;foreach($ac
as$b=>$W){if(!isset($J[$W]))continue
2;$x
.=where_link($q++,$b,$J[$W]);}$A=preg_replace('(^'.preg_quote($_GET["select"]).(substr($_GET["select"],-1)=="s"?"?":"").'_)',"_",$u["name"]);$S=implode(", ",array_keys($ac));echo"<a href='".h($x)."' title='".h($S)."'>".h($A)."</a>";$x=$Ug.'edit='.urlencode($u["table"]);foreach($ac
as$b=>$W)$x
.="&preset".urlencode("[".bracket_escape($b)."]")."=".urlencode($J[$W]);echo"<a href='".h($x)."' title='".lang(8)."'>",icon_solo("add"),"</a> ";}}}abstract
function
formatSelectQuery($G,$cl,$Pd=false);abstract
function
formatMessageQuery($G,$Xl,$Pd=false);abstract
function
formatSqlCommandQuery($G);function
printAfterSqlCommand(){}abstract
function
getTableDescriptionFieldName($P);abstract
function
fillForeignDescriptions(array$K,array$me);function
getFieldValueLink($W,$k){if(is_mail($W))return"mailto:$W";if(is_web_url($W))return$W;return
null;}abstract
function
formatSelectionValue($W,$x,$k,$wi);abstract
function
formatFieldValue($X,array$k);abstract
function
printTableStructure(array$l);abstract
function
printTablePartitions(array$Ii);abstract
function
printRelatedTables(array$R);abstract
function
printTableIndexes(array$t,array$yl);abstract
function
printSelectionColumns(array$L,array$c);abstract
function
printSelectionSearch(array$Z,array$c,array$t);abstract
function
printSelectionOrder(array$C,array$c,array$t);abstract
function
printSelectionLimit($w);abstract
function
printSelectionLength($Sl);abstract
function
printSelectionAction(array$t);function
isDataEditAllowed(){return!information_schema(DB);}abstract
function
processSelectionColumns(array$c,array$t);abstract
function
processSelectionSearch(array$l,array$t);abstract
function
processSelectionOrder(array$l,array$t);function
processSelectionLimit(){if(!isset($_GET["limit"]))return$this->settings->getRecordsPerPage();return$_GET["limit"]!=""?(int)$_GET["limit"]:0;}abstract
function
processSelectionLength();abstract
function
getFieldFunctions(array$k);abstract
function
getFieldInput($P,array$k,$Oa,$X,$p);function
getFieldInputHint($P,array$k,$X){return
support("comment")?$this->admin->formatComment($k["comment"]):"";}abstract
function
processFieldInput(array$k,$X,$p="");function
detectJson($Vd,&$X,$kj=null){if(is_array($X)){$he=JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|($this->config->isJsonValuesAutoFormat()?JSON_PRETTY_PRINT:0);$X=json_encode($X,$he);return
true;}$he=JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|($kj?JSON_PRETTY_PRINT:0);if(preg_match('~^jsonb?$~',$Vd)){if($X!=null&&$kj!==null&&$this->config->isJsonValuesAutoFormat())$X=json_encode(json_decode($X),$he);return
true;}if(!$this->config->isJsonValuesDetection())return
false;if(is_string($X)&&$X!=""&&preg_match('~varchar|text|character varying|String|keyword~',$Vd)&&($X[0]=="{"||$X[0]=="[")&&($Rf=json_decode($X))){if($kj!==null&&$this->config->isJsonValuesAutoFormat())$X=json_encode($Rf,$he);return
true;}return
false;}function
getServerVariables(){return
show_variables();}function
getStatusVariables(){return
show_status();}abstract
function
getDumpOutputs();abstract
function
getDumpFormats();abstract
function
sendDumpHeaders($ff,$nh=false);function
dumpDatabase($vc){}abstract
function
dumpTable($P,$jl,$Wm=0);abstract
function
dumpData($P,$jl,$G);abstract
function
getImportFilePath();abstract
function
printDatabaseMenu();abstract
function
printNavigation($hh);abstract
function
printDatabaseSwitcher($hh);function
printTablesFilter(){echo"<div class='tables-filter jsonly'>"."<input id='tables-filter' type='search' class='input' autocomplete='off' placeholder='".lang(9)."'>".script("initTablesFilter(".json_encode($this->admin->getDatabase(),JSON_HEX_TAG).");")."</div>\n";}abstract
function
printTableList(array$R);function
getSettingsRows($He){$N=[];if($He==1){$B=get_language_options();if($B)$N["lang"]="<tr><th id='label-language'>".lang(10)."</th>"."<td>".html_select("lang",get_language_options(),Locale::get()->getLanguage(),"","label-language")."</td></tr>\n";$cm=get_theme_titles($this->config->getColorVariant());if(count($cm)>1){list($Ul)=validate_theme($this->config->getTheme(),$this->config->getColorVariant());$B=[""=>lang(11)." ($cm[$Ul])"]+$cm;$N["theme"]="<tr><th id='label-theme'>".lang(12)."</th>"."<td>".html_select("theme",$B,($ta=$this->settings->getParameter("theme"))!==null?$ta:"","","label-theme")."</td></tr>\n";}$B=[""=>lang(13),Settings::$ColorSchemeLight=>lang(14),Settings::$ColorSchemeDark=>lang(15)];$N["colorScheme"]="<tr><th>".lang(16)."</th>"."<td>".html_radios("colorScheme",$B,($ta=$this->settings->getParameter("colorScheme"))!==null?$ta:"")."</td></tr>\n";}elseif($He==2){$B=[""=>lang(11),true=>lang(17),false=>lang(18),];$i=$B[$this->config->isRelationLinks()];$B[""].=" ($i)";$N["relationLinks"]="<tr><th>".lang(19)."</th>"."<td>".html_radios("relationLinks",$B,($ta=$this->settings->getParameter("relationLinks"))!==null?$ta:"")."<span class='input-hint'>".lang(20)."</span>"."</td></tr>\n";$i=$this->config->getRecordsPerPage();$B=[""=>lang(11)." ($i)","20","30","50","70","100",];$N["recordsPerPage"]="<tr><th id='label-records'>".lang(21)."</th>"."<td>".html_select("recordsPerPage",$B,($ta=$this->settings->getParameter("recordsPerPage"))!==null?$ta:"","","label-records")."<span class='input-hint'>".lang(22)."</span>"."</td></tr>\n";$i=($ta=$this->config->getEnumAsSelectThreshold())!==null?$ta:lang(23);$B=[""=>lang(11)." ($i)",-1=>lang(23),0=>lang(24),3=>lang(25,3),5=>lang(25,5),10=>lang(25,10),20=>lang(25,20),];$N["enumAsSelectThreshold"]="<tr><th id='label-enum'>".lang(26)."</th>"."<td>".html_select("enumAsSelectThreshold",$B,($ta=$this->settings->getParameter("enumAsSelectThreshold"))!==null?$ta:"","","label-enum",true)."<span class='input-hint'>".lang(27)."</span>"."</td></tr>\n";}return$N;}abstract
function
getForeignColumnInfo(array$me,$b);}class
Pluginer{private
static$InternalMethods=["inject"=>true,"getConfig"=>true,];private
static$AppendMethods=["getErrors"=>true,"getFieldFunctions"=>true,"getDumpOutputs"=>true,"getDumpFormats"=>true,"getSettingsRows"=>true,];private$plugins;private$hooks=[];function
__construct(Origin$_a,array$Zi){$this->plugins=$Zi;foreach(get_class_methods('\AdminNeo\Origin')as$fh){$this->hooks[$fh]=[];if(!(isset(self::$InternalMethods[$fh])?self::$InternalMethods[$fh]:false)){foreach($Zi
as$Yi){if(method_exists($Yi,$fh))$this->hooks[$fh][]=$Yi;}}if(isset(self::$AppendMethods[$fh])?self::$AppendMethods[$fh]:false)array_unshift($this->hooks[$fh],$_a);else$this->hooks[$fh][]=$_a;}}function
getPlugins(){return$this->plugins;}function
__call($A,array$Di){$Ja=isset(self::$AppendMethods[$A])?self::$AppendMethods[$A]:false;$H=$Ja?[]:null;assert(isset($this->hooks[$A]),"Calling unknown plugin method: $A");foreach($this->hooks[$A]as$Yi){$X=call_user_func_array([$Yi,$A],$Di);if($X!==null){if($Ja)$H+=$X;else
return$X;}}return$H;}function
updateCspHeader(array&$nc){$this->__call(__FUNCTION__,[&$nc]);}function
detectJson($Vd,&$X,$kj=null){return$this->__call(__FUNCTION__,[$Vd,&$X,$kj]);}}class
Admin
extends
Origin{function
getOperators(){return
Driver::get()->getOperators();}function
getServiceTitle(){return"<a href='".h(HOME_URL)."'><svg role='img' class='logo' width='133' height='28'><desc>AdminNeo</desc>"."<use href='".link_files("logo.svg",[])."#logo'/></svg></a>";}function
getDatabase(){return
DB;}function
getQueryTimeout(){return
2;}function
printToHead(){echo"<link rel='stylesheet' href='",link_files("jush.css",[]),"'>";if(!$this->admin->isLightModeForced())echo"<link rel='stylesheet' ".(!$this->admin->isDarkModeForced()?"media='(prefers-color-scheme: dark)' ":"")."href='",link_files("jush-dark.css",[]),"'>\n";echo
script_src(link_files("jush.js",[]),true);}function
printLoginForm(){$ad=Drivers::getList();$Ak=$this->config->getServerPairs($ad);$M=SERVER?:$this->config->getDefaultServer();echo"<table class='box box-light'>\n";if($Ak)echo$this->admin->getLoginFormRow('server',lang(5),"<select name='auth[server]'>".optionlist($Ak,$M,true)."</select>");else{$Yc=DRIVER?:$this->config->getDefaultDriver($ad);if(count($ad)>1)echo$this->admin->getLoginFormRow('driver',lang(28),html_select("auth[driver]",$ad,$Yc).script("initLoginDriver(qsl('select'));",""));else
echo$this->admin->getLoginFormRow('driver','',input_hidden("auth[driver]",$Yc));echo$this->admin->getLoginFormRow('server',lang(5),"<input class='input' name='auth[server]' value='".h($M)."' title='".lang(29)."' placeholder='localhost' autocapitalize='off'>");}echo$this->admin->getLoginFormRow('username',lang(6),'<input class="input" name="auth[username]" id="username" value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'),$this->admin->getLoginFormRow('password',lang(30),'<input type="password" class="input" name="auth[password]" autocomplete="current-password">');if(!$Ak){$vc=isset($_GET["db"])?$_GET["db"]:$this->config->getDefaultDatabase();echo$this->admin->getLoginFormRow('db',lang(31),'<input class="input" name="auth[db]" value="'.h($vc).'" autocapitalize="off">');}echo"</table>\n","<p>","<input type='submit' class='button default' value='".lang(32)."'>",checkbox("auth[permanent]",1,$_COOKIE["neo_permanent"],lang(33)),"</p>\n";}function
getFieldName(array$k,$C=0){$T=$k["full_type"].($k["null"]?" NULL":"");$Ob=$k["comment"];$wk=$T&&$Ob!=""?": ":"";return'<span title="'.h($T.$wk.$Ob).'">'.h($k["field"]).'</span>';}function
printTableMenu(array$yl,$yf){echo'<p class="links top-tabs">';$yg=[];$sk=($this->settings->isSelectionPreferred()&&!$this->settings->isNavigationReversed())||(!$this->settings->isSelectionPreferred()&&$this->settings->isNavigationReversed());if($sk)$yg["select"]=[lang(34),"data"];if(support("table")||support("indexes"))$yg["table"]=[lang(35),"structure"];if(!$sk)$yg["select"]=[lang(34),"data"];$P=$yl["Name"];$Mf=false;if(support("table")){$Mf=is_view($yl);if(!$Mf){if($P!="")$yg["create"]=[lang(36),"edit"];}elseif(support("view"))$yg["view"]=[lang(37),"edit"];}if($yf!==null)$yg["edit"]=[lang(8),"item-add"];$Di=$yf?"&".http_build_query($yf):"";foreach($yg
as$u=>$W)echo" <a href='",h(ME),"$u=",urlencode($P),($u=="edit"?$Di:""),"'",bold(isset($_GET[$u])),">",icon($W[1]),"$W[0]</a>";echo
doc_link([DIALECT=>Driver::get()->tableHelp($P,$Mf)],icon("help").lang(38)),"\n";}function
formatSelectQuery($G,$cl,$Pd=false){$pl=support("sql");$dn=!$Pd?Driver::get()->warnings():null;if($pl)$G
.=";";$sl=DIALECT=="elastic"||DIALECT=="mongo"?"json":DIALECT;$I="<pre><code class='jush-$sl'>".h(str_replace("\n"," ",$G))."</code></pre>\n";$I
.="<p class='links'>";if($pl)$I
.="<a href='".h(ME)."sql=".urlencode($G)."'>".icon("edit").lang(39)."</a>";if($dn)$I
.="<a href='#warnings' class='toggle'>".lang(40).icon_chevron_down()."</a>";$I
.=" <span class='time'>(".format_time($cl).")</span>";$I
.="</p>\n";if($dn){$I
.=script("initToggles(qsl('p'));");$I
.="<div id='warnings' class='warnings hidden'>\n$dn\n</div>\n";}return$I;}function
formatMessageQuery($G,$Xl,$Pd=false){restart_session();$We=&get_session("queries");if(!isset($We[$_GET["db"]]))$We[$_GET["db"]]=[];if(strlen($G)>1e6)$G=preg_replace('~[\x80-\xFF]+$~','',substr($G,0,1e6))."\n…";$We[$_GET["db"]][]=[$G,time(),$Xl];$pl=support("sql");$dn=!$Pd?Driver::get()->warnings():null;$Yk="sql-".count($We[$_GET["db"]]);$en="warnings-".count($We[$_GET["db"]]);$I=" ";if($dn)$I
.="<a href='#$en' class='toggle'>".lang(40).icon_chevron_down()."</a>, ";$wj=support("sql")?lang(41):lang(42);$I
.="<a href='#$Yk' class='toggle'>$wj".icon_chevron_down()."</a>";$I
.=" <span class='time'>".@date("H:i:s")."</span>\n";if($dn)$I
.="<div id='$en' class='warnings hidden'>\n$dn</div>\n";$I
.="<div id='$Yk' class='hidden'>\n";$sl=DIALECT=="elastic"||DIALECT=="mongo"?"json":DIALECT;$I
.="<pre><code class='jush-$sl'>".truncate_utf8($G,1000)."</code></pre>\n";$I
.="<p class='links'>";if($pl)$I
.="<a href='".h(str_replace("db=".urlencode(DB),"db=".urlencode($_GET["db"]),ME).'sql=&history='.(count($We[$_GET["db"]])-1))."'>".icon("edit").lang(39)."</a>";if($Xl)$I
.=" <span class='time'>($Xl)</span>";$I
.="</p>\n";$I
.="</div>\n";return$I;}function
formatSqlCommandQuery($G){if(preg_match('~^DELIMITER\s~i',$G))return"";return
truncate_utf8($G,1000);}function
getTableDescriptionFieldName($P){return"";}function
fillForeignDescriptions(array$K,array$me){return$K;}function
formatSelectionValue($W,$x,$k,$wi){if($W===null)$Rl="<i>NULL</i>";elseif(!$k)$Rl=$W;elseif(preg_match("~char|binary|boolean~",$k["type"])&&!preg_match("~var~",$k["type"]))$Rl="<code>$W</code>";elseif(is_blob($k)&&!is_utf8($W))$Rl="<i>".lang(43,strlen($wi))."</i>";elseif($this->admin->detectJson($k["full_type"],$wi))$Rl="<code class='jush-json'>$W</code>";else$Rl=$W;if($x)$Rl="<a href='".h($x)."'".(is_web_url($x)?target_blank():"").">$Rl</a>";return$Rl;}function
formatFieldValue($X,array$k){return$X;}function
printTableStructure(array$l){echo"<div class='scrollable'>\n","<table class='nowrap'>\n","<thead><tr>","<th>",lang(44),"</th>","<td>",lang(45),"</td>","<td>",lang(46),"</td>";if(support("comment"))echo"<td>",lang(47),"</td>";echo"</tr></thead>\n";$Mm=Driver::get()->getUserTypes();foreach($l
as$k){echo"<tr>","<th>",h($k["field"]),"</th>","<td>";$T=h($k["full_type"]);if(in_array($T,$Mm))echo"<a href='".h(ME.'type='.urlencode($T))."'>$T</a>";else
echo$T;if($k["null"])echo" <i>NULL</i>";if($k["auto_increment"])echo" <i>".lang(48)."</i>";$i=h($k["default"]);if(isset($k["default"]))echo" <span title='".lang(49)."'>[<b>",$k["generated"]?"<code class='jush-".DIALECT."'>$i</code>":$i,"</b>]</span>";echo"</td>","<td>",h($k["collation"]),"</td>";if(support("comment"))echo"<td>",$this->admin->formatComment($k["comment"]),"</td>";echo"\n";}echo"</table>\n","</div>\n";}function
printTablePartitions(array$Ii){$Kk=isset($Ii["partition_names"]);echo"<p>","<code class='jush-".DIALECT."'>BY {$Ii["partition_by"]} ({$Ii["partition"]})</code>";if(!$Kk&&isset($Ii["partitions"]))echo" ".lang(50).": ".h($Ii["partitions"]);echo"</p>";if($Kk){echo"<table>\n","<thead><tr><th>".lang(51)."</th><td>".lang(52)."</td></tr></thead>\n";foreach($Ii["partition_names"]as$u=>$A){echo"<tr><th>";if(DIALECT=="pgsql")echo"<a href='",h(ME."table=".urlencode($A)),"'>";echo
h($A);if(DIALECT=="pgsql")echo"</a>";echo"</th><td>".h($Ii["partition_values"][$u])."\n";}echo"</table>\n";}}function
printRelatedTables(array$R){echo"<ul class='links'>\n";foreach($R
as$J){$x=preg_replace('~ns=[^&]*~',"ns=".urlencode($J["ns"]),ME);echo"<li><a href='",h($x."table=".urlencode($J["table"])),"'>",icon("structure");if($J["ns"]!=$_GET["ns"])echo"<b>".h($J["ns"])."</b>.";echo
h($J["table"]),"</a>";}echo"</ul>\n";}function
printTableIndexes(array$t,array$yl){$_c=first(Driver::get()->getIndexAlgorithms($yl));$Gi=false;foreach($t
as$s){if(isset($s["partial"])?$s["partial"]:false){$Gi=true;break;}}echo"<table>\n","<thead><tr>","<th>",lang(45),"</th>","<td>",lang(53)," (",lang(54),")</td>";if($Gi)echo"<td>",lang(55),"</td>";echo"</tr></thead>\n";foreach($t
as$A=>$s){ksort($s["columns"]);$mj=[];foreach($s["columns"]as$u=>$W)$mj[]="<i>".h($W)."</i>".($s["lengths"][$u]?"(".h($s["lengths"][$u]).")":"").($s["descs"][$u]?" DESC":"");echo"<tr title='",h($A),"'>","<th>",h($s["type"]);if(isset($s['algorithm'])&&$s['algorithm']!=$_c)echo" (",h($s['algorithm']),")";echo"</th>","<td>",implode(", ",$mj),"</td>";if($Gi){echo"<td>";if($s['partial'])echo"<code class='jush-",DIALECT,"'>WHERE ",h($s['partial']),"</code>";echo"</td>";}echo"</tr>\n";}echo"</table>\n";}function
printSelectionColumns(array$L,array$c){print_fieldset_start("select",lang(56),"columns",(bool)$L,true);$L[""]=[];$q=0;foreach($L
as$u=>$W){$W=isset($_GET["columns"][$u])?$_GET["columns"][$u]:[];$b=select_input("name='columns[$q][col]'",$c,isset($W["col"])?$W["col"]:null,$u!==""?"selectFieldChange":"selectAddRow");echo"<div ",($u!=""?"":"class='no-sort'"),">",icon("handle","handle jsonly");if(Driver::get()->getFunctions()||Driver::get()->getGrouping())echo
html_select("columns[$q][fun]",[-1=>""]+array_filter([lang(57)=>Driver::get()->getFunctions(),lang(58)=>Driver::get()->getGrouping()]),isset($W["fun"])?$W["fun"]:null),help_script_command("value && value.replace(/ |\$/, '(') + ')'",true),script("qsl('select').onchange = (event) => { ".($u!==""?"":" qsl('select, input:not(.remove)', event.target.parentNode).onchange();")." };",""),"($b)";else
echo$b;echo" <button class='button light remove jsonly' title='",lang(59),"'>",icon_solo("remove"),"</button>",script("qsl('#fieldset-select .remove').onclick = selectRemoveRow;",""),"</div>\n";$q++;}print_fieldset_end("select",true);}function
printSelectionSearch(array$Z,array$c,array$t){print_fieldset_start("search",lang(60),"search",(bool)$Z);foreach($t
as$q=>$s){if($s["type"]=="FULLTEXT"){echo"<div>(<i>".implode("</i>, <i>",array_map('AdminNeo\h',$s["columns"]))."</i>) AGAINST","<input type='text' class='input' name='fulltext[$q]' value='".h(isset($_GET["fulltext"][$q])?$_GET["fulltext"][$q]:null)."'>",script("qsl('input').oninput = selectFieldChange;","");if(DIALECT=='sql')echo
checkbox("boolean[$q]",1,isset($_GET["boolean"][$q]),"BOOL");echo"</div>\n";}}$pb="this.parentNode.firstChild.onchange();";foreach(array_merge((array)$_GET["where"],[[]])as$q=>$W){if(!$W||("$W[col]$W[val]"!=""&&in_array($W["op"],$this->getOperators())))echo"<div>",select_input(" name='where[$q][col]'",$c,$W["col"],($W?"selectFieldChange":"selectAddRow"),"(".lang(61).")"),html_select("where[$q][op]",$this->getOperators(),$W["op"],$pb),"<input type='text' class='input' name='where[$q][val]' value='".h($W["val"])."'>",script("mixin(qsl('input'), {oninput: function () { $pb }, onkeydown: selectSearchKeydown});","")," <button class='button light remove jsonly' title='".lang(59)."'>",icon_solo("remove"),"</button>",script('qsl("#fieldset-search .remove").onclick = selectRemoveRow;',""),"</div>\n";}print_fieldset_end("search");}function
printSelectionOrder(array$C,array$c,array$t){print_fieldset_start("sort",lang(62),"sort",(bool)$C,true);$_GET["order"][""]="";$q=0;foreach((array)$_GET["order"]as$u=>$W){if($u!=""&&$W=="")continue;echo"<div ",($u!=""?"":"class='no-sort'"),">",icon("handle","handle jsonly"),select_input("name='order[$q]'",$c,$W,$u!==""?"selectFieldChange":"selectAddRow")," ",checkbox("desc[$q]",1,isset($_GET["desc"][$u]),lang(63))," <button class='button light remove jsonly' title='",lang(59),"'>",icon_solo("remove"),"</button>",script('qsl("#fieldset-sort .remove").onclick = selectRemoveRow;',""),"</div>\n";$q++;}print_fieldset_end("sort",true);}function
printSelectionLimit($w){echo"<fieldset><legend>".lang(64)."</legend><div class='fieldset-content'>","<input type='number' name='limit' class='input size' value='$w'>",script("qsl('input').oninput = selectFieldChange;",""),"</div></fieldset>\n";}function
printSelectionLength($Sl){if($Sl!==null)echo"<fieldset><legend>".lang(65)."</legend><div class='fieldset-content'>","<input type='number' name='text_length' class='input size' value='".h($Sl)."'>","</div></fieldset>\n";}function
printSelectionAction(array$t){echo"<fieldset><legend>".lang(66)."</legend><div class='fieldset-content'>","<input type='submit' class='button' value='".lang(56)."'>"," <span id='noindex' title='".lang(67)."'></span>","<script".nonce().">\n";$c=new
stdClass();foreach($t
as$s){$pc=reset($s["columns"]);if($s["type"]!="FULLTEXT"&&$pc)$c->$pc=null;}echo"const indexColumns = ".json_encode($c,JSON_UNESCAPED_UNICODE|JSON_HEX_TAG).";\n","selectFieldChange.call(gid('form')['select']);\n","</script>\n","</div></fieldset>\n";}function
processSelectionColumns(array$c,array$t){$L=[];$Fe=[];foreach((array)$_GET["columns"]as$u=>$W){if($W["fun"]=="count"||($W["col"]!=""&&(!$W["fun"]||in_array($W["fun"],Driver::get()->getFunctions())||in_array($W["fun"],Driver::get()->getGrouping())))){$L[$u]=apply_sql_function($W["fun"],($W["col"]!=""?idf_escape($W["col"]):"*"));if(!in_array($W["fun"],Driver::get()->getGrouping()))$Fe[]=$L[$u];}}return[$L,$Fe];}function
processSelectionSearch(array$l,array$t){$I=[];foreach($t
as$q=>$s){if($s["type"]=="FULLTEXT"&&isset($_GET["fulltext"])&&$_GET["fulltext"][$q]!="")$I[]="MATCH (".implode(", ",array_map('AdminNeo\idf_escape',$s["columns"])).") AGAINST (".q($_GET["fulltext"][$q]).(isset($_GET["boolean"][$q])?" IN BOOLEAN MODE":"").")";}foreach((array)$_GET["where"]as$Z){$Cb=$Z["col"];$Zh=$Z["op"];$W=$Z["val"];if("$Cb$W"!=""&&in_array($Zh,$this->getOperators())){$Vb=[];foreach(($Cb!=""?[$Cb=>$l[$Cb]]:$l)as$A=>$k){$ij="";$Ub=" $Zh";$Oh=DIALECT=="pgsql"&&$Zh=="="&&$k["type"]=="oid";if($Oh)$Ub
.=" ".$this->admin->processFieldInput($k,$W)."::regproc";elseif(preg_match('~IN$~',$Zh)){$lf=process_length($W);$Ub
.=" ".($lf!=""?$lf:"(NULL)");}elseif($Zh=="SQL")$Ub=" $W";elseif(preg_match('~^(I?LIKE) %%$~',$Zh,$z))$Ub=" $z[1] ".$this->admin->processFieldInput($k,"%$W%");elseif($Zh=="FIND_IN_SET"){$ij="$Zh(".q($W).", ";$Ub=")";}elseif(!preg_match('~NULL$~',$Zh))$Ub
.=" ".$this->admin->processFieldInput($k,$W);if($Cb!=""||(isset($k["privileges"]["where"])&&(preg_match('~^[-\d.'.(preg_match('~IN$~',$Zh)?',':'').']+$~',$W)||!preg_match('~'.number_type().'|bit~',$k["type"]))&&(!preg_match("~[\x80-\xFF]~",$W)||preg_match('~char|text|enum|set~',$k["type"]))&&(!preg_match('~date|timestamp~',$k["type"])||preg_match('~^\d+-\d+-\d+~',$W))&&(!preg_match('~^elastic~',DRIVER)||$k["type"]!="boolean"||preg_match('~true|false~',$W))&&(!preg_match('~^elastic~',DRIVER)||strpos($Zh,"regexp")===false||preg_match('~text|keyword~',$k["type"])))){if($Oh)$Vb[]=$ij.idf_escape($A).$Ub;else$Vb[]=$ij.Driver::get()->convertSearch(idf_escape($A),$Z,$k).$Ub;}}if(count($Vb)==1)$I[]=$Vb[0];elseif($Vb)$I[]="(".implode(" OR ",$Vb).")";else$I[]="1 = 0";}}return$I;}function
processSelectionOrder(array$l,array$t){$I=[];foreach((array)$_GET["order"]as$u=>$W){if($W!="")$I[]=(preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\)|COUNT\(\*\))$~',$W)?$W:idf_escape($W)).(isset($_GET["desc"][$u])?" DESC".(DIALECT=="pgsql"&&(isset($l[$W]["null"])?$l[$W]["null"]:null)?" NULLS LAST":""):"");}return$I;}function
processSelectionLength(){return
isset($_GET["text_length"])?$_GET["text_length"]:"100";}function
getFieldFunctions(array$k){$I=($k["null"]?"NULL/":"");$Dm=isset($_GET["select"])||where($_GET);foreach([Driver::get()->getInsertFunctions(),Driver::get()->getEditFunctions()]as$u=>$ye){if(!$u||(!isset($_GET["call"])&&$Dm)){foreach($ye
as$Si=>$W){if(!$Si||preg_match("~$Si~",$k["type"]))$I
.="/$W";}}if($u&&$ye&&!preg_match('~enum|set|bool~',$k["type"])&&!is_blob($k))$I
.="/SQL";}if($k["auto_increment"]&&!$Dm)$I=lang(48);return
explode("/",$I);}function
getFieldInput($P,array$k,$Oa,$X,$p){return"";}function
processFieldInput(array$k,$X,$p=""){if($p=="SQL")return$X;if(isset($k["full_type"]))$this->admin->detectJson($k["full_type"],$X,false);$A=$k["field"];$I=q($X);if(preg_match('~^(now|getdate|uuid)$~',$p))$I="$p()";elseif(preg_match('~^current_(date|timestamp)$~',$p))$I=$p;elseif(preg_match('~^([+-]|\|\|)$~',$p))$I=idf_escape($A)." $p $I";elseif(preg_match('~^[+-] interval$~',$p))$I=idf_escape($A)." $p ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i",$X)&&DIALECT!="pgsql"?$X:$I);elseif(preg_match('~^(addtime|subtime|concat)$~',$p))$I="$p(".idf_escape($A).", $I)";elseif(preg_match('~^(md5|sha1|password|encrypt)$~',$p))$I="$p($I)";elseif($k["type"]=="boolean"&&DIALECT=="elastic")$I=$I=="0"?"false":"true";return
unconvert_field($k,$I);}function
getDumpOutputs(){$_i=['file'=>lang(68),'text'=>lang(69),];if(function_exists('gzencode'))$_i['gz']='gzip';return$_i;}function
getDumpFormats(){return(support("dump")?['sql'=>'SQL']:[])+['csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV'];}function
sendDumpHeaders($ff,$nh=false){$zi=$_POST["output"];$Ld=(str_contains($_POST["format"],"sql")?"sql":($nh?"tar":"csv"));if($zi=="gz"){header("Content-Type: application/x-gzip");ob_start(function($O){return
gzencode($O);},1e6);}elseif($Ld=="tar")header("Content-Type: application/x-tar");elseif($Ld=="sql"||$zi=="text")header("Content-Type: text/plain; charset=utf-8");else
header("Content-Type: text/csv; charset=utf-8");return$Ld;}function
dumpTable($P,$jl,$Wm=0){if($_POST["format"]!="sql"){echo"\xef\xbb\xbf";if($jl)dump_csv(array_keys(fields($P)));}else{if($Wm==2){$l=[];foreach(fields($P)as$A=>$k)$l[]=idf_escape($A)." $k[full_type]";$ic="CREATE TABLE ".table($P)." (".implode(", ",$l).")";}else$ic=create_sql($P,$_POST["auto_increment"],$jl);set_utf8mb4($ic);if($jl&&$ic){if($jl=="DROP+CREATE"||$Wm==1)echo"DROP ".($Wm==2?"VIEW":"TABLE")." IF EXISTS ".table($P).";\n";if($Wm==1)$ic=remove_definer($ic);echo"$ic;\n\n";}}}function
dumpData($P,$jl,$G){if($jl){$Ng=(DIALECT=="sqlite"?0:1048576);$l=[];$hf=false;if($_POST["format"]=="sql"){if($jl=="TRUNCATE+INSERT")echo
truncate_sql($P).";\n";$l=fields($P);if(DIALECT=="mssql"){foreach($l
as$k){if($k["auto_increment"]){echo"SET IDENTITY_INSERT ".table($P)." ON;\n";$hf=true;break;}}}}$H=Connection::get()->query($G,1);if($H){$wf="";$gb="";$Xf=[];$_e=[];$ml="";$hc=0;while($J=($P!=''?$H->fetchAssoc():$H->fetchRow())){if(!$Xf){$Y=[];foreach($J
as$W){$k=$H->fetchField();if(!empty($l[$k->name]['generated'])){$_e[$k->name]=true;continue;}$Xf[]=$k->name;$u=idf_escape($k->name);$Y[]="$u = VALUES($u)";}$ml=($jl=="INSERT+UPDATE"?"\nON DUPLICATE KEY UPDATE ".implode(", ",$Y):"").";\n";}if($_POST["format"]!="sql"){if($jl=="table"){dump_csv($Xf);$jl="INSERT";}dump_csv($J);}else{if(!$wf)$wf="INSERT INTO ".table($P)." (".implode(", ",array_map('AdminNeo\idf_escape',$Xf)).") VALUES";foreach($J
as$u=>$W){if(isset($_e[$u])){unset($J[$u]);continue;}$k=$l[$u];$J[$u]=($W===null?"NULL":($W===false?0:unconvert_field($k,preg_match(number_type(),$k["type"])&&!preg_match('~\[~',$k["full_type"])&&is_numeric($W)?$W:(!is_blob($k)||is_utf8($W)?q($W):Driver::get()->quoteBinary($W)))));}$bk=($Ng?"\n":" ")."(".implode(",\t",$J).")";if(!$gb)$gb=$wf.$bk;elseif(DIALECT=="mssql"?$hc%1000!=0:strlen($gb)+4+strlen($bk)+strlen($ml)<$Ng)$gb
.=",$bk";else{echo$gb.$ml;$gb=$wf.$bk;}}$hc++;}if($gb)echo$gb.$ml;}elseif($_POST["format"]=="sql")echo"-- ".str_replace("\n"," ",Connection::get()->getError())."\n";if($hf)echo"SET IDENTITY_INSERT ".table($P)." OFF;\n";}}function
getImportFilePath(){return"adminneo.sql";}function
printDatabaseMenu(){echo"<p class='links top-links'>\n";$Fh=isset($_GET["ns"])?$_GET["ns"]:null;if($Fh==""&&support("database"))echo'<a href="',h(ME),'database=">',icon("edit"),lang(70),"</a>\n";if($Fh!=""&&support("scheme"))echo"<a href='",h(ME),"scheme='>",icon("edit"),lang(71),"</a>\n";if($Fh!=="")echo'<a href="',h(ME),'schema=">',icon("schema"),lang(72),"</a>\n";if(support("privileges"))echo"<a href='",h(ME),"privileges='>",icon("users"),lang(73),"</a>\n";echo"</p>\n";}function
printNavigation($hh){if($hh=="auth"){$zi="";foreach((array)$_SESSION["pwds"]as$Sm=>$Ek){foreach($Ek
as$M=>$Nm){foreach($Nm
as$U=>$E){if($E!==null){$yc=$_SESSION["db"][$Sm][$M][$U];foreach(($yc?array_keys($yc):[""])as$h){$Bk=$this->admin->getServerName($M,false);$S=h(get_driver_name($Sm,$M)).($U!=""||$Bk!=""?" - ":"").h($U).($U!=""&&$Bk!=""?"@":"").h($Bk).($h!=""?h(" - $h"):"");$zi
.="<li><a href='".h(auth_url($Sm,$M,$U,$h))."' class='primary' title='$S'>$S</a></li>\n";}}}}}if($zi)echo"<nav id='logins'><menu>\n$zi</menu></nav>\n";}else{$this->admin->printDatabaseSwitcher($hh);$xa=[];if(DB==""||!$hh){if(support("sql")){$xa[]="<a href='".h(ME)."sql='".bold(isset($_GET["sql"])&&!isset($_GET["import"])).">".icon("command").lang(41)."</a>";$xa[]="<a href='".h(ME)."import='".bold(isset($_GET["import"])).">".icon("import").lang(74)."</a>";}$xa[]="<a href='".h(ME)."dump=".urlencode(isset($_GET["table"])?$_GET["table"]:$_GET["select"])."' id='dump'".bold(isset($_GET["dump"])).">".icon("export").lang(75)."</a>";}if(DB=="")$xa[]='<a href="'.h(ME).'database="'.bold($_GET["database"]==="").">".icon("database-add").lang(76)."</a>\n";if(DB!=""&&$_GET["ns"]===""&&!$hh)$xa[]='<a href="'.h(ME).'scheme="'.bold($_GET["scheme"]==="").">".icon("database-add").lang(77)."</a>\n";if(DB!=""&&$_GET["ns"]!==""&&!$hh)$xa[]='<a href="'.h(ME).'create="'.bold($_GET["create"]==="").">".icon("table-add").lang(78)."</a>\n";if($xa)echo"<p class='links'>".implode("\n",$xa)."</p>";$R=[];if($_GET["ns"]!==""&&!$hh&&DB!=""){Connection::get()->selectDatabase(DB);$R=table_status('',true);}if($_GET["ns"]!==""&&!$hh&&DB!=""){if($R){$this->admin->printTablesFilter();$this->admin->printTableList($R);}else
echo"<div id='tables'><p>".lang(79)."</p></div>\n";}elseif($_GET["ns"]!==""&&DB!="")echo"<div id='tables'></div>\n";if(support("sql")||DIALECT=="elastic"||DIALECT=="mongo"){echo"<script".nonce().">\n";if(support("sql")&&$R){$yg=[];foreach($R
as$P=>$T)$yg[]=js_escape_re($P);$xl=support("table")&&!$this->config->isSelectionPreferred()?"table":"select";echo"window.jushLinks = { ".DIALECT.": {\n",js_escape_key(ME.$xl.'=$&'),': /\b(?<!\$)('.implode('|',$yg).')(?!\$)\b/g';$Zk=["sql","check","event","procedure","trigger","view","type","table","processlist"];if(support('routine')&&array_intersect_key($_GET,array_flip($Zk))){foreach(routines()as$J)echo",\n",js_escape_key(ME.'function='.urlencode($J["SPECIFIC_NAME"]).'&name=$&'),': /\b'.js_escape_re($J["ROUTINE_NAME"]).'(?=["`\]]?\()/g';}echo"\n}};\n";foreach(["bac","bra","sqlite_quo","mssql_bra"]as$W)echo"jushLinks.$W = jushLinks.".DIALECT.";\n";}if(DIALECT!="elastic"&&DIALECT!="mongo"&&$this->getConfig()->isSqlAutocompletionEnabled()&&(isset($_GET["sql"])||isset($_GET["trigger"])||isset($_GET["check"]))){$Hl=array_fill_keys(array_keys($R),[]);foreach(Driver::get()->getAllFields()as$P=>$l){foreach($l
as$k)$Hl[$P][]=$k["field"];}echo"window.addEventListener('DOMContentLoaded', () => { autocompletion = jush.autocompleteSql('".idf_escape("")."', ".json_encode($Hl,JSON_HEX_TAG)."); });\n";}echo"</script>\n";}echo
script("let autocompletion;\nwindow.addEventListener('DOMContentLoaded', () => { initSyntaxHighlighting('".js_escape(doc_version())."', '".js_escape(Connection::get()->getFlavor())."', autocompletion); });");}}function
printDatabaseSwitcher($hh){$g=$this->admin->getDatabases();if(!$g&&DIALECT!="sqlite")return;echo"<div class='db-selector'><form action=''>";hidden_fields_get();echo"<div>";if($g)echo"<select id='database-select' name='db' title='",lang(31),"'>".optionlist([""=>"(".lang(80).")"]+$g,DB)."</select>".script("mixin(gid('database-select'), {onmousedown: dbMouseDown, onchange: dbChange});");else
echo"<input id='database-select' class='input' name='db' value='".h(DB)."' title='",lang(31),"' autocapitalize='off'>\n";echo"<input type='submit' value='".lang(81)."' class='button ".($g?"hidden":"")."'>\n","</div>";if(support("scheme")&&$hh!="db"&&DB!=""&&Connection::get()->selectDatabase(DB)){echo"<div>","<select id='scheme-select' name='ns' title='",lang(82),"'>".optionlist([""=>"(".lang(83).")"]+$this->admin->getSchemas(),$_GET["ns"])."</select>".script("mixin(gid('scheme-select'), {onmousedown: dbMouseDown, onchange: dbChange});"),"</div>";if($_GET["ns"]!="")set_schema($_GET["ns"]);}foreach(["import","sql","schema","dump","privileges"]as$W){if(isset($_GET[$W])){echo
input_hidden($W);break;}}echo"</form></div>\n";}function
printTableList(array$R){$fd=$this->settings->isNavigationDual()||$this->settings->isNavigationHover();$Wg=($fd?"class='dual".($this->settings->isNavigationHover()?" hover":"")."'":($this->settings->isNavigationReversed()?"class='reversed'":""));echo"<nav id='tables'><div class='scroll-marker'></div><menu $Wg>";foreach($R
as$P=>$el){$P="$P";$A=$this->admin->getTableName($el);if($A==""||(isset($el["Partition"])?$el["Partition"]:false))continue;echo"<li>";$ya=in_array($P,[$_GET["table"],$_GET["select"],$_GET["create"],$_GET["indexes"],$_GET["foreign"],$_GET["trigger"],$_GET["check"],$_GET["view"]]);$Ab="primary".(is_view($el)?" view":"");$ql=support("table")||support("indexes");$pk=h(ME)."select=".urlencode($P);$zl=h(ME)."table=".urlencode($P);if($this->settings->isSelectionPreferred()){if($this->settings->isNavigationReversed()&&$ql)echo" <a href='$zl' title='",lang(35),"' class='secondary'>",icon("structure"),"</a>";echo"<a href='$pk'",bold($ya,$Ab)," data-primary='true' title='$A'>$A</a>";if($fd&&$ql)echo" <a href='$zl' title='",lang(35),"' class='secondary'>",icon_solo("structure"),"</a>";}else{if($this->settings->isNavigationReversed())echo" <a href='$pk' title='",lang(34),"' class='secondary'>",icon("data"),"</a>";if($ql)echo"<a href='$zl'",bold($ya,$Ab)," data-primary='true' title='$A'>$A</a>";else
echo"<span data-primary='true'",bold($ya,$Ab),">$A</span>";if($fd)echo" <a href='$pk' title='",lang(34),"' class='secondary'>",icon_solo("data"),"</a>";}echo"</li>\n";}echo"</menu></nav>\n",script("initTablesList(".json_encode($this->admin->getDatabase(),JSON_HEX_TAG).");");}function
getSettingsRows($He){$N=parent::getSettingsRows($He);if($He==1){$B=[""=>lang(11),Config::$NavigationSimple=>lang(84),Config::$NavigationDual=>lang(85),Config::$NavigationHover=>lang(86),Config::$NavigationReversed=>lang(87)];$i=$B[$this->config->getNavigationMode()];$B[""].=" ($i)";$N["navigationMode"]="<tr><th>".lang(88)."</th>"."<td>".html_radios("navigationMode",$B,($ta=$this->settings->getParameter("navigationMode"))!==null?$ta:"")."<span class='input-hint'>".lang(89)."</span>"."</td></tr>\n";$B=[""=>lang(11),0=>lang(35),1=>lang(34),];$i=$B[$this->config->isSelectionPreferred()?1:0];$B[""].=" ($i)";$N["preferSelection"]="<tr><th id='label-links'>".lang(90)."</th>"."<td>".html_select("preferSelection",$B,($ta=$this->settings->getParameter("preferSelection"))!==null?$ta:"","","label-links",true)."<span class='input-hint'>".lang(91)."</span>"."</td></tr>\n";}return$N;}function
getForeignColumnInfo(array$me,$b){return
null;}}class
TmpFile{private$handler;private$size;function
__construct(){$this->handler=tmpfile();}function
getSize(){return$this->size;}function
write($cc){if(!$this->handler)return;$this->size+=strlen($cc);fwrite($this->handler,$cc);}function
send(){if(!$this->handler)return;fseek($this->handler,0);fpassthru($this->handler);fclose($this->handler);}}function
print_select_result(Result$H,$e=null,array$qi=[],$w=0){$yg=[];$t=[];$c=[];$cb=[];$um=[];$I=[];for($q=0;(!$w||$q<$w)&&($J=$H->fetchRow());$q++){if(!$q){echo"<div class='scrollable'>\n","<table class='nowrap'>\n","<thead><tr>";for($Pf=0;$Pf<count($J);$Pf++){$k=$H->fetchField();if(!$k){echo"<th></th>";continue;}$A=$k->name;$pi=isset($k->orgtable)?$k->orgtable:"";$oi=isset($k->orgname)?$k->orgname:$A;if(isset($k->table))$I[$k->table]=$pi;if($qi&&DIALECT=="sql")$yg[$Pf]=($A=="table"?"table=":($A=="possible_keys"?"indexes=":null));elseif($pi!=""){if(!isset($t[$pi])){$t[$pi]=[];foreach(indexes($pi,$e)as$s){if($s["type"]=="PRIMARY"){$t[$pi]=array_flip($s["columns"]);break;}}$c[$pi]=$t[$pi];}if(isset($c[$pi][$oi])){unset($c[$pi][$oi]);$t[$pi][$oi]=$Pf;$yg[$Pf]=$pi;}}if($k->charsetnr==63)$cb[$Pf]=true;$um[$Pf]=$k->type;$S=trim(($pi!=""?"$pi.$oi":($k->name!=$oi?$oi:""))." ".Driver::get()->getTypeName($k));echo"<th".($S!=""?" title='".h($S)."'":"").">".h($A).($qi?doc_link(['sql'=>"explain-output.html#explain_".strtolower($A),'mariadb'=>"reference/sql-statements/administrative-sql-statements/analyze-and-explain-statements/explain#columns-in-explain-...-select",]):"");}echo"</thead>\n";}echo"<tr>";foreach($J
as$u=>$W){$x="";if(isset($yg[$u])&&!$c[$yg[$u]]){if($qi&&DIALECT=="sql"){$P=$J[array_search("table=",$yg)];$x=ME.$yg[$u].urlencode($qi[$P]!=""?$qi[$P]:$P);}else{$x=ME."edit=".urlencode($yg[$u]);foreach($t[$yg[$u]]as$Cb=>$Pf)$x
.="&where".urlencode("[".bracket_escape($Cb)."]")."=".urlencode($J[$Pf]);}}$T=($cb[$u]?'blob':($um[$u]==254?'char':''));$k=['full_type'=>$T,'type'=>$T,];$W=select_value($W,$x,$k,null);$Ab=$um[$u]<=9||$um[$u]==246?"class='number'":"";echo"<td $Ab>$W</td>";}}if($q)echo"</table>\n</div>";else
echo"<p class='message'>".lang(92);echo"\n";return$I;}function
referencable_primary($uk){$I=[];foreach(table_status('',true)as$Bl=>$P){if($Bl!=$uk&&fk_support($P)){foreach(fields($Bl)as$k){if($k["primary"]){if($I[$Bl]){unset($I[$Bl]);break;}$I[$Bl]=$k;}}}}return$I;}function
textarea($A,$X,$K=10,$Jb=80){echo"<textarea name='".h($A)."' rows='$K' cols='$Jb' class='sqlarea jush-".DIALECT."' spellcheck='false' wrap='off'>";if(is_array($X)){foreach($X
as$W)echo
h($W[0])."\n\n\n";}else
echo
h($X);echo"</textarea>";}function
select_input($Oa,$B,$X="",$Xh="",$Vi=""){if($B&&$X!=""&&!isset($B[$X]))$B=[$X=>$X]+$B;$Ll=($B?"select":"input");return"<$Ll $Oa".($B?"><option value=''>$Vi".optionlist($B,$X,true)."</select>":" size='10' value='".h($X)."' placeholder='$Vi'>").($Xh?script("qsl('$Ll').onchange = $Xh;",""):"");}function
json_row($u,$W=null){static$fe=true;if($fe)echo"{";if($u!=""){echo($fe?"":",")."\n\t\"".addcslashes($u,"\r\n\t\"\\/").'": '.($W!==null?'"'.addcslashes($W,"\r\n\t\"\\/").'"':'null');$fe=false;}else{echo"\n}\n";$fe=true;}}function
edit_type($u,$k,$Fb,$ne=[],$Od=[]){$T=isset($k["type"])?$k["type"]:null;echo'<td><select name="',h($u),'[type]" class="type" aria-labelledby="label-type">';$Zc=Driver::get()->getTypes();if($T&&!isset($Zc[$T])&&!isset($ne[$T])&&!in_array($T,$Od))$Od[]=$T;$il=Driver::get()->getStructuredTypes();if($ne)$il[lang(93)]=$ne;echo
optionlist(array_merge($Od,$il),$T),'</select><td><input name="',h($u),'[length]" value="',h(isset($k["length"])?$k["length"]:null),'" size="3"',(!(isset($k["length"])?$k["length"]:null)&&preg_match('~var(char|binary)$~',$T)?" class='input required'":" class='input'"),' aria-labelledby="label-length"><td class="options">',($Fb?"<select name='".h($u)."[collation]'".option_types($T,'(char|text|enum|set)$').'><option value="">('.lang(94).')'.optionlist($Fb,isset($k["collation"])?$k["collation"]:null).'</select>':''),(Driver::get()->getUnsigned()?"<select name='".h($u)."[unsigned]'".option_types($T,'^$|'.number_type()).'><option>'.optionlist(Driver::get()->getUnsigned(),isset($k["unsigned"])?$k["unsigned"]:null).'</select>':''),(isset($k['on_update'])?"<select name='".h($u)."[on_update]'".option_types($T,'timestamp|datetime').'>'.optionlist([""=>"(".lang(95).")","CURRENT_TIMESTAMP"],(preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?"CURRENT_TIMESTAMP":$k["on_update"])).'</select>':''),($ne?"<select name='".h($u)."[on_delete]'".option_types($T,'`')."><option value=''>(".lang(96).")".optionlist(Driver::get()->getOnActions(),isset($k["on_delete"])?$k["on_delete"]:null)."</select> ":" ");}function
option_types($T,$um){return" data-types='".h($um)."'".(preg_match("~$um~",$T)?"":" class='hidden'");}function
process_length($v){$wd=Driver::$EnumLengthPattern;return(preg_match("~^\\s*\\(?\\s*$wd(?:\\s*,\\s*$wd)*+\\s*\\)?\\s*\$~",$v)&&preg_match_all("~$wd~",$v,$_)?"(".implode(",",$_[0]).")":preg_replace('~^[0-9].*~','(\0)',preg_replace('~[^-0-9,+()[\]]~','',$v)));}function
process_type($k,$Db="COLLATE"){return" $k[type]".process_length($k["length"]).(preg_match(number_type(),$k["type"])&&in_array($k["unsigned"],Driver::get()->getUnsigned())?" $k[unsigned]":"").(preg_match('~char|text|enum|set~',$k["type"])&&$k["collation"]?" $Db ".(DIALECT=="mssql"?$k["collation"]:q($k["collation"])):"");}function
process_field($k,$sm){if($k["on_update"])$k["on_update"]=preg_replace('~current_timestamp(\(\))?~i',"CURRENT_TIMESTAMP",$k["on_update"]);return[idf_escape(trim($k["field"])),process_type($sm),($k["null"]?" NULL":" NOT NULL"),default_value($k),(preg_match('~timestamp|datetime~',$k["type"])&&$k["on_update"]?" ON UPDATE ".$k["on_update"]:""),(support("comment")&&$k["comment"]!=""?" COMMENT ".q(normalize_newlines($k["comment"])):""),($k["auto_increment"]?auto_increment():null),];}function
normalize_newlines($X){return
str_replace("\r","",(string)$X);}function
default_value($k){if($k["default"]===null)return"";$i=normalize_newlines($k["default"]);$ze=$k["generated"];if(in_array($ze,Driver::get()->getGenerated())){if(DIALECT=="mssql")return" AS ($i)".($ze=="VIRTUAL"?"":" $ze");else
return" GENERATED ALWAYS AS ($i) $ze";}if(stripos($i,"GENERATED ")===0)return" $i";if(preg_match('~char|binary|text|json|enum|set~',$k["type"])||preg_match('~^(?![a-z])~i',$i)){if(DIALECT=="sql"&&preg_match('~text|json~',$k["type"]))return" DEFAULT (".q($i).")";else
return" DEFAULT ".q($i);}else{$i=str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",$i);return" DEFAULT ".(DIALECT=="sqlite"?"($i)":$i);}}function
type_class($T){foreach(['char'=>'text','date'=>'time|year','binary'=>'blob','enum'=>'set',]as$Ab=>$Si){if(preg_match("~$Ab|$Si~",$T))return"class='$Ab'";}return"";}function
edit_fields(array$l,array$Fb,$T="TABLE",$ne=[]){$l=array_values($l);$Sb=$_POST?$_POST["comments"]:Admin::get()->getSettings()->getParameter("commentsOpened");$Pb=$Sb?"":"class='hidden'";echo"<thead><tr>\n";if(support("move_col"))echo"<th class='jsonly'></th>";if($T=="PROCEDURE")echo"<td></td>";echo"<th id='label-name'>",($T=="TABLE"?lang(97):lang(98)),"</th>\n","<td id='label-type'>",lang(45),"<textarea id='enum-edit' rows='4' cols='12' wrap='off' hidden></textarea>",script("gid('enum-edit').onblur = onFieldLengthBlur;"),"</td>\n","<td id='label-length'>",lang(99),"</td>\n","<td>",lang(100),"</td>\n";if($T=="TABLE")echo"<td id='label-null'>NULL</td>\n","<td><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='",lang(48),"'>AI</abbr>",doc_link(['sql'=>"example-auto-increment.html",'mariadb'=>"reference/data-types/auto_increment",'sqlite'=>"autoinc.html",'pgsql'=>"datatype-numeric.html#DATATYPE-SERIAL",'mssql'=>"t-sql/statements/create-table-transact-sql-identity-property",]),"</td>\n","<td id='label-default'>",lang(49),"</td>\n",support("comment")?"<td id='label-comment' $Pb>".lang(47)."</td>\n":"";echo"<td>","<button name='add[",(support("move_col")?0:count($l)),"]' value='1' title='",lang(101),"' class='button light'>",icon_solo("add"),"</button>",(support("move_col")?"":script("qsl('button').onclick = onAddLastFieldRowClick;")),script("row_count = ".count($l).";"),"</td>\n","</tr></thead>\n";$Ab=support("move_col")?"class='sortable'":"";echo"<tbody $Ab>\n";foreach($l
as$q=>$k){$q++;$ri=$k[($_POST?"orig":"field")];$Pc=(isset($_POST["add"][$q-1])||(isset($k["field"])&&!(isset($_POST["drop_col"][$q])?$_POST["drop_col"][$q]:null)))&&(support("drop_col")||$ri=="");echo"<tr",($Pc?"":" hidden"),">\n";if(support("move_col"))echo"<th class='handle jsonly'>",icon_solo("handle"),"</td>";if($T=="PROCEDURE")echo"<td>",html_select("fields[$q][inout]",Driver::get()->getInOut(),$k["inout"]),"</td>\n";echo"<th>";if($Pc)echo"<input class='input' name='fields[$q][field]' value='",h($k["field"]),"' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name' ".(isset($_POST["add"][$q-1])?"autofocus":"").">";echo
input_hidden("fields[$q][orig]",$ri);edit_type("fields[$q]",$k,$Fb,$ne);echo"</th>\n";if($T=="TABLE"){echo"<td>",checkbox("fields[$q][null]",1,$k["null"],"","","block","label-null"),"</td>\n";$wb=$k["auto_increment"]?"checked":"";echo"<td><label class='block'><input type='radio' name='auto_increment_col' value='$q' $wb aria-labelledby='label-ai'></label></td>\n","<td class='default-value'>";if(Driver::get()->getGenerated())echo
html_select("fields[$q][generated]",array_merge(["","DEFAULT"],Driver::get()->getGenerated()),$k["generated"]);else
echo
checkbox("fields[$q][generated]",1,$k["generated"],"","","","label-default");$Oa="name='fields[$q][default]' aria-labelledby='label-default'";$X=h($k["default"]);if(str_contains($X,"\n")){if($X[0]=="\n")$X="\n$X";echo"<textarea $Oa rows='3' cols='30' style='vertical-align: bottom;'>$X</textarea>";}else
echo"<input class='input' $Oa value='$X'>";echo"</td>\n";if(support("comment")){$Mg=Connection::get()->isMinVersion("5.5")?1024:255;$Oa="name='fields[$q][comment]' data-maxlength='$Mg' aria-labelledby='label-comment'";$X=h($k["comment"]);echo"<td $Pb>";if(str_contains($X,"\n")){if($X[0]=="\n")$X="\n$X";echo"<textarea $Oa rows='3' cols='30' style='vertical-align: bottom;'>$X</textarea>";}else
echo"<input class='input' $Oa value='$X'>";echo"</td>\n";}}echo"<td>";if(support("move_col"))echo"<button name='add[$q]' value='1' title='".lang(101)."' class='button light'>",icon_solo("add"),"</button>","<button name='up[$q]' value='1' title='".lang(102)."' class='button light hidden'>",icon_solo("arrow-up"),"</button>","<button name='down[$q]' value='1' title='".lang(103)."' class='button light hidden'>",icon_solo("arrow-down"),"</button>";if($ri==""||support("drop_col"))echo"<button name='drop_col[$q]' value='1' title='".lang(59)."' class='button light'>",icon_solo("remove"),"</button>";echo"</td>\n</tr>\n";}echo"</tbody>";}function
process_fields(&$l){$Mh=0;if($_POST["up"]){$ig=0;foreach($l
as$u=>$k){if(key($_POST["up"])==$u){unset($l[$u]);array_splice($l,$ig,0,[$k]);break;}if(isset($k["field"]))$ig=$Mh;$Mh++;}}elseif($_POST["down"]){$se=false;foreach($l
as$u=>$k){if(isset($k["field"])&&$se){unset($l[key($_POST["down"])]);array_splice($l,$Mh,0,[$se]);break;}if(key($_POST["down"])==$u)$se=$k;$Mh++;}}elseif($_POST["add"]){$l=array_values($l);array_splice($l,key($_POST["add"]),0,[[]]);}elseif(!$_POST["drop_col"])return
false;return
true;}function
normalize_enum($z){$W=$z[0];return"'".str_replace("'","''",addcslashes(stripcslashes(str_replace($W[0].$W[0],$W[0],substr($W,1,-1))),'\\'))."'";}function
grant($Ce,array$pj,$c,$Vh,$Lm){if(!$pj)return
true;if($pj==["ALL PRIVILEGES","GRANT OPTION"]){if($Ce)return(bool)queries("GRANT ALL PRIVILEGES ON $Vh TO $Lm WITH GRANT OPTION");else
return
queries("REVOKE ALL PRIVILEGES ON $Vh FROM $Lm")&&queries("REVOKE GRANT OPTION ON $Vh FROM $Lm");}if($pj==["GRANT OPTION","PROXY"]){if($Ce)return(bool)queries("GRANT PROXY ON $Vh TO $Lm WITH GRANT OPTION");else
return(bool)queries("REVOKE PROXY ON $Vh FROM $Lm");}return(bool)queries(($Ce?"GRANT ":"REVOKE ").preg_replace('~(GRANT OPTION)\([^)]*\)~','$1',implode("$c, ",$pj).$c)." ON $Vh ".($Ce?"TO ":"FROM ").$Lm);}function
drop_create($bd,$ic,$cd,$Ql,$dd,$y,$ah,$Yg,$Zg,$Th,$Ah){if($_POST["drop"])query_redirect($bd,$y,$ah);elseif($Th=="")query_redirect($ic,$y,$Zg);elseif($Th!=$Ah){$lc=queries($ic);queries_redirect($y,$Yg,$lc&&queries($bd));if($lc)queries($cd);}else
queries_redirect($y,$Yg,queries($Ql)&&queries($dd)&&queries($bd)&&queries($ic));}function
create_trigger($Vh,array$mm){$Zl=" $mm[Timing] $mm[Event]".(preg_match('~ OF~',$mm["Event"])?" $mm[Of]":"");return"CREATE TRIGGER ".idf_escape($mm["Trigger"]).(DIALECT=="mssql"?$Vh.$Zl:$Zl.$Vh).rtrim(" $mm[Type]\n$mm[Statement]",";").";";}function
create_routine($Xj,$J){$Hk=[];$l=(array)$J["fields"];ksort($l);$mf=implode("|",Driver::get()->getInOut());foreach($l
as$k){if($k["field"]!="")$Hk[]=(preg_match("~^($mf)\$~",$k["inout"])?"$k[inout] ":"").idf_escape($k["field"]).process_type($k,"CHARACTER SET");}$Dc=rtrim($J["definition"],";");return"CREATE $Xj ".idf_escape(trim($J["name"]))." (".implode(", ",$Hk).")".($Xj=="FUNCTION"?" RETURNS".process_type($J["returns"],"CHARACTER SET"):"").($J["language"]?" LANGUAGE $J[language]":"").(DIALECT=="pgsql"?" AS ".q($Dc):"\n$Dc;");}function
remove_definer($G){return
preg_replace('~^([A-Z =]+) DEFINER=`'.preg_replace('~@(.*)~','`@`(%|\1)',logged_user()).'`~','\1',$G);}function
format_foreign_key($o){$Wh=implode("|",Driver::get()->getOnActions());$h=$o["db"];$Fh=$o["ns"];return" FOREIGN KEY (".implode(", ",array_map('AdminNeo\idf_escape',$o["source"])).") REFERENCES ".($h!=""&&$h!=$_GET["db"]?idf_escape($h).".":"").($Fh!=""&&$Fh!=$_GET["ns"]?idf_escape($Fh).".":"").idf_escape($o["table"])." (".implode(", ",array_map('AdminNeo\idf_escape',$o["target"])).")".(preg_match("~^($Wh)\$~",$o["on_delete"])?" ON DELETE $o[on_delete]":"").(preg_match("~^($Wh)\$~",$o["on_update"])?" ON UPDATE $o[on_update]":"").(isset($o["deferrable"])?" $o[deferrable]":"");}function
tar_file($n,TmpFile$dm){$Re=pack("a100a8a8a8a12a12",$n,644,0,0,decoct($dm->getSize()),decoct(time()));$yb=8*32;for($q=0;$q<strlen($Re);$q++)$yb+=ord($Re[$q]);$Re
.=sprintf("%06o",$yb)."\0 ";echo$Re,str_repeat("\0",512-strlen($Re));$dm->send();echo
str_repeat("\0",511-($dm->getSize()+511)%512);}function
doc_link(array$Ri,$Rl="<sup>?</sup>"){if(!(isset($Ri[DIALECT])?$Ri[DIALECT]:null))return"";$Tm=doc_version();$Gm=['sql'=>"https://dev.mysql.com/doc/refman/$Tm/en/",'sqlite'=>"https://www.sqlite.org/",'pgsql'=>"https://www.postgresql.org/docs/".(Connection::get()->isCockroachDB()?"current":$Tm)."/",'mssql'=>"https://learn.microsoft.com/en-us/sql/",'oracle'=>"https://www.oracle.com/pls/topic/lookup?ctx=db".str_replace(".","",$Tm)."&id=",'elastic'=>"https://www.elastic.co/guide/en/elasticsearch/reference/$Tm/",];if(Connection::get()->isMariaDB()){$Gm['sql']="https://mariadb.com/docs/server/";$Ri['sql']=isset($Ri['mariadb'])?$Ri['mariadb']:str_replace(".html","",$Ri['sql']);}return"<a href='".h($Gm[DIALECT].$Ri[DIALECT].(DIALECT=='mssql'?"?view=sql-server-ver$Tm":""))."'".target_blank().">$Rl</a>";}function
doc_version(){return
preg_replace('~^(\d\.?\d).*~s','\1',Connection::get()->getVersion());}function
db_size($h){if(!Connection::get()->selectDatabase($h))return"?";$I=0;foreach(table_status()as$Q)$I+=$Q["Data_length"]+$Q["Index_length"];return
format_number($I);}function
set_utf8mb4($ic){static$Hk=false;if(!$Hk&&preg_match('~\butf8mb4~i',$ic)){$Hk=true;echo"SET NAMES ".charset(Connection::get()).";\n\n";}}error_reporting(E_ALL&~E_DEPRECATED);set_error_handler(function($yd,$j){return(bool)preg_match('~^Undefined (array key|offset|index)~',$j);},E_WARNING|E_NOTICE);;$ce=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($ce||ini_get("filter.default_flags")){foreach(['_GET','_POST','_COOKIE','_SERVER']as$W){$Am=filter_input_array(constant("INPUT$W"),FILTER_UNSAFE_RAW);if($Am)$$W=$Am;}}if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");class
Server{private$params;private$key;function
__construct(array$Di,$u=null){$this->params=$Di;$this->key=$u;}function
getKey(){return
isset($this->key)?$this->key:substr(md5($this->getDriver().$this->getServer()),0,8);}function
getDriver(){return$this->params["driver"];}function
getServer(){return
isset($this->params["server"])?$this->params["server"]:"";}function
getDatabase(){return
isset($this->params["database"])?$this->params["database"]:"";}function
getName(){return
isset($this->params["name"])?$this->params["name"]:(isset($this->params["server"])?$this->params["server"]:"");}function
getUsername(){return
isset($this->params["username"])?$this->params["username"]:"";}function
getPassword(){return
isset($this->params["password"])?$this->params["password"]:"";}function
hasCredentials(){return$this->getUsername()!=""||$this->getPassword()!="";}function
getConfigParams(){$Di=isset($this->params["config"])?$this->params["config"]:[];$Be=["servers"];foreach($Be
as$Ci){if(isset($Di[$Ci]))unset($Di[$Ci]);}return$Di;}}class
Config{static$NavigationSimple="simple";static$NavigationDual="dual";static$NavigationHover="hover";static$NavigationReversed="reversed";private$params;private$servers=[];function
__construct(array$Di){$this->params=$Di;if(isset($this->params["servers"])){foreach($this->params["servers"]as$u=>$M){$_k=new
Server($M,is_string($u)?$u:null);$this->params["servers"][$u]=$_k;$this->servers[$_k->getKey()]=$_k;}}}function
getTheme(){return
isset($this->params["theme"])?$this->params["theme"]:"default";}function
getColorVariant(){return
isset($this->params["colorVariant"])?$this->params["colorVariant"]:"blue";}function
getCssUrls(){return$this->parseList(isset($this->params["cssUrls"])?$this->params["cssUrls"]:[]);}function
getJsUrls(){return$this->parseList(isset($this->params["jsUrls"])?$this->params["jsUrls"]:[]);}function
getNavigationMode(){return
isset($this->params["navigationMode"])?$this->params["navigationMode"]:self::$NavigationSimple;}function
isNavigationSimple(){return$this->getNavigationMode()==self::$NavigationSimple;}function
isNavigationDual(){return$this->getNavigationMode()==self::$NavigationDual;}function
isNavigationReversed(){return$this->getNavigationMode()==self::$NavigationReversed;}function
isSelectionPreferred(){return
isset($this->params["preferSelection"])?$this->params["preferSelection"]:false;}function
isJsonValuesDetection(){return
isset($this->params["jsonValuesDetection"])?$this->params["jsonValuesDetection"]:false;}function
isJsonValuesAutoFormat(){return
isset($this->params["jsonValuesAutoFormat"])?$this->params["jsonValuesAutoFormat"]:false;}function
isRelationLinks(){return
isset($this->params["relationLinks"])?$this->params["relationLinks"]:false;}function
getRecordsPerPage(){return(int)(isset($this->params["recordsPerPage"])?$this->params["recordsPerPage"]:50);}function
getEnumAsSelectThreshold(){if(array_key_exists("enumAsSelectThreshold",$this->params))return$this->params["enumAsSelectThreshold"]!==null?(int)$this->params["enumAsSelectThreshold"]:null;else
return
5;}function
isVersionVerificationEnabled(){return
isset($this->params["versionVerification"])?$this->params["versionVerification"]:true;}function
isSqlAutocompletionEnabled(){return
isset($this->params["sqlAutocompletion"])?$this->params["sqlAutocompletion"]:true;}function
getHiddenDatabases(){return$this->parseList(isset($this->params["hiddenDatabases"])?$this->params["hiddenDatabases"]:[]);}function
getHiddenSchemas(){return$this->parseList(isset($this->params["hiddenSchemas"])?$this->params["hiddenSchemas"]:[]);}function
getVisibleCollations(){return$this->parseList(isset($this->params["visibleCollations"])?$this->params["visibleCollations"]:[]);}function
getDefaultDriver(array$ad){$Yc=isset($this->params["defaultDriver"])?$this->params["defaultDriver"]:null;return$Yc&&isset($ad[$Yc])?$Yc:key($ad);}function
getDefaultServer(){$M=isset($this->params["defaultServer"])?$this->params["defaultServer"]:null;if($M===null)return
null;$_k=isset($this->params["servers"][$M])?$this->params["servers"][$M]:null;if($_k)return$_k->getKey();return$M;}function
getDefaultDatabase(){return
isset($this->params["defaultDatabase"])?$this->params["defaultDatabase"]:null;}function
getDefaultPasswordHash(){return
isset($this->params["defaultPasswordHash"])?$this->params["defaultPasswordHash"]:null;}function
getSslKey(){return
isset($this->params["sslKey"])?$this->params["sslKey"]:null;}function
getSslCertificate(){return
isset($this->params["sslCertificate"])?$this->params["sslCertificate"]:null;}function
getSslCaCertificate(){return
isset($this->params["sslCaCertificate"])?$this->params["sslCaCertificate"]:null;}function
getSslTrustServerCertificate(){return
isset($this->params["sslTrustServerCertificate"])?$this->params["sslTrustServerCertificate"]:null;}function
getSslEncrypt(){return
isset($this->params["sslEncrypt"])?$this->params["sslEncrypt"]:null;}function
getSslMode(){return
isset($this->params["sslMode"])?$this->params["sslMode"]:null;}function
hasServers(){return
isset($this->params["servers"]);}function
getServerPairs(array$ad){$Nk=null;foreach($this->servers
as$M){if(!isset($ad[$M->getDriver()]))continue;if(!$Nk)$Nk=$M->getDriver();elseif($M->getDriver()!=$Nk){$Nk=null;break;}}$Ak=[];foreach($this->servers
as$u=>$M){if(!isset($ad[$M->getDriver()]))continue;$zk=$M->getName();if($Nk&&$zk)$Ak[$u]=$zk;else$Ak[$u]=$ad[$M->getDriver()].($zk!=""?" - $zk":"");}return$Ak;}function
getServer($yk){return
isset($this->servers[$yk])?$this->servers[$yk]:null;}function
applyServer($M){$M=$this->getServer($M);if(!$M)return;$this->params=array_merge($this->params,$M->getConfigParams());}private
function
parseList($_g){if(is_array($_g))return$_g;return
preg_split('~\s*,\s*~',(string)$_g);}}class
Settings{private
static$CookieName="neo_settings";static$ColorSchemeLight="light";static$ColorSchemeDark="dark";static$NavigationWidthMin=10;static$NavigationWidthMax=30;private$config;private$params=[];function
__construct(Config$Wb){$this->config=$Wb;if(isset($_COOKIE[self::$CookieName])){parse_str($_COOKIE[self::$CookieName],$this->params);$this->save();}if(isset($_COOKIE["neo_lang"])){$this->updateParameter("lang",$_COOKIE["neo_lang"]);unset($_COOKIE["neo_lang"]);cookie("neo_lang","",-3600);}}static
function
readParameter($u){parse_str(isset($_COOKIE[self::$CookieName])?$_COOKIE[self::$CookieName]:"",$Di);return
isset($Di[$u])?$Di[$u]:null;}function
getParameter($u,$i=null){return
isset($this->params[$u])?$this->params[$u]:$i;}function
updateParameter($u,$X){$this->updateParameters([$u=>$X]);}function
updateParameters(array$Di){$this->params=array_filter(array_merge($this->params,$Di),function($X){return$X!==null;});$this->save();}private
function
save(){cookie(self::$CookieName,http_build_query($this->params),7776000);}function
getTheme(){return($ta=$this->getParameter("theme"))!==null?$ta:$this->config->getTheme();}function
getColorScheme(){return$this->getParameter("colorScheme");}function
getNavigationMode(){return($ta=$this->getParameter("navigationMode"))!==null?$ta:$this->config->getNavigationMode();}function
isNavigationSimple(){return$this->getNavigationMode()==Config::$NavigationSimple;}function
isNavigationDual(){return$this->getNavigationMode()==Config::$NavigationDual;}function
isNavigationHover(){return$this->getNavigationMode()==Config::$NavigationHover;}function
isNavigationReversed(){return$this->getNavigationMode()==Config::$NavigationReversed;}function
getNavigationWidth(){$kn=$this->getParameter("navigationWidth");if($kn===null)return
null;return
min(max((float)$kn,self::$NavigationWidthMin),self::$NavigationWidthMax);}function
isSelectionPreferred(){return($ta=$this->getParameter("preferSelection"))!==null?$ta:$this->config->isSelectionPreferred();}function
isRelationLinks(){return
isset($this->params["relationLinks"])?$this->params["relationLinks"]:$this->config->isRelationLinks();}function
getRecordsPerPage(){return($ta=$this->getParameter("recordsPerPage"))!==null?$ta:$this->config->getRecordsPerPage();}function
getEnumAsSelectThreshold(){$X=$this->getParameter("enumAsSelectThreshold");if($X<0)return
null;return$X!==null?(int)$X:$this->config->getEnumAsSelectThreshold();}}class
Hash{static
function
hkdf($v,$u,$rf="",$ck=""){if(extension_loaded("hash")&&PHP_VERSION_ID>=70120)return
hash_hkdf("sha1",$u,$v,$rf,$ck);if($ck=="")$ck=str_repeat("\0",20);$qj=self::hmacSha1($u,$ck);$Qh="";for($Wf="",$db=1;!isset($Qh[$v-1]);$db++){$Wf=self::hmacSha1($Wf.$rf.chr($db),$qj);$Qh
.=$Wf;}return
substr($Qh,0,$v);}static
function
hmacSha1($f,$u){if(!extension_loaded("hash"))return
hash_hmac("sha1",$f,$u,true);if(strlen($u)>64)$u=sha1($u,true);$u=str_pad($u,64,"\0");$Ff=($u^str_repeat("\x36",64));$ai=($u^str_repeat("\x5C",64));return
sha1($ai.sha1($Ff.$f,true),true);}}class
Random{static
function
strongKey(){return
strtr(rtrim(base64_encode(Random::bytes(32)),"="),"+/","-_");}static
function
bytes($v){if(PHP_VERSION_ID>=70000)return
random_bytes($v);$H=self::tryAlternatives($v);if($H!==false)return$H;$H=self::lastResortRandom($v);if($H!==false)return$H;throw
new
Exception("Error generating random bytes");}private
static
function
tryAlternatives($v){if(extension_loaded("libsodium"))return
\Sodium\randombytes_buf($v);$_m=DIRECTORY_SEPARATOR==="/";if($_m){$H=self::readDevUrandom($v);if($H!==false)return$H;}$hb=$_m&&PHP_VERSION_ID>50609&&PHP_VERSION_ID<50613;if(extension_loaded("mcrypt")&&!$hb){$H=mcrypt_create_iv($v,MCRYPT_DEV_URANDOM);if($H!==false)return$H;}$ib=PHP_VERSION_ID<50444||(PHP_VERSION_ID>50500&&PHP_VERSION_ID<50528)||(PHP_VERSION_ID>50600&&PHP_VERSION_ID<50612);if(extension_loaded("openssl")&&!$ib){$H=openssl_random_pseudo_bytes($v,$hl);if($hl)return$H;}return
false;}private
static
function
readDevUrandom($v){static$m=null;if($m===null)$m=@fopen("/dev/urandom","rb");if(!$m)return
false;$Mj=$v;$H="";do{$f=fread($m,$Mj);if($f===false)return
false;$Mj-=strlen($f);$H
.=$f;}while($Mj>0);return$H;}private
static
function
readCapicom($v){$Lb=new
\COM("CAPICOM.Utilities.1");$Mj=$v;$H="";do{$f=base64_decode((string)$Lb->GetRandom($v,0));$Mj-=strlen($f);$H
.=$f;}while($Mj>0);return$H;}private
static
function
lastResortRandom($v){static$u=null;static$ck=null;if($u===null){$f=$_SERVER;$f[]=uniqid("",true);shuffle($f);$u=sha1(serialize($f),true);if(extension_loaded("openssl"))$ck=openssl_random_pseudo_bytes(20);else{$ck="";for($q=0;$q<20;$q++)$ck
.=chr((mt_rand()^mt_rand())%256);}}else{if((ord($u)%2===0)===(ord($ck)%2===0))$u=Hash::hmacSha1($u,$ck);else$ck=Hash::hmacSha1($ck,$u);}return
Hash::hkdf($v,$u,"$v",$ck);}}if(!function_exists("str_starts_with")){function
str_starts_with($Qe,$xh){return
strpos($Qe,$xh)===0;}}if(!function_exists("str_contains")){function
str_contains($Qe,$xh){return
strpos($Qe,$xh)!==false;}}if(!function_exists("password_verify")){function
password_verify($E,$Pe){return
false;}}if(!function_exists("ini_set")){function
ini_set($gi,$X){return
false;}}function
version(){return
VERSION;}function
idf_unescape($if){if(!preg_match('~^[`\'"[]~',$if))return$if;$ig=substr($if,-1);return
str_replace($ig.$ig,$ig,substr($if,1,-1));}function
q($O){return
Connection::get()->quote($O);}function
number($W){return
preg_replace('~[^0-9]+~','',$W);}function
number_type(){return'((^|[^o])int(?!er)|numeric|real|float|double|decimal|money)';}function
remove_slashes(array$Y,$ce=false){$I=[];foreach($Y
as$u=>$W)$I[stripslashes($u)]=(is_array($W)?remove_slashes($W,$ce):($ce?$W:stripslashes($W)));return$I;}function
bracket_escape($if,$Va=false){static$jm=[':'=>':1',']'=>':2','['=>':3','"'=>':4'];return
strtr($if,($Va?array_flip($jm):$jm));}function
min_version($Tm,$Hg=null,$e=null){if(!$e)$e=Connection::get();if($Hg&&$e->isMariaDB())$Tm=$Hg;return$Tm&&$e->isMinVersion($Tm);}function
charset(Connection$e){return($e->isMinVersion("5.5.3")?"utf8mb4":"utf8");}function
link_files($A,array$be){switch($A){case'favicon-blue.ico':$n='favicon-blue-0f5ce53a66b1e25395d0048da369f19e__dc9919c6.ico';break;case'favicon-green.ico':$n='favicon-green-def78cfa7c465c8b0e9966e3eb87407d__dc9919c6.ico';break;case'favicon-orange.ico':$n='favicon-orange-cd68622e75276fdf7c60d1e9d4deee14__dc9919c6.ico';break;case'favicon-purple.ico':$n='favicon-purple-d4b02fdcc3abcc374a77c65f88513c01__dc9919c6.ico';break;case'favicon-red.ico':$n='favicon-red-c2ebb34a8df5aba28e15d87728a151df__dc9919c6.ico';break;case'favicon-blue.svg':$n='favicon-blue-17e440832c1eac07527560a0d6f0d2ee__dc9919c6.svg';break;case'favicon-green.svg':$n='favicon-green-bb254c95a033f67e3d433a3df63e160d__dc9919c6.svg';break;case'favicon-orange.svg':$n='favicon-orange-53ca3b502d7fb29f01bfbf87fc4d6b24__dc9919c6.svg';break;case'favicon-purple.svg':$n='favicon-purple-4cfd57d31ab991e8071fe34060cd3123__dc9919c6.svg';break;case'favicon-red.svg':$n='favicon-red-a006e401273230fd6be80568c8361b57__dc9919c6.svg';break;case'apple-touch-icon-blue.png':$n='apple-touch-icon-blue-f2a5f6f50418d7293b806faf273fe381__dc9919c6.png';break;case'apple-touch-icon-green.png':$n='apple-touch-icon-green-903cc109ea077cd9e91508416c5e335a__dc9919c6.png';break;case'apple-touch-icon-orange.png':$n='apple-touch-icon-orange-6efda14fd1d3c45382c67d7f324bdccf__dc9919c6.png';break;case'apple-touch-icon-purple.png':$n='apple-touch-icon-purple-2388fa66883b7c5e6b4cf5c795eae8fc__dc9919c6.png';break;case'apple-touch-icon-red.png':$n='apple-touch-icon-red-507228751d2170d047e72142d2c02390__dc9919c6.png';break;case'logo.svg':$n='logo-de272eb4bdca9c6fffd38c073270fb1a__9d7e398f.svg';break;case'jush.css':$n='jush-b3a93b18444da26820ff61746521dede__c1fc09bb.css';break;case'jush-dark.css':$n='jush-dark-f8dac59c6ad1018686e52a0e0357e421__2ec7793c.css';break;case'jush.js':$n='jush-31ccd1ce96536a294822e952872683d1__d5406f57.js';break;case'icons.svg':$n='icons-70163a2695280bf75edba563e7b5471b__2ec7793c.svg';break;case'default-blue.css':$n='default-blue-e7acfdb81453b86f081569afa115859e__58308477.css';break;case'default-green.css':$n='default-green-268f042072e76ab07aa2fd0350e0aa0c__58308477.css';break;case'default-orange.css':$n='default-orange-e4e5ea626cdcbe83e07c7933dc04f916__58308477.css';break;case'default-purple.css':$n='default-purple-6b1de1f635d52b55797976fef486a515__58308477.css';break;case'default-red.css':$n='default-red-0f424ea89c2a43c6eb0ec8f0e22362a5__58308477.css';break;case'default-blue-dark.css':$n='default-blue-dark-1061ad7d216f143e3626560b92b66061__136f6b79.css';break;case'default-green-dark.css':$n='default-green-dark-6176b1c7b42ee9f244b3a9c3d8065728__136f6b79.css';break;case'default-orange-dark.css':$n='default-orange-dark-ef164a8d58dc89b2719f881377f73c89__136f6b79.css';break;case'default-purple-dark.css':$n='default-purple-dark-0f4fa03fa2d9287ef390780e90d9b06d__136f6b79.css';break;case'default-red-dark.css':$n='default-red-dark-3c1e28afe2cc92815bc7347df0d5776f__136f6b79.css';break;case'dune-blue.css':$n='dune-blue-73b4ec7032a07757722b492bcc01937f__136f6b79.css';break;case'dune-green.css':$n='dune-green-4d18ddecd75372ef6cc20a81a9e1d8d8__136f6b79.css';break;case'dune-orange.css':$n='dune-orange-1abf8bc0f3672a0a03a9c80b4fa032ee__136f6b79.css';break;case'dune-purple.css':$n='dune-purple-d155c2c103c4fd5ea0859e0794d8cad1__136f6b79.css';break;case'dune-red.css':$n='dune-red-b617e378af72da12da8a98167baae57e__136f6b79.css';break;case'dune-blue-dark.css':$n='dune-blue-dark-0dcd247d9a7497ee9b8d269f82fd8345__136f6b79.css';break;case'dune-green-dark.css':$n='dune-green-dark-9c9c318f665c9f0415fc1c3736e7b90f__136f6b79.css';break;case'dune-orange-dark.css':$n='dune-orange-dark-be294087bf88411c8bf6a085f995fcce__136f6b79.css';break;case'dune-purple-dark.css':$n='dune-purple-dark-ea50a2a07296958eba342f0cf81c60f3__136f6b79.css';break;case'dune-red-dark.css':$n='dune-red-dark-fde13ea21a7e0cbe487d1d4d28b6a1cd__136f6b79.css';break;case'main.js':$n='main-0864f21d8576870afa2ea1b4d2bb6be8__169bbed7.js';break;default:$n=null;break;}if(!$n)return
null;return
BASE_URL."?file=".urldecode($n);}function
ini_bool($gi){$W=ini_get($gi);return
preg_match('~^(on|true|yes)$~i',$W)||(int)$W;}function
ini_bytes($tf){$W=ini_get($tf);switch(strtolower(substr($W,-1))){case'g':$W=(int)$W*1024;case'm':$W=(int)$W*1024;case'k':$W=(int)$W*1024;}return$W;}function
max_input_vars($J,$xi){$Jg=(int)ini_get("max_input_vars");return($Jg?(int)floor(($Jg-$xi)/$J):0);}function
max_input_vars_error(){$tf="max_input_vars";return
lang(104,"$tf = ".(int)ini_get($tf));}function
sid(){static$I;if($I===null)$I=(session_id()&&!($_COOKIE&&ini_bool("session.use_cookies")));return$I;}function
save_driver_name($Yc,$M,$A){restart_session();$_SESSION["drivers"][$Yc][$M]=$A;stop_session();}function
get_driver_name($Yc,$M=null){return
isset($_SESSION["drivers"][$Yc][$M])?$_SESSION["drivers"][$Yc][$M]:Drivers::get($Yc);}function
save_login($Yc,$M,$U,$E,$h=""){$u=isset($_COOKIE["neo_key"])?$_COOKIE["neo_key"]:null;$_SESSION["pwds"][$Yc][$M][$U]=$u?[encrypt_string($E,$u)]:$E;$_SESSION["db"][$Yc][$M][$U][$h]=true;}function
delete_login($Yc,$M,$U){unset($_SESSION["pwds"][$Yc][$M][$U]);unset($_SESSION["db"][$Yc][$M][$U]);}function
get_password(){$E=get_session("pwds");if(is_array($E))return$_COOKIE["neo_key"]?decrypt_string($E[0],$_COOKIE["neo_key"]):false;return$E;}function
get_vals($G,$b=0){$I=[];$H=Connection::get()->query($G);if(is_object($H)){while($J=$H->fetchRow())$I[]=$J[$b];}return$I;}function
get_key_vals($G,$e=null,$Ik=true){if(!$e)$e=Connection::get();$I=[];$H=$e->query($G);if(is_object($H)){while($J=$H->fetchRow()){if($Ik)$I[$J[0]]=$J[1];else$I[]=$J[0];}}return$I;}function
get_rows($G,$e=null,$j="<p class='error'>"){if(!$e)$e=Connection::get();$I=[];$H=$e->query($G);if(is_object($H)){while($J=$H->fetchAssoc())$I[]=$J;}elseif(!$H&&!is_object($e)&&$j&&(defined("AdminNeo\PAGE_HEADER")||$j=="-- "))echo$j.error()."\n";return$I;}function
unique_array(array$J,array$t){foreach($t
as$s){if(!preg_match("~PRIMARY|UNIQUE~",$s["type"])&&!$s["partial"])continue;$xm=[];foreach($s["columns"]as$u){if(!isset($J[$u]))continue
2;$xm[$u]=$J[$u];}return$xm;}return
null;}function
escape_key($u){if(preg_match('(^([\w(]+)('.str_replace("_",".*",preg_quote(idf_escape("_"))).')([ \w)]+)$)',$u,$z))return$z[1].idf_escape(idf_unescape($z[2])).$z[3];return
idf_escape($u);}function
where($Z,$l=[]){$Vb=[];foreach((array)$Z["where"]as$u=>$W){$u=bracket_escape($u,true);$b=escape_key($u);$k=isset($l[$u])?$l[$u]:null;$Xd=isset($k["type"])?$k["type"]:null;$we=isset($k["full_type"])?$k["full_type"]:null;$Hf=$k&&(is_blob($k)||preg_match('~binary~',$Xd));if($Hf&&!is_utf8($W))$Vb[]="$b = ".Driver::get()->quoteBinary($W);elseif(DIALECT=="sql"&&$Xd=="json")$Vb[]="$b = CAST(".q($W)." AS JSON)";elseif(DIALECT=="pgsql"&&preg_match('~^jsonb?$~',$we))$Vb[]="$b::jsonb = ".q($W)."::jsonb";elseif(DIALECT=="sql"&&is_numeric($W)&&strpos($W,".")!==false)$Vb[]="$b LIKE ".q($W);elseif(DIALECT=="mssql"&&strpos($Xd,"datetime")===false)$Vb[]="$b LIKE ".q(preg_replace('~[_%[]~','[\0]',$W));else$Vb[]="$b = ".(isset($l[$u])?unconvert_field($l[$u],q($W)):q($W));if(DIALECT=="sql"&&preg_match('~char|text~',$Xd)&&preg_match("~[^ -@]~",$W))$Vb[]="$b = ".q($W)." COLLATE ".charset(Connection::get())."_bin";}foreach((array)$Z["null"]as$u)$Vb[]=escape_key($u)." IS NULL";return
implode(" AND ",$Vb);}function
where_columns($Z,$l=[]){$c=[];foreach((array)$Z["null"]as$u)$c[$u]=true;foreach((array)$Z["where"]as$u=>$W){$u=bracket_escape($u,true);foreach($l
as$A=>$k){if($u==$A||strpos($u,idf_escape($A))!==false)$c[$A]=true;}}return$c;}function
where_check($W,$l=[]){parse_str($W,$tb);remove_slashes([&$tb]);return
where($tb,$l);}function
where_link($q,$b,$X,$di="="){return"&where%5B$q%5D%5Bcol%5D=".urlencode($b)."&where%5B$q%5D%5Bop%5D=".urlencode(($X!==null?$di:"IS NULL"))."&where%5B$q%5D%5Bval%5D=".urlencode($X);}function
convert_fields(array$c,array$l,array$L=[]){$H="";foreach($c
as$u=>$W){if($L&&!in_array(idf_escape($u),$L))continue;$Na=convert_field($l[$u]);if($Na)$H
.=", $Na AS ".idf_escape($u);}return$H;}function
cookie_path(){return
strtr(preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),[";"=>"%3B",","=>"%2C"]);}function
cookie($A,$X,$sg=2592000){header("Set-Cookie: $A=".rawurlencode($X).($sg?"; expires=".gmdate("D, d M Y H:i:s",time()+$sg)." GMT":"")."; path=".cookie_path().(HTTPS?"; secure":"")."; HttpOnly; SameSite=lax",false);}function
get_url($Fm,$dc){$I=@file_get_contents($Fm,false,$dc);if(function_exists('http_get_last_response_headers'))$http_response_header=($ta=http_get_last_response_headers())!==null?$ta:[];return[$I,isset($http_response_header)?$http_response_header:[]];}function
get_settings($gc="neo_settings"){parse_str(isset($_COOKIE[$gc])?$_COOKIE[$gc]:"",$N);return$N;}function
get_setting($u,$gc="neo_settings"){$N=get_settings($gc);return
isset($N[$u])?$N[$u]:null;}function
save_settings(array$N,$gc="neo_settings"){cookie($gc,http_build_query($N+get_settings($gc)));}function
restart_session(){if(!ini_bool("session.use_cookies")&&session_status()==PHP_SESSION_NONE)session_start();}function
stop_session($ke=false){$Jm=ini_bool("session.use_cookies");if(!$Jm||$ke){session_write_close();if($Jm&&ini_set("session.use_cookies","0")===false)session_start();}}function&get_session($u){return$_SESSION[$u][DRIVER][SERVER][$_GET["username"]];}function
set_session($u,$W){$_SESSION[$u][DRIVER][SERVER][$_GET["username"]]=$W;}function
auth_url($Sm,$M,$U,$h=null){$Em=remove_from_uri(implode("|",array_keys(Drivers::getList()))."|username|ext|".($h!==null?"db|":"").($Sm=='mssql'||$Sm=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$Em,$z);return"$z[1]?".(sid()?session_name()."=".urlencode(session_id())."&":"").urlencode($Sm)."=".urlencode($M)."&".($_GET["ext"]?"ext=".urlencode($_GET["ext"])."&":"")."username=".urlencode($U).($h!=""?"&db=".urlencode($h):"").($z[2]?"&$z[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($y,$Xg=null){if($Xg!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($y!==null?$y:$_SERVER["REQUEST_URI"]))][]=$Xg;}if($y!==null){if($y=="")$y=".";header("Location: $y");exit;}}function
query_redirect($G,$y,$Xg,$Dj=true,$Ed=true,$Pd=false,$Xl=""){if($Ed){$cl=microtime(true);$Pd=!Connection::get()->query($G);$Xl=format_time($cl);}$Xk=$G?Admin::get()->formatMessageQuery($G,$Xl,$Pd):"";if($Pd){Admin::get()->addError(error().$Xk.script("initToggles();"));return
false;}if($Dj)redirect($y,$Xg.$Xk);return
true;}function
queries_redirect($y,$Xg,$Dj){$vj=implode("\n",Queries::$queries);$Xl=format_time(Queries::$start);return
query_redirect($vj,$y,$Xg,$Dj,false,!$Dj,$Xl);}class
Queries{static$queries=[];static$start=0.0;}function
queries($G){if(!Queries::$start)Queries::$start=microtime(true);if(support("sql")){Queries::$queries[]=(preg_match('~;$~',$G)?"DELIMITER ;;\n$G;\nDELIMITER ":$G).";";return
Connection::get()->query($G);}else{Queries::$queries[]=$G;return[];}}function
apply_queries($G,array$R,$_d='AdminNeo\table'){foreach($R
as$P){if(!queries("$G ".$_d($P)))return
false;}return
true;}function
format_time($cl){return
lang(105,max(0,microtime(true)-$cl));}function
relative_uri(){return
str_replace(":","%3a",preg_replace('~^[^?]*/([^?]*)~','\1',$_SERVER["REQUEST_URI"]));}function
remove_from_uri($Ci=""){return
substr(preg_replace("~(?<=[?&])($Ci".(sid()?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_file($u,$zc=false,$Fc=""){$m=$_FILES[$u];if(!$m)return
null;foreach($m
as$u=>$W)$m[$u]=(array)$W;$I='';foreach($m["error"]as$u=>$j){if($j)return$j;$A=$m["name"][$u];$em=$m["tmp_name"][$u];$bc=file_get_contents($zc&&preg_match('~\.gz$~',$A)?"compress.zlib://$em":$em);if($zc){$cl=substr($bc,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$cl))$bc=iconv("utf-16","utf-8",$bc);elseif($cl=="\xEF\xBB\xBF")$bc=substr($bc,3);}if($Fc){if(!preg_match("~$Fc\\s*\$~",$bc))$bc
.=";";$bc
.="\n\n";}$I
.=$bc;}return$I;}function
upload_error($j){$Rg=($j==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($j?lang(106).($Rg?" ".lang(107,$Rg):""):lang(108));}function
repeat_pattern($Si,$v){return
str_repeat("$Si{0,65535}",$v/65535)."$Si{0,".($v%65535)."}";}function
is_utf8($W){return(preg_match('~~u',$W)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$W));}function
format_number($W){return
strtr(number_format($W,0,".",lang(109)),preg_split('~~u',lang(110),-1,PREG_SPLIT_NO_EMPTY));}function
format_rows(array$Q){$K=$Q["Rows"];$Ka=($K&&(DIALECT=="sqlite"||(isset($Q["Engine"])?$Q["Engine"]:"")==(DIALECT=="pgsql"?"table":"InnoDB")));return($Ka?"~ ":"").format_number($K);}function
friendly_url($W){return
preg_replace('~\W~i','-',$W);}function
table_status1($P,$Rd=false){$I=table_status($P,$Rd);return($I?reset($I):["Name"=>$P]);}function
column_foreign_keys($P){$I=[];foreach(Admin::get()->getForeignKeys($P)as$o){foreach($o["source"]as$W)$I[$W][]=$o;}return$I;}function
fields_from_edit(){$I=[];foreach((array)$_POST["field_keys"]as$u=>$W){if($W!=""){$W=bracket_escape($W);$_POST["function"][$W]=$_POST["field_funs"][$u];$_POST["fields"][$W]=$_POST["field_vals"][$u];}}foreach((array)$_POST["fields"]as$u=>$W){$A=bracket_escape($u,true);$I[$A]=["field"=>$A,"full_type"=>"varchar","type"=>"varchar","privileges"=>["insert"=>1,"update"=>1,"where"=>1,"order"=>1],"null"=>true,"auto_increment"=>($u==Driver::get()->primary),];}return$I;}function
dump_headers($ff,$oh=false){$ff=friendly_url($ff).date("-Ymd-His");$Ld=Admin::get()->sendDumpHeaders($ff,$oh);$zi=$_POST["output"];if($zi!="text")header("Content-Disposition: attachment; filename=$ff.$Ld".($zi!="file"&&preg_match('~^[0-9a-z]+$~',$zi)?".$zi":""));session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$Ld;}function
dump_table_order(array$vh,array$Jj){$bg=array_flip($vh);$mi=[];$bn=[];$rc=false;$an=function($A)use(&$an,&$mi,&$bn,&$rc,$bg,$Jj){if(isset($mi[$A]))return;if(isset($bn[$A])){$rc=true;return;}$bn[$A]=true;foreach(isset($Jj[$A])?$Jj[$A]:[]as$Hj){if(isset($bg[$Hj]))$an($Hj);}unset($bn[$A]);$mi[$A]=true;};foreach($vh
as$A)$an($A);return($rc?null:array_keys($mi));}function
dump_csv($J){$rm=$_POST["format"]=="tsv";foreach($J
as$u=>$W){if(preg_match('~["\n]|^0[^.]|\.\d*0$|'.($rm?'\t':'[,;]|^$').'~',$W))$J[$u]='"'.str_replace('"','""',$W).'"';}echo
implode(($_POST["format"]=="csv"?",":($rm?"\t":";")),$J)."\r\n";}function
apply_sql_function($p,$b){return($p?($p=="unixepoch"?"DATETIME($b, '$p')":($p=="count distinct"?"COUNT(DISTINCT ":strtoupper("$p("))."$b)"):$b);}function
get_temp_dir(){$Qi=ini_get("upload_tmp_dir");if(!$Qi)$Qi=sys_get_temp_dir();return$Qi;}function
open_file_with_lock($n){if(is_link($n))return
null;$m=@fopen($n,"c+");if(!$m)return
null;@chmod($n,0660);if(!flock($m,LOCK_EX)){fclose($m);return
null;}return$m;}function
write_and_unlock_file($m,$f){rewind($m);fwrite($m,$f);ftruncate($m,strlen($f));unlock_file($m);}function
unlock_file($m){flock($m,LOCK_UN);fclose($m);}function
first(array$Ma){return
reset($Ma);}function
get_private_key($ic){$n=get_temp_dir()."/adminneo.key";if(!$ic&&!file_exists($n))return
false;$m=open_file_with_lock($n);if(!$m)return
false;$u=stream_get_contents($m);if(!$u){$u=Random::strongKey();write_and_unlock_file($m,$u);}else
unlock_file($m);return$u;}function
get_random_string(){return
Random::strongKey();}function
select_value($W,$x,$k,$Tl){if(is_array($W)){$I="";if(array_filter($W,'is_array')==array_values($W)){$Xf=[];foreach($W
as$V)$Xf+=array_fill_keys(array_keys($V),null);foreach(array_keys($Xf)as$Sf)$I
.="<th>".h($Sf);foreach($W
as$V){$I
.="<tr>";foreach(array_merge($Xf,$V)as$Om)$I
.="<td>".select_value($Om,$x,$k,$Tl);}}else{foreach($W
as$Sf=>$V)$I
.="<tr>".($W!=array_values($W)?"<th>".h($Sf):"")."<td>".select_value($V,$x,$k,$Tl);}return"<table>$I</table>";}$hk="";if($k&&$W!==null&&($Tl===null||strlen($W)<=$Tl)&&($Y=Driver::get()->explodeArrayValue($W,$k["full_type"],$hk))){$gk=$k;$gk["type"]=$gk["full_type"]=$hk;$I=select_array_value($Y,$W,$x,$gk,$Tl);return
Driver::get()->implodeArrayValues($I,$k["full_type"]);}if(!$x)$x=Admin::get()->getFieldValueLink($W,$k);if($k)$W=Connection::get()->formatValue($W,$k);$I=$k?Admin::get()->formatFieldValue($W,$k):$W;if($I!==null){if(!is_utf8($I))$I="\0";elseif($Tl!=""&&is_shortable($k))$I=truncate_utf8($I,max(0,+$Tl));else$I=h($I);}return
Admin::get()->formatSelectionValue($I,$x,$k,$W);}function
select_array_value(array$Y,$W,$x,array$k,$Tl){$H=[];foreach($Y
as$X){if(is_array($X))$H[]=select_array_value($X,$W,$x,$k,$Tl);else{$cg=preg_replace('~(where%5B\d+%5D%5Bval%5D=)'.preg_quote(urlencode($W),"~")."~",'${1}'.urlencode($X),$x);$H[]=select_value($X,$cg,$k,$Tl);}}return$H;}function
is_blob(array$k){$um=Driver::get()->getStructuredTypes();$T=lang(111);return
preg_match('~blob|bytea|raw|file'.(DIALECT=="mssql"?'|binary|image':'').'~',$k["type"])&&!in_array($k["type"],isset($um[$T])?$um[$T]:[]);}function
is_generated_always(array$k){return(isset($k["generated"])?$k["generated"]:"")!=""||stripos((string)(isset($k["default"])?$k["default"]:""),"GENERATED ALWAYS AS ")===0;}function
is_mail($X){return
is_string($X)&&filter_var($X,FILTER_VALIDATE_EMAIL);}function
is_web_url($X){if(!is_string($X)||!preg_match('~^(https?:)?//~i',$X))return
false;$Tb=parse_url($X);if(!$Tb)return
false;$Fm=$X;if(isset($Tb['path'])){$rd=array_map('urlencode',explode('/',$Tb['path']));$Fm=str_replace($Tb['path'],implode('/',$rd),$Fm);}if(isset($Tb['query'])){parse_str($Tb['query'],$Di);$Fm=str_replace($Tb['query'],http_build_query($Di),$Fm);}if(!isset($Tb['scheme']))$Fm="https:$Fm";return(bool)filter_var($Fm,FILTER_VALIDATE_URL);}function
is_shortable($k){return$k&&!preg_match('~'.number_type().'|date|time|year~',$k["type"]);}function
host_port($M){return(preg_match('~^(:([^:].*)|(\[(.+)]|(([^:]+://)?[^:]+))(:(\d+))?)$~',$M,$z)?[(isset($z[4])?$z[4]:"").(isset($z[5])?$z[5]:""),$z[2].(isset($z[8])?$z[8]:"")]:[$M,'']);}function
count_rows($P,$Z,$If,$Fe){$G=" FROM ".table($P).($Z?" WHERE ".implode(" AND ",$Z):"");return($If&&(DIALECT=="sql"||count($Fe)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$Fe).")$G":"SELECT COUNT(*)".($If?" FROM (SELECT 1$G GROUP BY ".implode(", ",$Fe).") x":$G));}function
slow_query($G){$h=Admin::get()->getDatabase();$Yl=Admin::get()->getQueryTimeout();$Qk=Driver::get()->slowQuery($G,$Yl);$e=null;if(!$Qk&&support("kill")){$e=connect();if($e&&($h==""||$e->selectDatabase($h))){$Zf=number($e->getValue(connection_id()));echo'<script',nonce(),'>
	const timeout = setTimeout(() => {
		ajax(\'',js_escape(ME),'script=kill\', function() {
		}, \'kill=',$Zf,'&token=',get_token(),'\');
	}, ',1000*$Yl,');
</script>
';}}ob_flush();flush();$I=@get_key_vals(($Qk?:$G),$e,false);if($e){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$I;}function
get_token(){$_j=rand(1,1e6);return($_j^$_SESSION["token"]).":$_j";}function
verify_token(){list($fm,$_j)=explode(":",$_POST["token"]);return($_j^$_SESSION["token"])==$fm&&in_array($_SERVER["HTTP_SEC_FETCH_SITE"],["","same-origin"]);}function
script($Uk,$im="\n"){return"<script".nonce().">$Uk</script>$im";}function
script_src($Fm,$Cc=false){return"<script src='".h($Fm)."'".nonce().($Cc?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
input_hidden($A,$X=""){return"<input type='hidden' name='".h($A)."' value='".h($X)."'>";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($O){if($O===null||$O==="")return"";return
str_replace(["&","<","\"","'","\0"],["&amp;","&lt;","&quot;","&#039;","&#0;"],$O);}function
truncate_utf8($O,$v=80){if($O=="")return"";if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$v).")($)?)u",$O,$z))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$v).")($)?)",$O,$z);return
h($z[1]).(isset($z[2])?"":"<i>…</i>");}function
icon_solo($r){return
icon($r,"solo");}function
icon_chevron_down(){return
icon("chevron-down","chevron");}function
icon_chevron_right(){return
icon("chevron-down","chevron-right");}function
icon($r,$Ab=null){$r=h($r);return"<svg class='icon ic-$r $Ab'><use href='".link_files("icons.svg",[])."#$r'/></svg>";}function
checkbox($A,$X,$wb,$dg="",$Yh="",$Ab="",$fg=""){$I="<input type='checkbox' name='$A' value='".h($X)."'".($wb?" checked":"").($fg?" aria-labelledby='$fg'":"").">".($Yh?script("qsl('input').onclick = function () { $Yh };",""):"");return($dg!=""||$Ab?"<label".($Ab?" class='$Ab'":"").">$I".h($dg)."</label>":$I);}function
optionlist($B,$rk=null,$Km=false){$I="";foreach($B
as$Sf=>$V){$ji=[$Sf=>$V];if(is_array($V)){$I
.='<optgroup label="'.h($Sf).'">';$ji=$V;}foreach($ji
as$u=>$W)$I
.='<option'.($Km||is_string($u)?' value="'.h($u).'"':'').($rk!==null&&($Km||is_string($u)?(string)$u:$W)===$rk?' selected':'').'>'.h($W);if(is_array($V))$I
.='</optgroup>';}return$I;}function
html_select($A,$B,$X="",$Xh="",$fg="",$Km=false){static$dg=0;$eg="";if(!$fg&&substr(isset($B[""])?$B[""]:"",0,1)=="("){$dg++;$fg="label-$dg";$eg="<option value='' id='$fg'>".h($B[""]);unset($B[""]);}return"<select name='".h($A)."'".($fg?" aria-labelledby='$fg'":"").">".$eg.optionlist($B,$X,$Km)."</select>".($Xh?script("qsl('select').onchange = function () { $Xh };",""):"");}function
html_radios($A,$B,$X=""){$H="<span class='labels'>";foreach($B
as$u=>$W)$H
.="<label><input type='radio' name='".h($A)."' value='".h($u)."'".($u==$X?" checked":"").">".h($W)."</label>";$H
.="</span>";return$H;}function
confirm($Xg="",$tk="qsl('input')"){return
script("$tk.onclick = () => confirm('".js_escape($Xg?:lang(112))."');","");}function
print_fieldset_start($r,$og,$ef,$Ym=false,$Sk=false){echo"<fieldset id='fieldset-$r' class='closable ".(!$Ym?" closed":"")."'>","<legend><a href='#'>$og</a></legend>",icon($ef,"fieldset-icon jsonly"),"<div class='fieldset-content".($Sk?" sortable":"")."'>";}function
print_fieldset_end($r,$Sk=false){echo"</div>",script("initFieldset('$r');","");if($Sk)echo
script("initSortable('#fieldset-$r .fieldset-content');","");echo"</fieldset>\n";}function
bold($eb,$Ab=""){return($eb?" class='$Ab active'":($Ab?" class='$Ab'":""));}function
js_escape($O){return
str_replace("<","\\x3C",addcslashes($O,"\r\n'\\"));}function
js_escape_key($O){return'"'.str_replace("<","\\x3C",addcslashes($O,"\r\n\t\"\\")).'"';}function
js_escape_re($O){return
addcslashes(preg_quote($O,"/"),"\r\n");}function
pagination($D,$oc){return"<li>".($D==$oc?"<strong>".($D+1)."</strong>":'<a href="'.h(remove_from_uri("page").($D?"&page=$D".($_GET["next"]?"&next=".urlencode($_GET["next"]):""):"")).'">'.($D+1)."</a>")."</li>";}function
print_hidden_fields(array$rj,array$jf=[],$ij=""){$H=false;foreach($rj
as$u=>$W){if(!in_array($u,$jf)){if(is_array($W))print_hidden_fields($W,[],$u);else{$H=true;echo
input_hidden($ij?$ij."[$u]":$u,$W);}}}return$H;}function
hidden_fields_get(){if(sid())echo
input_hidden(session_name(),session_id());if(SERVER!==null)echo
input_hidden(DRIVER,SERVER);echo
input_hidden("username",$_GET["username"]);}function
enum_input($Oa,array$k,$X,$pd=null,$vb=false){preg_match_all("~'((?:[^']|'')*)'~",$k["length"],$_);$Y=$_[1];$Wl=Admin::get()->getSettings()->getEnumAsSelectThreshold();$L=!$vb&&$Wl!==null&&count($Y)>$Wl;$T=$vb?"checkbox":"radio";$za=$L?"selected":"checked";$H=$L?"<select $Oa>":"<span class='labels'>";if($L&&$k["null"]&&$pd!==""){$wb=$X===null?$za:"";$H
.="<option value='__adminneo_empty__' disabled $wb></option>";}if($pd!==null){$wb=(is_array($X)?in_array($pd,$X):$X===$pd)?$za:"";if($L)$H
.="<option value='$pd' $wb>".lang(113)."</option>";else$H
.="<label><input type='$T' $Oa value='$pd' $wb><i>".lang(113)."</i></label>";}foreach($Y
as$W){if($pd===""&&$W==="")continue;$W=stripcslashes(str_replace("''","'",$W));$wb=is_array($X)?in_array($W,$X):$X===$W;$wb=$wb?$za:"";$re=$W===""?("<i>".lang(113)."</i>"):h(Admin::get()->formatFieldValue($W,$k));if($L)$H
.="<option value='".h($W)."' $wb>$re</option>";else$H
.=" <label><input type='$T' $Oa value='".h($W)."' $wb>$re</label>";}$H
.=$L?"</select>":"</span>";return$H;}function
input($k,$X,$p,$Sa=false){$A=h(bracket_escape($k["field"]));$um=Driver::get()->getTypes();$Jf=isset($k["full_type"])&&Admin::get()->detectJson($k["full_type"],$X,true);$Oj=(DIALECT=="mssql"&&$k["auto_increment"]&&!$_POST["clone"]);if($Oj&&!$_POST["save"])$p=null;if(in_array($k["type"],Driver::get()->getUserTypes())){$xd=type_values($um[$k["type"]]);if($xd){$k["type"]="enum";$k["length"]=$xd;}}$Oa=" name='fields[$A]' ".($Sa?" autofocus":"");$ye=(isset($_GET["select"])||$Oj?["orig"=>lang(114)]:[])+Admin::get()->getFieldFunctions($k);$Oe=(in_array($p,$ye)||isset($ye[$p]));echo"<td class='function'>",Driver::get()->getUnconvertFunction($k)." ";if(count($ye)>1){$rk=$p===null||$Oe?$p:"";echo"<select name='function[$A]'>".optionlist($ye,$rk)."</select>",help_script_command("value.replace(/^SQL\$/, '')",true),script("qsl('select').onchange = functionChange;","");}else
echo
h(reset($ye));echo"</td><td>";$uf=Admin::get()->getFieldInput(isset($_GET["edit"])?$_GET["edit"]:null,$k,$Oa,$X,$p);if($uf!="")echo$uf;elseif(preg_match('~bool~',$k["type"]))echo"<input type='hidden'$Oa value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$X)?" checked":"")."$Oa value='1'>";elseif($k["type"]=="enum")echo
enum_input($Oa,$k,$X);elseif($k["type"]=="set"){preg_match_all("~'((?:[^']|'')*)'~",$k["length"],$_);echo"<span class='labels'>";foreach($_[1]as$W){$W=stripcslashes(str_replace("''","'",$W));$wb=$X!==null&&in_array($W,explode(",",$X),true);$wb=$wb?"checked":"";$re=$W===""?("<i>".lang(113)."</i>"):h(Admin::get()->formatFieldValue($W,$k));echo" <label><input type='checkbox' name='fields[$A][]' value='".h($W)."' $wb>$re</label>";}echo"</span>";}elseif(is_blob($k)&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$A'>";elseif($Jf)echo"<textarea $Oa cols='50' rows='12' class='jush-json'>".h($X).'</textarea>';elseif(($Rl=preg_match('~text|lob|memo|json~i',$k["type"]))||preg_match("~\n~",$X)){if($Rl&&DIALECT!="sqlite")$Oa
.=" cols='50' rows='12'";else{$K=min(12,substr_count($X,"\n")+1);$Oa
.=" cols='30' rows='$K'";}echo"<textarea $Oa>".h($X).'</textarea>';}else{$Tg=!preg_match('~int~',$k["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$k["length"],$z)?((preg_match("~binary~",$k["type"])?2:1)*$z[1]+($z[3]?1:0)+($z[2]&&!$k["unsigned"]?1:0)):($um&&$um[$k["type"]]?$um[$k["type"]]+($k["unsigned"]?0:1):0);if(DIALECT=='sql'&&Connection::get()->isMinVersion("5.6")&&preg_match('~time~',$k["type"]))$Tg+=7;echo"<input class='input'".((!$Oe||$p==="")&&preg_match('~(?<!o)int(?!er)~',$k["type"])&&!preg_match('~\[]~',$k["full_type"])?" type='number'":"").($p!="now"?" value='".h($X)."'":" data-last-value='".h($X)."'").($Tg?" data-maxlength='$Tg'":"").(preg_match('~char|binary~',$k["type"])&&$Tg>20?" size='44'":"")."$Oa>";}$Ve=Admin::get()->getFieldInputHint($_GET["edit"],$k,$X);if($Ve!="")echo" <span class='input-hint'>$Ve</span>";if(count($ye)>1)echo
script("qs('select', qsl('td').previousSibling).onchange(null, true);","");$ge=0;foreach($ye
as$u=>$W){if($u===""||!$W)break;$ge++;}if(count($ye)>1)echo
script("qsl('td').oninput = partial(skipOriginal, $ge);");}function
process_input($k){if(is_generated_always($k))return
null;$if=bracket_escape($k["field"]);$p=isset($_POST["function"][$if])?$_POST["function"][$if]:"";if($p=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?idf_escape($k["field"]):false);if($p=="NULL")return
Driver::get()->getNull();if(is_blob($k)&&ini_bool("file_uploads")){$m=get_file("fields-$if");if(!is_string($m))return
false;return
Driver::get()->quoteBinary($m);}$X=isset($_POST["fields"][$if])?$_POST["fields"][$if]:(isset($_FILES["fields"]["name"][$if])?$_FILES["fields"]["name"][$if]:null);if($X===null)return
false;if($k["auto_increment"]&&$X=="")return
null;if($k["type"]=="set")$X=implode(",",(array)$X);if($p=="json"){$X=json_decode($X,true);if(!is_array($X))return
false;return$X;}return
Admin::get()->processFieldInput($k,$X,$p);}function
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$Tj=$zd=[];foreach(table_status("",true)as$P=>$Q){$Bl=Admin::get()->getTableName($Q);if(!isset($Q["Engine"])||$Bl==""||($_POST["tables"]&&!in_array($P,$_POST["tables"])))continue;$H=Connection::get()->query("SELECT".limit("1 FROM ".table($P)," WHERE ".implode(" AND ",Admin::get()->processSelectionSearch(fields($P),[])),1));if($H&&!$H->fetchRow())continue;$x=h(ME."select=".urlencode($P)."&where[0][op]=".urlencode($_GET["where"][0]["op"])."&where[0][val]=".urlencode($_GET["where"][0]["val"]));if($H)$Tj[]="<li><a href='$x'>".icon("search")."$Bl</a></li>";else$zd[]="<div class='error'><a href='$x'>$Bl</a>: ".error()."</div>";}if($Tj)echo"<ul class='links'>\n",implode("\n",$Tj),"</ul>\n";if($zd)echo
implode("\n",$zd),"\n";if(!$Tj&&!$zd)echo"<p class='message'>".lang(79)."</p>\n";}function
help_script($Rl,$Mk=false){return
script("initHelpFor(qsl('select, input'), '".h($Rl)."', $Mk);","");}function
help_script_command($Mb,$Mk=false){return
script("initHelpFor(qsl('select, input'), (value) => { return $Mb; }, $Mk);","");}function
edit_form($P,$l,$J,$Dm){$Bl=Admin::get()->getTableName(table_status1($P,true));$S=$Dm?lang(39):lang(115);page_header("$S: $Bl",["select"=>[$P,$Bl],$S]);if($J===false){echo"<p class='error'>".lang(92)."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";$ld=false;$jn=($Dm&&!isset($_GET["select"])?where_columns($_GET,$l):[]);$ec=(count($jn)!=count($l));if(!$ec)$jn=[];if(!$l)echo"<p class='error'>".lang(116)."\n";else{echo"<table class='box'>".script("qsl('table').onkeydown = onEditingKeydown;");$Sa=!$_POST;foreach($l
as$A=>$k){echo"<tr".(isset($jn[$A])?" class='where-column'":"")."><th>".Admin::get()->getFieldName($k);$u=bracket_escape($A);$i=isset($_GET["preset"][$u])?$_GET["preset"][$u]:null;if($i===null){$i=$k["default"];if($k["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$i,$Lj))$i=$Lj[1];if(DIALECT=="sql"&&preg_match('~binary~',$k["type"]))$i=bin2hex($i);}$X=($J!==null?($J[$A]!=""&&DIALECT=="sql"&&preg_match("~enum|set~",$k["type"])&&is_array($J[$A])?implode(",",$J[$A]):(is_bool($J[$A])?+$J[$A]:$J[$A])):(!$Dm&&$k["auto_increment"]?"":(isset($_GET["select"])?false:$i)));if(!$_POST["save"]&&is_string($X))$X=Admin::get()->formatFieldValue($X,$k);if(($Dm&&!isset($k["privileges"]["update"]))||is_generated_always($k)){echo"<td class='function'></td><td>";if($Dm||!$k["generated"])echo
select_value($X,'',$k,null);else
echo"<code class='jush-".DIALECT."'>",h($X),"</code>";echo"</td>";}else{$ld=true;$p=($_POST["save"]?isset($_POST["function"][$u])?$_POST["function"][$u]:"":($Dm&&preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?"now":($X===false?null:($X!==null?'':'NULL'))));if(!$_POST&&!$Dm&&$X==$k["default"]&&preg_match('~^[\w.]+\(~',$X))$p="SQL";if(preg_match("~time~",$k["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$X)){$X="";$p="now";}if($k["type"]=="uuid"&&$X=="uuid()"){$X="";$p="uuid";}if($Sa!==false)$Sa=($k["auto_increment"]||$p=="now"||$p=="uuid"?null:true);input($k,$X,$p,(bool)$Sa);if($Sa)$Sa=false;}echo"\n";}if(!support("table")&&!fields($P))echo"<tr>"."<th><input class='input' name='field_keys[]'>".script("qsl('input').oninput = fieldChange;","")."<td class='function'>".html_select("field_funs[]",Admin::get()->getFieldFunctions(["null"=>isset($_GET["select"])]))."<td><input class='input' name='field_vals[]'>"."\n";echo"</table>\n",script("initToggles(gid('form'));");if($jn)echo
script("initWhereChange();");}echo"<p>";if($ld){echo"<input type='submit' class='button default' value='".lang(117)."'>\n";if(!isset($_GET["select"])&&$ec){$Oc=($jn&&Admin::get()->getErrors()?" disabled":"");echo"<input type='submit' class='button' name='insert' value='".($Dm?lang(118):lang(119))."' title='Ctrl+Shift+Enter'$Oc>\n",($Dm?script("qsl('input').onclick = function () { return !ajaxForm(this.form, '".js_escape(lang(120))."', this); };"):"");}}echo($Dm?"<input type='submit' class='button' name='delete' value='".lang(121)."'>".confirm()."\n":"");if(isset($_GET["select"]))print_hidden_fields(["check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]]);echo
input_hidden("referer",isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"]),input_hidden("save","1"),input_token(),"</form>\n";}function
file_upload_form_script($oe,$vf){$Lg=ini_get("max_file_uploads");$Rg=ini_get("upload_max_filesize");$Sg=ini_bytes("upload_max_filesize");return
script("initFilesUploadForm('".js_escape($oe)."', '".js_escape($vf)."', "."$Lg, '".js_escape(lang(122,$Lg,"'max_file_uploads'"))."', "."$Sg, '".js_escape(lang(123,$Rg,"'upload_max_filesize'"))."')");}function
compress_alphabet(){return
strtr(implode(range('"','~')),"'\\","!\n");}function
decompress_string($O){$Ha=array_flip(str_split(compress_alphabet()));$v=strlen($O);$Qm=($v?13*($v-1)/2-$Ha[$O[0]]:0);$ab="";$Rj=0;$Sj=0;for($q=1;$q<$v;$q+=2){$Rj=($Rj<<13)+$Ha[$O[$q]]*93+$Ha[$O[$q+1]];$Sj+=13;while($Sj>=8&&$Qm>=8){$Sj-=8;$Qm-=8;$ab
.=chr($Rj>>$Sj);$Rj&=(1<<$Sj)-1;}}if($ab=="")return"";return
function_exists('gzinflate')?gzinflate($ab):inflate($ab);}function
inflate($ab){$pg=[3,4,5,6,7,8,9,10,11,13,15,17,19,23,27,31,35,43,51,59,67,83,99,115,131,163,195,227,258];$qg=[0,0,0,0,0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5,0];$Rc=[1,2,3,4,5,7,9,13,17,25,33,49,65,97,129,193,257,385,513,769,1025,1537,2049,3073,4097,6145,8193,12289,16385,24577];$Tc=[0,0,0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13];$I="";$F=0;do{$ee=inflate_bits($ab,$F,1);$T=inflate_bits($ab,$F,2);if(!$T){$F=($F+7)&~7;$v=inflate_bits($ab,$F,16);$F+=16;$I
.=substr($ab,$F>>3,$v);$F+=$v<<3;}else{if($T==1){$Bg=array_merge(array_fill(0,144,8),array_fill(0,112,9),array_fill(0,24,7),array_fill(0,8,8));$Uc=array_fill(0,30,5);}else{$Ag=inflate_bits($ab,$F,5)+257;$Sc=inflate_bits($ab,$F,5)+1;$C=[16,17,18,0,8,7,9,6,10,5,11,4,12,3,13,2,14,1,15];$dh=array_fill(0,19,0);$ch=inflate_bits($ab,$F,4)+4;for($q=0;$q<$ch;$q++)$dh[$C[$q]]=inflate_bits($ab,$F,3);$eh=inflate_table($dh);$rg=[];while(count($rg)<$Ag+$Sc){$rl=inflate_symbol($ab,$F,$eh);if($rl==16)$rg=array_merge($rg,array_fill(0,inflate_bits($ab,$F,2)+3,end($rg)));elseif($rl==17)$rg=array_merge($rg,array_fill(0,inflate_bits($ab,$F,3)+3,0));elseif($rl==18)$rg=array_merge($rg,array_fill(0,inflate_bits($ab,$F,7)+11,0));else$rg[]=$rl;}$Bg=array_slice($rg,0,$Ag);$Uc=array_slice($rg,$Ag);}$Cg=inflate_table($Bg);$Wc=inflate_table($Uc);while(($rl=inflate_symbol($ab,$F,$Cg))!=256){if($rl<256)$I
.=chr($rl);else{$v=$pg[$rl-257]+inflate_bits($ab,$F,$qg[$rl-257]);$Vc=inflate_symbol($ab,$F,$Wc);$Mh=strlen($I)-$Rc[$Vc]-inflate_bits($ab,$F,$Tc[$Vc]);for($q=0;$q<$v;$q++)$I
.=$I[$Mh+$q];}}}}while(!$ee);return$I;}function
inflate_bits($ab,&$F,$hc){$I=0;for($q=0;$q<$hc;$q++){$I+=((ord($ab[$F>>3])>>($F&7))&1)<<$q;$F++;}return$I;}function
inflate_table(array$rg){$P=[];$Bb=0;for($bb=1;$bb<=max($rg);$bb++){foreach($rg
as$rl=>$v){if($v==$bb){$P[$bb][$Bb]=$rl;$Bb++;}}$Bb<<=1;}return$P;}function
inflate_symbol($ab,&$F,array$P){$Bb=0;$bb=0;do{$Bb=($Bb<<1)+inflate_bits($ab,$F,1);$bb++;}while(!isset($P[$bb][$Bb]));return$P[$bb][$Bb];}if(isset($_GET["file"]))load_compiled_file($_GET["file"]);function
load_compiled_file($n){if($n==""){http_response_code(404);exit;}if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){http_response_code(304);exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");ini_set("zlib.output_compression","1");$Ld=pathinfo($n,PATHINFO_EXTENSION);switch($Ld){case"css":header("Content-Type: text/css; charset=utf-8");break;case"js":header("Content-Type: text/javascript; charset=utf-8");break;case"ico":header("Content-Type: image/x-icon");break;case"png":header("Content-Type: image/png");break;case"svg":header("Content-Type: image/svg+xml");break;}switch($n){case'favicon-blue-0f5ce53a66b1e25395d0048da369f19e__dc9919c6.ico':$f='AAABAAEAICAAAAEAIAC6AQAAFgAAAIlQTkcNChoKAAAADUlIRFIAAAAgAAAAIAgGAAAAc3p69AAAAYFJREFUeNrV1wEEGmEYh/FztCYBRATANhCAAEGAEGZowEUFhM2G6A4QAJksoMi2AYRlAxgcAUgthAS2yTFo5d2DDzbO6r2PhB9APY73z+cUn3+6qbsJcFGCjxlCbPHL2CLEDD5KcG0EPESAH5ArfUeAtDbgCb5BElrjsSbgI8SSD5qAM8SSsyZAbNIErCGWrDQBTYglTe0ZNnCAKB3gJR2iAnwsIBdawEchyRC9jompoYUe3hg9tFCL+dNX2ivo4wEcpTT6EF0AsEMHeTgXyqODnf4M489phC7aeGq00cUIK1s7sLr1DryEWPJCE5DBBJLQBJkkO9DAHnKlPbwkO/AMjuGijCGWiCD/iLDEEGW4f/2WIuA3qnBiZPHIyMKJUcVJe4ZHDJCDc6UcBjhqz/AEMSKMUf9PTA51jBFBAN0X+AKJEWGDr8YGESTGZ02AB7HE0wSk8B6S0DuktDvgYgRRegvXxsuogjnkQnNUrL8NUUSAKUL8NEJMEaB4x4/TG/gDMBOIUjRp9w0AAAAASUVORK5CYII=';break;case'favicon-green-def78cfa7c465c8b0e9966e3eb87407d__dc9919c6.ico':$f='AAABAAEAICAAAAEAIAC+AQAAFgAAAIlQTkcNChoKAAAADUlIRFIAAAAgAAAAIAgGAAAAc3p69AAAAYVJREFUeNpiYOhiAFBfBxBohGEcxs/RmgQQEQDbQAACBAFCmBDgogLCZkN0BwiATBZQZNsAgmwAgyMAuRZCAtvkGLTy7sEHcFbvfWT4AbjH8f75Huq/CXBRgY8lQuzx29gjxBI+KnBtBDxFgJ+QO/1AgKw24AW+Q1La4rkm4DPEkk+agCvEkqsmQKxSBGwhlkSagA7Eko72DNs4QZRO8NIOUQk+1pAbreGjlGaI3ibENNDFEO+MIbpoJHz0jfYKRngCRymLEUQXABzQRxHOjYro4wBJGwAAEaYYoIeXRg8DTBHZ2oHo0TvwGmLJK01ADnNISnPkFAEA2jhC7nSEl2YHmnAMF1VMsEEMAQDE2GCCKlw4RlMT8Ad1OAnyeGbk4SSo46I9wzPGKMC5UwFjnLVneIEYMWZo/SOmgBZmiCGA7g98hSSIscM3Y4cYkuCLJsCDWOJpAjL4CEnpAzLaHXAxhSi9h2vjZVTDCnKjFWqw/jYsI8ACIX4ZIRYIUMbfEW3maO8YAIxSqCXQN5/tAAAAAElFTkSuQmCC';break;case'favicon-orange-cd68622e75276fdf7c60d1e9d4deee14__dc9919c6.ico':$f='AAABAAEAICAAAAEAIAC7AQAAFgAAAIlQTkcNChoKAAAADUlIRFIAAAAgAAAAIAgGAAAAc3p69AAAAYJJREFUeNrV1wHkGnEUwPFztCYBRATANhCAAEGAEGbIwEUFhM2G6A4QAJksoMi2AQTZAAZHAEsthAS2yTFo5f2/OHCcv3v3I+EDcO/reI+f9fO1dVN3E2CjAhcL+NjjX2gPHwu4qMA2EfAUHv5AEvoND1ltwAv8gqS0xXNNwFeIIV80AVeIIVdNgBilCNhCDNloAtoQQ9raNWzhBFE6wUl7iEpwsUoweAUXpTSH6H1MTAMdDPAhNEAHjZih77RbMMQTWEpZDCG6AOCAHooJBhfRw0G/hvHrNEEfXbwMddHHBBtTd2Bz6zvwFmLIG01ADjNISjPk0tyBFo6QhI5w0tyBV7BCNqoYY40AEhFgjTGqsCPfShzwH3VYMfJ4FsrDilHHRbuGZ4xQgJVQASOctWt4ifzeKZooPDK0iSkCCKD7A98hMQLs8CO0iwyM+qYJcCCGOJqADD5DUvqEjPYO2JhAlD7CNvEyqmGZYPASNRh/G5bhYQ4ff0M+5vBQvrfH6W09ALYCTFLvUfsXAAAAAElFTkSuQmCC';break;case'favicon-purple-d4b02fdcc3abcc374a77c65f88513c01__dc9919c6.ico':$f='AAABAAEAICAAAAEAIAC6AQAAFgAAAIlQTkcNChoKAAAADUlIRFIAAAAgAAAAIAgGAAAAc3p69AAAAYFJREFUeNrV1wEEGmEYh/FztCYBRATANhCAAEGAEGYIcFEBYbMhugM0AJksoMi2AYRlAxgcAUgthAS2yTFo5d2DDzbO6r2PhB9APY73z+e8Ln66qbsJcFGCjxlCbPHL2CLEDD5KcG0EPESAH5ArfUeAtDbgCb5BElrjsSbgI8SSD5qAM8SSsyZAbNIErCGWrDQBTYglTe0ZNnCAKB3gJR2iAnwsIBdawEchyRC9iompoYUe3hg9tFCL+dOX2ivo4wEcpTT6EF0AsEMHeTgXyqODnf4M489phC7aeGq00cUIK1s7sLr1DryAWPJcE5DBBJLQBJkkO9DAHnKlPbwkO/AMjuGijCGWiCD/iLDEEGW4f/2WIuA3qnBiZPHIyMKJUcVJe4ZHDJCDc6UcBjhqz/AEMSKMUf9PTA51jBFBAN0X+AKJEWGDr8YGESTGZ02AB7HE0wSk8B6S0DuktDvgYgRRegvXxsuogjnkQnNUrL8NUUSAKUL8NEJMEaB4x4/TG/gD0xZAYUYkFLAAAAAASUVORK5CYII=';break;case'favicon-red-c2ebb34a8df5aba28e15d87728a151df__dc9919c6.ico':$f='AAABAAEAICAAAAEAIAC7AQAAFgAAAIlQTkcNChoKAAAADUlIRFIAAAAgAAAAIAgGAAAAc3p69AAAAYJJREFUeNrV1wHkGnEUwPFztCYBRAQY20AAAgQBQpghwEUFhM2G6A4QAJksoMi2AQTZBhgcAUgthAS2yTFo5f2/OHCcv3v3I+EDcO/reI+f9eOZdVN3E2CjAhcL+NjjX2gPHwu4qMA2EfAUHv5AEvoND1ltwEv8gqS0xQtNwFeIIV80AVeIIVdNgBilCNhCDNloAtoQQ9raNWzhBFE6wUl7iEpwsUoweAUXpTSH6H1MTAMdDPAhNEAHjZih77RbMMQTWEpZDCG6AOCAHooJBhfRw0G/hvHrNEEfXbwKddHHBBtTd2Bz6zvwFmLIG01ADjNISjPk0tyBFo6QhI5w0tyB17BCNqoYY40AEhFgjTGqsCPfShzwH3VYMfJ4HsrDilHHRbuGZ4xQgJVQASOctWt4ifzeKZooPDK0iSkCCKD7A98hMQLs8DO0iwyM+qYJcCCGOJqADD5DUvqEjPYO2JhAlD7CNvEyqmGZYPASNRh/G5bhYQ4ff0M+5vBQvrfH6W09AE8YAEN5XivhAAAAAElFTkSuQmCC';break;case'favicon-blue-17e440832c1eac07527560a0d6f0d2ee__dc9919c6.svg':$f='+<bATb3V?$so%el,wEIwK&mlYjGZ$a8-HGs8y$j)-UBmQx`Cf?>]C6?xmhS1<w
ZNSJl63"aZ]nB<rgk|tG)vC,pHYx;Wb2NVXBMxd!lQ=g0"!mM[]c*SW?7
E^]B7t[fHolNczfsymCW
CM~8Ult[(brlOx`71nDc
H;N~ybgNd*l6LMbRk;>%06rQeBnDc14r_7Z0b9uqO=xV5=22d05hr#V]F`V>ZD,#JA9[$XEV-*TBfz7Z%
rEJw!G!cT+[7>-u:iVU)N$iv%ySN.`._&}sV@FUIuH)%cY-0#OXWPT8t./3Oid8~R]3?9n;/Qs
Y2!K`1Q9d
tys6C=xmJfXFCT%0xl`H&%njK7`N[.q6jET6kV
VqmiJoIrZ#rsP~q0vDN<FH1w9l4R-?A#H:#onn0@0]3dNk3,E{<7r;2q
u)F!d(nhskXC~JT4N!~!g52r#`3hF[%j
oPE~ZV(W_~g#t?WXQCxEe7)ZQKGxei4.gu_R>pAL]HDZf!uSL)$_)^vZ:Xk![_HKh=C|S~PjqHcgjUutq#-~?PmA#<MYyg2R';break;case'favicon-green-bb254c95a033f67e3d433a3df63e160d__dc9919c6.svg':$f='&<bATb3V?$so%eo_t_hLePj9EjS(Ic^3}i[Os!U@i%X`9w9h7
[@
I:KPV|A4v9o?tG^4G8Kl/Za|j!Q;%pO#+U]tOQ@f!m@5m(iU+Xd
:mOOX[.7/V%^=Dj0`KWYnDy%^,cfeZsrK*5P&I[0
$m~<+JorKkYLM7yy$Aw$[w/57.-wX^0JIMH;`
EW/c}a9c

eAhG[68u92FB)
ToD/k0uYFG=PN>>d,^BJ&icL,/i,2N?:udHuPgxk>l07BF^82,>cspK:`^P,q[%?5=4<`h.?.kJ"X_a_mu|Z>-SL,/k)gDk#g):;if"T:5u9
?>JfFYF#KP)D%8xDljc2l^mJ<^%4+Iu=v?FB.Y^67X>9:@KMfCyqQL.-Kmn#DsAler%=^*ps8vI)CEG%8
bknN$znn
D-t3DO&3,C5<7r@2q],)F!d(nhsf)H-JU7?!^"9+Xtpm.q8>NCVHgU$kcVodiJa6[qLov!)IR#}x3*ku*P%3jGe?w?dILBVvhJ~,bFMpBcD%CK/#{0d-|Mu[Pn{fe.3aFbOf6.%,.opHOIuO9';break;case'favicon-orange-53ca3b502d7fb29f01bfbf87fc4d6b24__dc9919c6.svg':$f='&<bATb3V?$so%el,wEIwL!M$a_R(Ic^2cYP0
-+_=VtkTnq5G
]9Lh@D6K6GH(sVGneM;s+raV;M^D!*+KY*IOa=3Z`PmB;9_2Obq7yBg2Z%>bw,v+Y%P7|fhq*F156s0beWd,6`ay=p@_gN3Z=jciDmX_i72]oyww{Z~&Ut4M]*axgl)b!a1ju/uH<*9Xo
=
Ul9*$m_7{*MB>d@6aDhpC0Rt_1D-,/u//bLNxN!Dc[m?bh@-`9rmyGW2b(F%J[Qafv1UZr>a>F6JA8g[!0b@[fJtmTv4e%TYc-0#j"5iK2z=wl
*E@MT<:>g<H|W|2(rwB
g5j1,D7hGqj!`uuvXM9_hJa7+NN~VgB]NK[T3a!z#1A}OTmcvh["nTvNeTw$$;eT6q?q1?CDM!=<1SnN]QTt9lj&eNk(4c>}6hisx;^`kO(cefsRGicdC|"j9npLdO:cM^+&QT[{IL-Wu]3@7yXoO46wh/&9Sd+Vo~(7pzhcG()N3^n#?6MAX=Pd
]uX2I0XtRaah;-u5eKzydbLJw$UJhJ]
fe:Fe6mt>cu';break;case'favicon-purple-4cfd57d31ab991e8071fe34060cd3123__dc9919c6.svg':$f='+<bAU6+V?$so%eoa6[DcHOAP_Cx(^^F;2nJOs!U09RM?Tw1h7?fDMh@D6K6GH(sW:c#bW`9DTcKpCf,1a1E%kD+5q+F]4B,8UB|ML]o,4O$F:-Sutmd.1l:6ly,?7MjE>rM=td#VYm/o}bPN3Z=AH2GFCrK4TIrcvu65o!8H3d"9V^}B7s3mnUM&Wx87ya:7X
mAh1Y7Wu72FB8FRoD/k0uYFG=PV;h%?KNUP%#GQ^
2w"
SkNgvI0*kTGqx))i0|m-597vz$R_y_,e<pwj=Y`"7[1E)$2(fp:Ex.$(:Op<o1CeTD_yBV
#FT:r
y-y?.@i`K.+Yg?j_Hk`_>f$l~LS;5RSh?>{b"
&+evz;:0eol+,pv/]hQo|q*A;p{F69^H{
F#X:3c%4:=Hdpu{k&lMXV@E/#GL#?#)e-WHQiGP@.%eYsSRXBg!n9tO*z&WP*$Up~J1]E18NCC{!iER:u`UBQ5(*#viS-0S(llO)OsT0bALBjGk4Bt}3Hwkvp!81Fs-^5[}2H/0[(gXX++<t3ZZ577
3fS]`cB;^5uWM3tX';break;case'favicon-red-a006e401273230fd6be80568c8361b57__dc9919c6.svg':$f='+<bAU6+V?$so%eoa6[DcEe<SKeo.[BnWu^_0
-+j@96@+X_5GA4^m3%R;yn_USCF5vXi6B6jvyvvy?qZYfND@5~KR9wPw1q,+w{:cwGa2aY)<GPqWy/nLYzy>c3Au_/MA7,dc
}`rf-`x<FH$&bI]FGspJPra.)yE]$w~aKaM]on_y4%i2=`y?0`vYw6}rUy&B3JD1F
/B
o8ro30<1Tp4r,.qWnFpndQQq#ek`C+.f19/9#q+uNg4bc6H.9(02NJtu){yYINu`Uzs:%?o1GH"Lgtxvhu>9uq8)/:th8&TH,OT*]5<Ydlap!w8k>zUUDvh@h+)F+>T8=R`(;8*p2Il^<$eSAzo6lU8L_P_Yh-i#lD(4lV7"52"_UdLUTuCV+Yf/[^)f(~i
.,!Hkau}dsr
i84
>s)_:!#hJQ9:ex&|].#nB#/g7`Ds1xz$U+"f!p-.*YXigq8vUBF!9lVoojveL
+8ox,X;hOaXS*cTW+ODnn.]r,!3BBNvdJ~,brQumcAQJa9E)x*rt!$x~u(xCb
69OF`xI}1->oD?M:yg2R';break;case'apple-touch-icon-blue-f2a5f6f50418d7293b806faf273fe381__dc9919c6.png':$f='iVBORw0KGgoAAAANSUhEUgAAALQAAAC0CAIAAACyr5FlAAAK7UlEQVR42uzSgQAAAAACoP2ln2CDYig9QA7kQA7kQA7kQA7kQA7kQA6QAzmQAzmQAzmQAzmQAzlADuRADuRADuRADuRADuQAOZADOZADOcbeOUa502RxeG0bX17btm3b/tu2bdu2PbZt21bY+6xqPduTSSddSd1zPyTdVZk+p5+prr51f7d83ixVOXXH55Yte6No2r25Qy/O/Pg7OB/4ykFO0cC/4FBma6qq2Tsmb+SVGe9/7f86zWhMFx+HQ5mjs7XmwITMT77LXe+R04WOdFdw+Ka1xOzL7vML7rTLTnd+RMHhW+Z01Owfz911izOE8IMKDl8wh6W9dPHLbsFCOD/Izyo4pB8z3EyG4GPJK5rTqeCQ2HgEGECGeL5MVHBIPAM1CAvh/AkFh5Rvrf/13STr0x/UHpmO4yXzn+7+3pev+VA0puN/fX/hDyk4fOSBkjPg96KN09KRN/KqbuBoSzsnGtNRz8NFwSFBDJRAeDdwCOsqS8/86Nu9gYP4mK25WsGBaXVNlpS8plORFeuP5k/ZkPbVnLj3J0e9Nib8uWEhj/QPvPvz8zd/eAa/54vzfOUgp2hAs6kb0uhCR7rXN1s0I42AN7dNDxxYY/CG3sCB1+wb66dwNLVaQ5Jqlu/L/XxWLPf+hvdOu8X5qS9mxS7fnxuaVNvcZtXcaiyI6IeDN9KyFW/3Bg6eTf4FR1p+0/ydWS+ODL3xfe6lsc6feGlk6IKdWWkFzW5Za+WG6YNDzF5bWIx1GQ7cUpXr+3DklLQs3pP9zNBg7plX/NmhIUv25OSWuv4KwFK7TjhsjZXic2dRQsYH3/h3OFLP6oSDP+rLcDAV6DM3jttjEu83P961gYRUDJ1wVGz8wtZYIb7Wn136b41bk0/phKNs+Zu+CUd6QbPAwmzeb158ZlHPECFVRy8cGz6nsdNuE5OP0kUv/gscCUd0wlE07T5fg4PFgQ3HCni5MCEWwrm8zScKNd3G7EEnHDX7xnGkes9occTe3pgz8I//HgDVAUfu0Et8Cg5eQxi6xT0wuQ9amNDSbtN0GBEOnXDUn1nytxEi6aQ42JEbIRo3hW/XCQfRDt+Bw2JzvDk+QgoshL89MdJqc7gRjqawrRzBs774ibWuWByvOzabg3hj4Fp/hGPaxnSJsBA+a2uGGx8rLXGHxPHCSXc47Vax0F8853EO1p2c73ePFeJO0mEhPDKtzl0T0raUM+I4XrVjiDhlb67J7vdrQp9+NyGdKuGwIXzWlgx3vcoS4/q3s4wl4mx7RkDlxi/97lWWuKe8cLw6JtzFIJgOOLI++6G1pkA06CyI9bsgmKRYiDdbF8PnOuDAC8bf7LRZRBuZwucKjps+OKNn4c1lOPDKLf1EG19beFNwsGTfGzjw5ug9oplPLdkrOEj2IfDQGzgyP/2+EMr6UrKPgkOkCboOB54/5jqntVP+NEEFh0sJxuQPc6QbL5n/jN8lGN/+8Vl54bjrs3NKmmCgfTg1Wl44Pp0Zo0RNBtri3dnywkE2q5JDGmikz4gcDrn81o/OksjodSE1ZPiykHrt4XwZ4dh4vMA1OTXPF3c+TcSY4ZNwOJzOT6bHyEUG2ginUxVv8Yi1d9oHL06UhYzhS5M6uuzuKPs00dWyTxP9q+wT/4hrDuUhGzF1YOP9M0jl3CuWJODdvUpWOM1oTAzUTwvGRafXvzHOpCmDb02IiM2oN6zUZC5L7aRikKpDKhfDA84HvnKQUzTQVKlJhpDTkZXPDQ8xDxYvjAg9G13ldKoiteYwu915LKyceR8juRcfImhoj4dXOBym5EKVmqxu6Nx0vACBvCexeH1c+JYThTUNXZoyKVT25bUdjCWUWkDobIh+elQYpRmOh5VX1HZoyswAxwdTo4orexzga2yxBCXU8M89fVP6l3Niqb1xS0/CrDSmC4U66M6PBCfUNLZatR5aQXnbR9OiFRzGLtkz9+x9VQymBRV1ncSzk3MbUQwExFefCK/YF1C6P6CUD3yNSqvjFA1o1vs5BHQ+PSRYLNkbZQoO/LOZMUJAZn5Dovfx9GiRz2GsKTjwdydFVtV3aqY3Bp63J0RwwQoOj2aCPdQ3ICK1TjOxhSXXPtAngEtVcHgnTZBFuIiUOhNiwfRTXKSCwxPWTaz6XEyVw9tRSS7gTFSliOgrOEyUYIxY8nBImc3uBUT4oweDSp8fLmIqCg5TZp8/3C9gxPKk3eeKqd1m6FDCj/Ouu+tc8fBlSUyAxAUoOOSQJjzw1QUK62w9WUgFN8IVblnESc1ropjTwIUJ9391wQBpgjdM6Vbu/uw8AZIxK5PnbMskF4ShheVcgl1ZRS2VdZ2dlr/l4/CBrySrEhyjAc1oPHtrJh3pjrzAAN2K6U2Jmm7/5JyhuhgFh7HG/ZNXmsDQpeAw0N6bHCUvHGrhzVibuTlDXjjmbs9UcBho7GshLxyE6RQcxoYgqekpIxls3UJcRMFhuCKSab901cCEFlLBYawt25cjFxwrD+SqNEHPGYp1WchYdTBP5ZB62rafLjK/4o0Aq+YVU9nnF+KqHxsQZE4yHh8YRFazhik4vCiqZkc3UxXtIFt90a5sIZ5WcHjZ8spa2bLJ608ZLqDvvLj8slbNbKZETUha5m3PvO/LC57HguV7BrDiqnZNmbfg0JNR3GWxkw+Gbva2jwwvPcgq7pezY4+ElHdZHfovXsFhiDH/Z2s3nY2hhNxj/qHdK53l2fH62PCFu7K52TChv1oVsCo4DLS/ZkUkZjdqPbSGFktsZv2+CyWsfjEtQH+mU49PM/aqpQsPLCRx/AjyNa2HRi8uG9eMNpXs88SgoNqmrt6nBCN5RW9NYDspt5ExhoUxnA985SCnaND7dGXU948OCPRcso/KBHtqcHBhRZtmessvb3tyUJDKBPOE/dsLgni+mNPisxrEq5OCw3D7z9VOahoz8dRMZmQpM2Pl8rycQ6oSjJkwMlEwlRaSp55KMPa0dZ+hyVqG0+nN0nUU9vhgSpTKPjepNIEiT7xwincZz1htY9feCyUUEVTSBDl0K+zTSfhLxKncbkx0wlNq5+3IemV0mCl0KwoOBGcuSJWoA0YtL7ck6mWXtKCFpKSkC/In3lw005rSrTzYJ+DN8RGUfhu7KoVxhZ0MWIUJSapJy2+iKCBjDOMBH/gaklhzKLiMBjSjMV0orEB38+pWFByUjpRXmjBjc4aCw0Cj3oG8cDBTVnAYaCx6oTiVkYx7vjjf1GpVcBhrpPnLCAdL9irZx3Br67SJEn2yOMWGPJRSqtIEqb4lFxykiqk0Qc8Zy1qykLF0b44mv0m2jdeoFcnmJ2Pc6hTN86ayzyl/TnKvmclALUEimYLDaxs0kdppTjJ48Nm9TobSrZD4effn500V0kCnqQlTcHhdzmSSbSLZI6Gk2jTSJgWHKPpzNLT8SZGC5XFH7sDufyLbSMFhxp1vtp4qonCxJ7EgKEc9CDn2B1IbALa220jgQJdmNBY8y2CRoK0miyk4hFEbn2oIbq/hgapq8Z7sPKnV9AoOMR1BkMjSF9XsH+kX6BoQj/QPpMb+uiP5qFF0TCwUHHIade/ZTnzJnpyJ61IHL0pgdz7yQFEskvlHTiEf+MpBTtGA4DevylLsJ/endulABgAAAGCQv/U9vmJIDuRADuRADpADOZADOZADOZADOZDjBTmQAzmQAzmQAzmQAzmQA+RADuRADuRADuRADuRADpADOZADOZADOZADOZADOSAe34f5izVe/wAAAABJRU5ErkJggg==';break;case'apple-touch-icon-green-903cc109ea077cd9e91508416c5e335a__dc9919c6.png':$f='iVBORw0KGgoAAAANSUhEUgAAALQAAAC0CAIAAACyr5FlAAALGklEQVR42uzSgQAAAAACoP2ln2CDYig9QA7kQA7kQA7kQA7kQA7kQA6QAzmQAzmQAzmQAzmQAzlADuRADuRADuRADuRADuQAOZADOZADOcbeOUc7syxR/Nq2bdu2bdu27fvZtm3b9nds2z5J+v2e+vGeOyeZSbqT2qv+GHRPZq3ZaVTXro56NBYklUz5LafzQxnfX5783rHxz+6EccApF7lFgdgih6C5oqBo1KcpH50c9+Q2f2oUozBVopwcAn99ddHYL+Of25mv3iqjChWpLuSITlStGp346n586aCN6jxEyBFdCPiLxnzB13XFaEJ4oJAjGuBvrM3ucC8f1UXjgTzWenJIm+EyMzQ/Ot6nAgGLySGgC/CAGbp/+cpicsgIlE/oqfETVpJDZq3/d26S8PxuxRN/+Ltltbm15W+f2/NpXZiK/3f+wg8JOaKkQ0l681BdJtBYl/LRKS2Qo2bLbF2Yik46FwvIIT5QHOF/Sg7QkLM1/pkdQyEH/rHmykIhB1AFNQUr8lYMjx/+0/KfXpzx4s2jb750yKVn9T/rhN4nHNr10L067LVDmx2wvTvuzSkXuUUBir008yWqUJHqhbWFykvg8OazOSEHKF/YNxRyYEWjP4tRcpTWl05NnfrF4i+uH3k9336b37ZxxXjUDSNv+HLxl9PSppXVlylXwYKIc3IwI83p+mgo5KBvii1yrMpf9f7890/pc8q2v23Lt/TU+IlT+576wfwPVuevdmWtlQ/mjBx69FrFYmzQ5MAaC5KjnxybijZ9svCT43odxzeLiB3f6/hPF326uXizChYstTskR3N5vj6uz1gX99R2/02OzbMckoMfjWZyMBS4ZfQtfB5D7PYxtwfXkBCK4ZAcef1eai7P06elszr9V+HqjdMdkiOny8PRSY41BWs0LUyz28bctq5wnWoNCNVxSo6+L1I44GvWg4/s9nf/BznWTXRIjozvr4g2cgRU4JcVvzC54DMYa7ze76t+V47B6MEhOYpGf86VwpGf6Cu+2vKktw7/LweoE3Ikv3dcVJGDaQhNt/4Ghtvd4+4ubyhXDoCHwyE5Smd2/EcLsWGavliXvEwXrlg6xCE58HZEDzkafA3nDTzPClpou3DQhY2+RhfJUbFk0D986i/t1VSSqa+XTP7l79fL5/eKRXK8MusVi2ih7c05b7rYrVStGa+vp399UcDXpBf6M3+9kYsl09rEXLeC38k6WmibnTHbrQFpzaaZXNFWMPRdfctXWZT4+oG4PmNuQPryzJftJccbc95wayqLj+u/7tKW6Lu1cfPy+70cc1NZ/J72kuPM/mc6cIIFSY6EF3ZvKkrTBerTVsecE8xSWuiZrQP3eZDkwNK+ODfQ3KjL2OQ+F3Js//v2ThbegiYHlj/wdV0m2hbehBws2YdCDqxy5UhdLKqW7IUcBPvgeAiFHPHP76qFstEU7CPk0GGCwZMDS/30jEBTvdVhgkKO4AOMiR9uOcA4q81tMRdgvEu7Xewlxx4d9hBpgoe4YtgV9pLj2hHXiqjJQ3y88GN7yUE0q8ghPQThMzqGwy7bqe1OBDJGXEgNM6JZSP3D8h9sJMevK38NTk5N/+Jmb6LbjKgkhz/gv3r41XYxA20EoWuSvCUcqG6svnf8vbYw48GJD9Y01biR9umrYNM+fRVbaZ/4I3637DutTDHTtvt9O6Ry7oolcXi3rJLVRjEK4wON0YRxczPnnjvgXDOZcf7A8+dnzfcs1WQyS+2EYhCqQygXzQPGAadc5BYFlKSapAkZET/ixN4nmkOLk/ucPDphNC8mSWqNQLO/edCWQYz7aMkj2ImgoR2ydYgv4FMGQlJN5lTn/LbyNwTy4aTFOQPOabOqTW51rhJYobJPr0inLSHVAkJnL/TTp/U9jdQMg7cOzqjMUCZAyHH50MuTylqd1bu4rnhSyiT+3K/OevWmUTeRe2PHNjs6pwKFqUKiDqrzkMkpk0vqSlQrEV8af+WwK4Uc3i7ZM/YMPSsGw4LMykz82ctzl6MYmJA8YWjc0J4bevba2IsDTudkzOEWBSgW+hgCdh7b81i9ZO8VhBzYdSOu0wIy84FE76phV/HaQo4wBftcPPji7KpsZTxoeC4YdAEvLOQIayTYQV0OmpU+SxmM6WnTD+h8AK8q5IhMmCCLcDPTZxpIC4af+iWFHOFAC77qMYljWLBVEQUvMDJhpPboCzkMCjBGLNl/c/8mf5MKO/jRPpv6nNT7JP0yQg4To88P7nrww5Me7rquK7nbPF3g4OHMdbus6/LQxIcYAOkXEHLYIU3Yv/P+JNZpu7otGdxwV7iyiLMybyXJnO4ad9d+nfbzQpoQAYhuZc8Oe+IgeXzK42/PfZtYEJoWlnNxdq0vXJ9VlVXb9I/ISg44JVgV5xgFKEbht+a8RUWqIy/wQLdiPETUtGu7XT3TxQg5vAffz15pAk2XkMNDXDLkEnvJIQtv3uL12a/bS4535r4j5PAQ7GthLzlw0wk5vHVBktPTRmawdQt+ESGH54pIhv3WZQPTWkghh7f4fNHndpHj6yVfS5hg+IBi3RZmfLP0G4khDTc6rOlgvuINB6uKCCT6fHzS+MO7HW4mM47ofgRRzQoIOSIoqmZHN6OSdhCt/tGCj7R4WsgRYWwp3sKWTRHvZXiBW0ffurVkqzINImpC0vLuvHf37bRv+GnB8j0NWHJZ5OTLQg4nEcV1zXXEg6Gb3bntzl5zglXcG0fdOGDzgPrmekcv7x2EHIz/2drNYWFYQuwxf2iks+72HWcPOPvDBR/yseGE82xVkFXI4SH+HhWxJGeJaiWKaosWZC3osaEHq18MC9CfOdTjU4y9aqlCh4UkjocgX1OtBLV4bUx5DQn2ObL7kfk1+aGHBCN5RW+NY3tZ7jLaGBbGMA445SK3KBB6uDLq+8O6HRa+YB+JBDum5zEJpQnKeMSVxB3V4yiJBAsH/muCoPsXM7Eoe5GeOgk5PMf/rnaS05iBpzIMRCkzYuX1JIY0wgHGDBgZKBilhaTXkwDjcKPlCE3WMiIYUMNPk9jjsqGXGRp9LtIEkjwx4dRzmfAgryav+/ruJBEUaYIduhX26cT9pf1UroOBzoy0Ge/Ne++MfmeIbsUIciA4C0KqRB4wcnm5Eqi3sWgjWkhSSgYhf2LmooyF6FYO7HLgeQPPI/Xbk1OfpF1hJwNWYaamTl2Vv4qkgLQxtAcccDoldUq/Tf0oQDEKU4XEClQX3Yq55CB1pL3ShNdmvybk8BDkO7CXHIyUhRwegkUvFKc2MmPvjnuX1pcKObwFYf42koMlewn28RxVjVU6RZ8tRrKhMIWUSpgg2bfsIgehYhImGD6wrGULMz5b9JmyDPZv4/Xo5EfNZ8ZTU59S4YdEn5P+nOBek5mBWoJAMiFHxDZoIrTTTGbQ8fF6KrIQ3QqBn3t12MsolwY6TaUh5Ii4nMmQbSLZIyGlPEUZAiGHTvozcMvAo3scHSlaIHdg9z8dbSTkMHHnm3ar25G4OJy0wClHPgg79geSDQArGioI4ECX5jUt6MvgIk5bJbCFHBrkxicbgus5PFBVfbLwE6T9SmAvOfRwBEEiS19ksz+k6yHBEeLQroeSY//H5T+iRnE+sBByWAby3rOd+KeLPn1u+nP3jL+H3fmIA0WxSOQfMYUccMpFblEA5zdTZSv2k/tLu3QgAwAAADDI3/oeXzEkB3IgB3IgB8iBHMiBHMiBHMiBHMjxghzIgRzIgRzIgRzIgRzIAXIgB3IgB3IgB3IgB3IgB8iBHMiBHMiBHMiBHMiBHBDxnn9eIYwbtwAAAABJRU5ErkJggg==';break;case'apple-touch-icon-orange-6efda14fd1d3c45382c67d7f324bdccf__dc9919c6.png':$f='iVBORw0KGgoAAAANSUhEUgAAALQAAAC0CAMAAAAKE/YAAAAC1lBMVEX//////v7yyqv//v3qrX399/PZaRL44tLjkFH9+fX007rpp3XWXgLXYQbbciH34M7jkVLggzvkk1byyqz007nYZg7+/Pr228b++vfqrHzhiEP33svii0j67OH67eL++fb//fzwxaTopXH+/PvstInwwp/++/njj0/XYAXabhv559rZaBHhiUXhiETbcyLii0npqXjZahXghT/YZQz228f66+D23crXYQfijEr45NX45NTz0LX89e/ffzXZaBLstYvhh0Lww6DpqHb9+PTabBfefTPjj07opHD12MLxyqv22sTbbx378OfbcSDcdSX89O7aaxb67ePz0bbnnmfpqnnjkFDmm2Lll1vbbxzklFbffzb11r/abBjkllnghkHkk1X23cntt43338ztupL44tHijEnqrH3oom355tjlmV7z0LbopG/rsYXvwZ3fgDfccyPfgjvuvpjoo2700rjopXLzz7TvwZ777uTstYr45dbbcB788er11r7ZaxX66t7YZAvww6HbcR/tuZH00rnrroDcdSbnomz01b3eezDwxaPstoz34c/zz7PdeCr55tfxyKn56dzzzrHnn2jfgjrnoGrpp3Txyan77+fklVn99fD88uruvZfmnmb77+bqq3vefjT++/jZaRPtt47vwJzefTLdei301Lzvv5rxxqXyzbDnoWvfgTjnoGnhiUb77uXttozeey/qrn/99/LZahTtuJDuu5TpqXflmV/ijUvlmF389O3mnWXvvpnddynwxKLssobxx6f88+z669/YZw/23MjhikfghD7deSzabhrfgTnyzK/YZQ388uv118DzzrLxyKjrsILuvJX449Pdei7cdCTghD3z0bfbciLggzzklFfss4jWXgHXYwnXYgj78enefDH56NrstIr56Nvqqnncdif78OjYYwropnPqq3rXYgfcdijqrX7XYAT01LvrsYTWXQDabRnWXwPcHI28AAAFZElEQVR42uzBgQAAAACAoP2pF6kCAAAAAACYHXuAjqRLwzj+1MS27Yw939i2Z23btm2PbduKbdvdwZMce42u2uqkC3NuLX5Hcf6Nqnve9z/e/3V0hoYEBgQEhoR2duA/Qm93Dx30dPfC6jzcbVSwuXvA0iZ5UoXnJFiXWwydcHeDRfl40ylvH1iSmzfHESzBitw5rkFLXoOcgAWvRg9P/lXfyMjIu/lPd//8aR//ytPDsm+OUQBJdv7DMIBRq75BegMcorEoWy3a1vbK/nvOs6cPcwura0YjhoZejtZUF+YeevosZ6L/103HaHxYLZoDMN+DJTGxEXQiYvp7du+AUz3yaOl3atF2mGxBv72ZE2h+/Os4qOqgPBo3AlWi2QUTec2aQRd9dG8V/l2nLPq3AIL8+Re+suhOmCYnnprci4NSqCz60GEA7+Rf1MmiG2GSd8RTs4VBkAuRRT8MSQOkLJL8uSy6AaaQmoaow9AKyATKoo9wFoClk0lOkkXXwwwP7lGnrKVwECCL/gb5AQBRJFNk0TaY4Ngp6rYs02n0EzIiAUATedb86F/QgPc6fXsUkXxDJOA2jT8w/e2xm4YMO7sQv0mSqQC++OUB0y/EPTTkO85uecP8iyIAX9pj+i3PTkNOKw4XRfRXvwIgzvTDhcYMKY5xRTTbMwGYfozTmDH8S49KNC8oou2wVnS3WjR/I48esFh0r00tuqUDzocA8dFwV4tmfrT6uCU0Wn2wvct/Wmj6YNtKQ+YKWSE00JC3CFnW3KEhMULWYkFDNCDAS8MCMtgHZhmhAelQcnOnE4MSTOM2h7rFSqKW6h7e1Omta6DGY9BGBdugB8wlVTRTB/+ncKZ3wE4H9oE2mO+77dRs3Rsxnq7OxoZ6m62+obGzC6+GVL6dmvRskiBe2pNYf7rIf3pKMizis5+rpgu+d/UKLOXFk4eP6Vzzjw99fz7E+9gUKJSUXb10uSabMtk1uZeu/ug5FH5SCxG4fQfUJCd4zRs+f+7R2XPnz8zzSkiGmpLAMYhArsyETsfyKCqaH9kFXRKOUlw0d/pCh7qbFBlNzgnXnFxLCo4m14W5wWVuP20nLRBN2jMi4ZLIk98mxUY7WNtYWSVhXJLX7dfvJEVHKxzPentcMlSlvW3F+uV0JDRaoXjlgQ9WVJaf+WGiD+CTGDRcXlnxiQMr51JJfLSqllaqExrdQkOKIcJFGlILES7QkA0Q4RkNCYMIbstoQKEEIYLGqNuQFwQ5Qt2uQ5gY6vQtCHRL34apEkIVTaZmryuDYB79Q9Qke/UaiLc4vpkua565CNYwJXUqXbK8vwtiOc60SRmxr3ECrdNWRct/XQD/JjhKCu+vpjPN1/x8o+Fo5DWIwLHNUPhD6cc3zAz0pwP/GTNTH5WWQKF0TNQQsGUr1EQ+f+EVFR4WFh7l9eJ5JNRcKRA3uWx7H3T5/BdEjlvLN0OHjVPFzohDd5KgkY/fkPDBdkY4NKnbZolpvLZMgouk87+0zArh8aOtcMHhgz3W2nuc7lecHgpJn/xUPknR0XOp0HL5qhdUvWvF9FYqTLXO3mP2qdz7/ekZSxbMj06av+DTn0nvv5/bPts6e4+HNORXEOE2DXkEEUqKacDLBxDiOg0YgRg3blK3tWsgyEnqtgrC+FGnAYgjvZm6vB8iZU6jDvGRECotlZr5pUG0sAhq8rIIFjClnRoc/Rkswe1rX6eLAlMkWMWxE8fpgpu3MmElv796jRNoP3EDllO1ejKd2jJrMazJrXRk/ZuoxNGsD22UYGmJm/bu35eXX9DaUpCft2//QNgu/Kk9OBYAAAAAGORvPYi9FRsAAAAAAAAABIY9HLkA16UTAAAAAElFTkSuQmCC';break;case'apple-touch-icon-purple-2388fa66883b7c5e6b4cf5c795eae8fc__dc9919c6.png':$f='iVBORw0KGgoAAAANSUhEUgAAALQAAAC0CAIAAACyr5FlAAAKzUlEQVR42uzSgQAAAAACoP2ln2CDYig9QA7kQA7kQA7kQA7kQA7kQA6QAzmQAzmQAzmQAzmQAzlADuRADuRADuRADuRADuQAOZADOZADOcbeOQBJz2xh+No2Cte2bdu2bdv4bNu2sbZtewfr3XFyn6v+/X/ZmckknTmnuqpmMt3ZVOXZxunznva8RcY6AueWDK39WN+fX935oye2fvFeFD7wlYv8RIXsgkMsNjXmO/LLrp89veWzd7lhoRqVaeJxOMQSoVnf8d+2funevPVFFZrQkOYChzdtpuJo+zcfxptOutCcmwgc3jIj4Tv2G95uWgpdCDcUOLxgicj84KoPpgULVbght9UeDukzFBlp5mP1h0zD0BgOMYYAG8hQ48vvNIZDZqA2YaEKf0JLOGTVertrk7Yv389/+i//LQPL3nnn73548+dVZRre7vqFP+QROGRA6fjuY1UdI7LQ9bNn3Akcc01XVWUaWhhcdIBDfKA4wm8IBxYeam79wj1TgQP/WGx63BQ4sLlAZKRhqvXiSOm27kt/bDr6jap9ny3b+ZHire8pWP+m3FWvvLbshZcpq191ja9c5CcqUO3yn5poQkOazwcjpp2Gw5vXZgUObDJ/eypwUHxHf5WlcISmot0FvsJ1nYe/Wsm7X/K8S2kp3Orw1yq5bU+hPzQdNdNqbIhYh4MV6dD6T6YCB2NTdsEx2jSVu6xt2/sLlzyfd2lv4U9s/0Bh7vK20abptOy18sKswaFmrzNsxiYNByUy1ul9OHwdM/mr2re8K5935kjZ8u6CgtUd/s7klwBstVuEIzY5qj6H+mpaPne3W8PReMUiHPxRL8PBVODoN6t4PS4px75dnVxHQiiGRThGdnwtNjmivgavrLlV5dn6ixbhGFr3cW/CMdY87RgWFhAZb10cIoTqWIVj+1epbMRjavIxuPL9t4Cj5rRFOPr+/BrPwWGYZdt7WFy4EAtVeLyKXb2mZWP2YBEO39Ffc2X88C/Ulfj8ZMf3Hn9rB6gFODp/9CRPwcEyhP9L9Q5cXk58vyY8EzMtGB4Oi3AEL6/+Xw9Rd0FdXOgsUZWnivdZhANvh3fgiEcSuz9eogUWquz5ZGk8mkgjHFNFe/57se1rD4oG+tX1wNl//Pf6ZO6WbITjyp+bNcJClWt/b0njsDJTdVJd7/39y4x4VG309//zrVwMXFiWdcMKfiftsFClrzSQrgnpXMNldZ0ytv+H6qf4tK/924/E9Zl1E9LLf1Ldhgc7D+tLWXxct/qVvkT9Ot+SM7rj61m3lMXvqS8cOz9cnKQTzAIcbV+5f9TXoyqEeiqzzgmmKRZqZZuk+9wCHJSe37zQiEVUHZ3c5wLH0hdcTn7jzQIclNHd31Z1vLbxJnCwZZ8KHJTp8sOqmqe27AUOgn1wPKQCR+uX76uEst4J9hE4VJhgKnBQun/5HCMa8lSYoMBhPcCY+GGu3EkZWPaurAswXvHSK/rCseoVV0WaYKMd+Hy5vnAc+nKFiJpsNGK99IWDsFORQ9pohM+oGA69yvIXXyGQ0XEhNWR4WUhduqVbRzjKd/QkJ6dO4/jCaKL6DG/CYSSMg1+q0IsMtBGmIclbMmKR+fjJH9TqQsbpn9RFF+LpSPv0u2TTPv0uy9I+GWbJ5q4lz3e7YwOpXHrFkji8lUr2zgvVqIwPNEsTxvWXB3d9zKUhg7s/UTJQGbQt1WQnW+2EYhCqQygX3QOFD3zlIj9RwZRUk3QhrZdGkba6B4tt7ytsuzJmGpKk1h2WiBtNZ4eZ99GTOziIoKFtPjdiJFzJhaSanB0Ple/sQSCfSSx2fbS4YnfvrC9simmhsp8aXqAvIdUCQmd79NNFpGZoPjc8PbJgirkBjv2fK5voX7SDb2Ey0pXn45/7yl+aj3y9kgnKshctYvShMk1I1EFzbtKV71uYjJqLtGDP3IEvlAsc9m7Z855Sz4rBtGB6JIQ/e7h+EsVAZ854y/mRuqOD9ccG+cDXvrIAP1GBaqnPIaBz8zvz1Za9XSZwUA59pUIJyNxvSPQOfLFcxXPYawIHZe+nS2fGQqbrjY5nzydLeGCBI6ORYOvekNNbEjBdbD1F/rWvy+FRBQ5nwgTZhHMhImDB9NNCmKCdJnAoX3X71THHvU88QNvlUeXRFzhcFGCMWLLx1FAi5gAi/NGGE4Nb36t8KgKHK6PP178x58xP62oO9ZO7zTTs3dZhrVtzsJ9NeSZA6gEEDj2kCWtfd53EOpV7esngRp+flk2ckcYpkjmd+F7Nmtdet/wkAof9lmJyWRwk535Zf/2frcSC0LWwnYuza7xtZmY0FA39Lx6HD3wlWBXnGBWoRuVr/2ilIc2RF9itW3GjiahpxUuv2qaLETjsN96fvtIEui6Bw0bb95kyfeGQjTd77erfWvSF4/qSVoHDRuNcC33hwE0ncNjrgiSnp45kcHSLaUiwj/2KSKb92mUDU1pIgcNeK1jboRccRRs6JUwwc4ZiXRcyijd2SQxppq1qX5/7FW84WE1HTKLPO66Pb3hLnjvJ2PjWPKKaTUzgcFBUzYlurkraQbR63sp2JZ4WOBw2f9csRzY5PsrwAEe/VRXonjXdZiJqQtKSs7R1zWuuZx4Ltu/pwHgAU8wpOKyEi8bCceLB0M0uf4ntqQfZxUUl1Xh6OBZOWHp4+0zgYP7P0W4WK0MJ74N/aKSz6R07EMfmrWjn5jBhPVsVsJq2msRzwMdQ7eRiG85PRMiWUXd0gN0vpgX/1Z9ZXI5yVi1NGLCQxHET5GvmIo1W3Idi2m0S7LPxbXlz/nDqIcFIXtFb+xBF1k3SDbAxRuEDX7nIT1RIPVwZ9f2GN+dmLthHIsE2vyM/2Dtnut4C3XOb3p4nkWCZsFstENT44k4brJ5QSyeBw3a77W4nOY2ZeJouM6KUmbHyeA7HkEqAMRNGJgqu0kIy6kmAsSvgUBGa7GU4GVBjmCT2IL2MRJ+7VJpAkicWnGotkxnjz9UeGSCJoB7SBNGtcE4n7i/lp0q7MdHpKfbnLG3b8aEi0a24Ao4kBGdIXfBwk8srLYF6vvYZtJCklExC/sTKxXStiW5l3etzdn+8hNRv53/VQL/CSQbswnQX+EabpkgKSB9Df8AHvnKx4eQQFahGZZqQWIHmoltxLxykjtRXmnD1ry0Ch41GvgN94WCmLHDYaGx6oTjVkYzVr7oWmooKHPYaYf46wsGWvQT72G6RuZhK0adLIdlQhkJKJUyQ7Ft6wUGomIQJZs7Y1tKFjII1Hab+ptkxXmd/Xu9+Ms7/usHMvEn0OenPcX26mQzUEgSSCRyOHdBEaKc7yWDg4/FMZ010KwR+usr5gUsDnabpuAkcSs7kkmMiOSNhcsA10iaBQyX9aTozvOnt+U5hgdyB0/9UtJHA4caTbyr39JG4OJNY4JQjH4Qe5wPJAYDh2RgBHOjS7MaCsQwWcdqaYrrAoYzc+GRDSHsOD1RVRL0j7TfF9IVDTUcQJLL1RTb79W/KTQ4IGpJjv3RrN2oUmVhoBod1I+89x4kXrO64+LvGkz+o4XQ+4kBRLBL5R0whH/jKRX6iAs5vlspanCf3r3bpQAYAAABgkL/1Pb5iSA7kQA7kQA6QAzmQAzmQAzmQAzmQ4wU5kAM5kAM5kAM5kAM5kAPkQA7kQA7kQA7kQA7kQA6QAzmQAzmQAzmQAzmQAzkgDe9KFu6DmR8AAAAASUVORK5CYII=';break;case'apple-touch-icon-red-507228751d2170d047e72142d2c02390__dc9919c6.png':$f='iVBORw0KGgoAAAANSUhEUgAAALQAAAC0CAIAAACyr5FlAAALAUlEQVR42uzSgQAAAAACoP2ln2CDYig9QA7kQA7kQA7kQA7kQA7kQA6QAzmQAzmQAzmQAzmQAzlADuRADuRADuRADuRADuQAOZADOZADOcbeOUe58yxR/Gfbtm3btm3btm3btrW2jWRtZG3MvM9TP/72O5vMTLqTuqf+GHRn55y+26iuWx3zGG+v7v7ukeZnT6i/d9fANWtXnL0AxgW3POQVBeKLHILJvvbOT24O3rBh+elzzdEoRmGqCDliHNOjg52f315xzoK0+qyMKlSkemySQzCQ/WnVxcvQ0mEb1fmR2CKHwJru/Ow2WtcVowvhB2OBHILp8eGmp46mUV00fpCfFXIY32coZrjMj6ePsS1LyGEwGAI8YIYaX+4Qchg8A6UJPTX+hJHkkFXr/12bVJ67SNfX9/3dGh87eOa2b3n5TFWYiv93/cIfEnLEyIBSffnKqow1PhK8YaMZyDFU+qsqTEUHg4sJ5BAfKI7wOZIDjDWXVZw1fyTkwD822d8h5AD2eFd7f2Fm+7cf1r/4QMUt5xecdWDucTtnHbpF+j7rpey8ctKWS/yxwXxY0lZLcstDXlGAYhW3XkAVKlJ9vLvD9hI4vGk2J+QAvUmvR0IOrPPTW+KUHBO9oe6E72ueuC3/9H1p+9/XncsV46cKztiv5snbuxN/mOzrsV0FGyLOycGKtPn5kyMhB2NTfJGjvyg78MC1GQds9Pt6c9OWnhp/IvOAjQMPXjdQnOPKXisN5owcavY6wGZs2OTAxtsDsU+Owcri4KM3pe+1Dm0WFUvfe93gozcPVZXY4YKtdofkmOxtU9ej9fnlZ8zz3+Qo+cUhOfijsUwOpgKFZx9E82hihecdGl5HQiiGQ3K0vnHBZG+rug398sx/FR4s+tEhOZqfOzE2yTFQkqtooZsVnnvIQFm+PRsQquOUHK+fT2FralJNPpqePPI/yJH/tUNy1N+7W8yRw7LqX3qIxQXNoK3xeQ2vPmo7BrMHh+To/PRWnnR8fJN6MjXcW33Fqv/lAHVCjsA168QUOViG0HWrNtDcii48crK/13YAPBwOyRH6+el/9BCFP6iHI4F0Vbgv7T2H5MDbETvkmB4fyz5iGyNooSzn6O2nJ8ZdJEdf6jv/8KlfsMREd4N63v3tQ39/3pvwSjySo/L2iwyihbKquy93cVgZyP1SPa+7cwdrakJt9Dc8vD8Pu394LO6GFfxOxtFCWSj1V7cmpEPFP6vnWPv7V6tXU/2dVZcuj+sz7iaklbddaC45qu66zK2lLD6u/3pLX6LeDpf/0fbGhXG3lMXvaS45sg7Z3IETLExyVJ636ERnrSowWpsTd04wQ2mhVrYO3OdhkgOrvW1ra3JclTHLfS7kmNfJxlvY5MDa3r5UlYm1jTchB1v2kZAD68/6WBWLqS17IQfBPjgeIiFHxbkLK6FsLAX7CDlUmGD45MBqbt7Mmhg1P0xQyBFWgDHxwzMHGDc+dkjcBRgnbLKQueRI3HwxkSZ4iLwTdzOXHPmn7i2iJg8RfORGc8lBNKvIIT0E4TMqhsMsS9hoAQIZoy6khhmxLKSue/4+E8lR//LD4cmpGV/cHE0sK5ZTMFjT03kn72kWM9BG0CqSvMUPTA0PFl98tCnMKLns+KmRITfSPt0RbtqnO+Is7ZNl1T17D7IRrR0b68+DVM5dsSQO75lVssooRmF8oHGaMK4n/ffsw7fWkxnZR27bk5ngWarJAFvthGIQqkMoF90DxgW3POQVBWxJNUkX0v7dRxn7rq8PLTL237Djx0/5MElSqwVQcLR9+Q7zPnryKA4iaGjbvnrPmpqyNYSkmhxrb2545REE8r6GeB22VcNrj411tNgCI1T2o0119CWkWkDo7Il++sBNSM3Q9tW7o831tkAHcuSesOtwXfWsRVA9XV2/f8M/d+UdFxeceQC5N/7YcH7nVKAwVUjUQXV+pOv3byd6u+1ZYjhYkXfS7kIOb7fsmXtGnhWDacFoSwP+7P6CDBQDXb9+1f71+y0fvtzy0StccNuT9huvKECxyOcQsDN9z7XVlr1XEHJg+aftowRk+gOJXt5Je/DZQg6fgn1yjtlxrK3J1h50PNlHbccHCzl8jQRL2WGFUMovtsboTvoxebvl+FQhR3TCBNmEC6X8rCEtmH6qjxRy+IEZfNWdP37Ghq0dVfABHd9/rDz6Qg6NAowRS7Z+9qY1OWH7Dv5oyyevZey3gfoYIYeO0ecpO65YesWJze89T+42bzc4LIu1btO7z5VcfgITIPUBQg4zpAnJ2y5LYp2G1x8ngxvuClc2cfoLs0jmVHTBEcnbLuOBNCEaEN1K4haL4yApu+bU6nuvJBaEroXtXJxdg+UFo62NUyP/iKzkgluCVXGOUYBiFK665woqUh15gde6FR0hoqaETRf2VBcj5PAWtJ+50gS6LiGHh8g5didzySEbb96i6s5LzSVH9X1XCTk8BOdamEsO3HRCDm9dkOT0NJEZHN2CX0TI4YMicl7jsoEpLaSQw1vUPH6rWeSoffpOCRP0DyjWzWHGXRJD6jca33xKf8UbDlY7KpDo885fvkzdZVU9mZG662pENdtAyBFFUTUnummVtINo9eDDNyjxtJAjyhiqLuXIpqiPMnxA4TkHDwXKbN0goiYkLdX3XZ20zdL+04LtezqwkfroyZeFHE4iiqdHR4gHQzebsPGCXnOCXdyCM/dv/fyt6bFR5x8v5PAEzP852s1hYVhC7DH/0O5KZxk7sg7bMvDQ9TQ2nHCerQqyCjk8xN+jIvpyU2etOQt19mYlNn/wErtfTAvQnznU41OMs2qpwoCFJI4fQb5mzxLU4rMx22tIsE/abquPd7ZFHhKM5BW9NY7tvvx0+hg2xjAuuOUhrygQebgy6vvUnVeRYB//IsHS9lxruKbS1h5DwfK03deQSDA/8F8LBDW+6Ine7GS1dBJyeI7/3e0kpzETT1szEKXMjJXPi3IMqQQYM2FkoqCVFpJRTwKM/cbMEZrsZUQzoMaySOyRe/wuEn2uqTSBJE8sONVaxh+Md7Q2v/8iSQRFmmCGboVzOnF/KT+Vm1AetuSfAvdfk3nwZqJb0YIcYQjOkLqQB4xcXq4E6g1WFKGFJKVkGPInVi62thDdSvL2y2cfsQ2p38quPZ1+hZMM2IXpTvi+vyibpID0MfQHXHDb/cd3rZ++QQGKUZgqJFaguuhW9CUHqSPNlSZU3nGJkMNDkO/AXHIwUxZyeAg2vVCcmsiMpK2WnOgNCTm8BWH+JpKDLXsJ9vEcU0MDKkWfKUayIZ9CSiVMkOxbZpGDUDEJE/QPbGsZc2LoY7fYhsH8Y7xKrzpZf2aUXXeG7T8k+pz05wT36swM1BIEkgk5onZAE6GdejKDgY/Ps6ML0a0Q+Jm05RJauTTQadoKQo6oy5k0OSaSMxJGGoK2JhByqKQ/bV+8nbbHmtGiBXIHTv9T0UZCDh1Pvml84wkSF/tJC5xy5IMw43wgOQBwcqCPAA50aV7TgrEMLuK0tQWmkEOB3PhkQ3A9hweqquCjNyHttwXmkkNNRxAksvVFNvuUnVYKc39k55XJsV/3wv2oURxMLIQcZoK89xwnHnz05vKbzim+6ChO5yMOFMUikX/EFHLBLQ95RQGc3yyVjThP7i/t0oEMAAAAwCB/63t8xZAcyIEcyIEcIAdyIAdyIAdyIAdyIMcLciAHciAHciAHciAHciAHyIEcyIEcyIEcyIEcyIEcIAdyIAdyIAdyIAdyIAdyQHtHp5xFOjNVAAAAAElFTkSuQmCC';break;case'logo-de272eb4bdca9c6fffd38c073270fb1a__9d7e398f.svg':$f='(]^+JbP.FqjXYdorFxH%oTmn1#,Na[(-^<}T{`+Ahl-RItQoM;{4bK}l["$V3F6U&V6Ey@S8#w=t>3kaN[hLow+fWEUH+K<LoXqyEy6JupFy-JyK4S8q(7tl96;KLl/F|,Cz)p?p(B)[axu/4u77-)nvU
R?vPex0x2ynqlE!VMsqy.7^Mtiv[hKzB^oh,VovqjM1XCS0v]mXW-smT}3TK7IVEL2YtHsc^Dne,}uyaN:]l/HJnieEbYSTw;KD$c_8p_B2y&,]pd?W+OvtUWi,FjFuW3Gsr=[=,k5ZhU;]w50sP*<)SM
tcO5=+WoZrY8Iq)IW=_gPo=RG*5hngIJV?j"daOWXS`x~L$e])]A/t{9it,:r%.89Z!;1rZhBw]6K6fQlvHN$Hw,QuiFcFpKmc{y#sO=!8QV,<+O&P/25]6vLiFL^ILo%v=7LZHx2=IpuT_qcxR7puVAY]-[aZk-!Hsk3@pU2?.=/khk7TY+8^U^mMe^&3|d[5+h9;Y
kr~/LPx3%=u>(#a3Hf@EX)<u
hpxoYBBVp`W(PvmMW
B#sK.gGL@Vd{:",35}yAFD8*Arm#eht>.nM#/VX$c0nfYn>@aFR7y~^p#M;>Hr]/"5-YOhURoN?g"zr)rf03v&=U+I-CNf2fyI`@2rCNwy$T>{3b.C"<mw^pUpNV.:1gW1HboUDhY6rSWb#t&3^ZZCWe([&88L?Tb:rJC{:,[0cUZh4Z?E>_4(eVbK+W4cj3K
6JZ,1OCPNi-r:-0+h9c@$6(OPFO,>/K_<D>?aD4|c[qNng
#]abQba^dg.vgT
jO4.nVHH3Y??RBOkYeEql7Z%i$fv:!`8=ol
<6HDyKdV^.GOQE<w848Z0)$;-[WOZ($QN.)/E#@[UhS3g@bs8$w@iRav#q,^!">riV0ad4mzAx-tm;I$7+G<hFV$knOjWB`9D:,!6.B`@~D~lLM@<M0y2w8SF<2z*Q?8suZ!O(%O"i>PX9(r?[=%/{TBK"Y5o,?wUbppvc%SDB9:2sH.!E?uV/?
m,@iTyWH"kU~.Qf,)]TyKwNyoX6LeQ(^HfM@6j
4o+qU-cQZ:uU]TVg=la`BE{x<YgRQys@]DNHkxs-[I/xZDH(tx~I,OKPNZ/@fA]-^.jOn630BkZbx.P^-,m);cooD1IAp.,``B4+,etGxX"U8fa;-m84^sKe*v>@/HAeYMWEKTQ)eqhf~:)bj!p<2bBA{<+-LC46:QPR:9CjzQATX#[YXUysw]
N.c{F{GlQ+bj=,TT-!C{[nb4XXv@IXBg4/"YW.M7"&I]1:iT"%EKDl:j![3j6cJm@H6qxXW2/Z3Cbs2d^_Mps>DM!ccnZ<i*Bk_oLtHcB*IHFOrym<(YWVBvJs)l@)0Z
=r:E0<}*va7n8dz1"9z&IAIi9Vql_/_GmWkv_:7+J@p:0<f]@QLtEi=rp`*wKM:5vfI1|nK.ne&[~?Dw9$GKV(o;/%`Hmip$>""Ue?0@$iQ%0E@-8u^"L:b>FLzv@>2F,<8Oa+M=?1oWnKWe[PvjmLPP1h}>?=m6-g]sv1UozX%`5v(*-1kTxb9=scVhWiuXQq$+!BPCVI)xDF&Cnc4ACZZ;UYX0(]s_GY!vk8WEz/4F"DLf=_6%>e[r;9[xM
*??SKd):Aiccqb{<(e68*v9Xya1
}IiKS_We9OJP11tEgIuGCfq=227bEC06#b8:]191/`0PF4dN3NCRTej;PMj)t1HQ
Jk-U9uH!E]5fjhHQ[+SE@:i^g{tA^Al~K<U3Js9&fM#B=^50#vEFbxFZ5L?Y3#pI^GKK[GYdMVSZS-kM<^><@^4f#(*V&b>jq3*^KjD3*Rj:sZUT"F5[bbKNE?X;A{TeBBBDDh+O^.lXKwEfA")l6+[^TWA?4gsuw|<
F:E?URQb2aF,p$7S90=|txQTehv2K|GQ]/#8t!]{/N<29Gp"TPCb9HnMc}q@$*7z?v`WcA(@>t%Q%t2zFCg.^la~3eLCq_$QqZ>erybXCLsr`Q)1Xvng-<,eXT8Gismd[Kh5k)PClZRUu<<uVag@F,=B#6wrEv$LS(Zs^CXo2:d/o%A%n/ZC1%4vJi9[DJ7|ViE>Q+(A:M5wRMExVg">y+d/OirXu6Z@>[`*:xk1k:,a64QavY*$xgkb=eYrj?%BUFsiBT>VTy`OsXZ8T]!(4(9)TVb`f![p</Q,?n.6x;Vcy,ezD|@X0Xca@ad["tI%:wj?P}^e*sm]oP?U`&OhkEg+TWAAc5FL5H.DImYqS
4fIvQ%7XhX2^!kthV*<ddA1ed`@9m6@mZ7)ocp_a%uQl@q0U??@Nf?_0.+DqepA/LGctQ1X(#=m3EmLVkn?I+7r~foFQN=BUF~8$nF"4
{9LE>C+$c%w8vSvNB?}93S#K4kkm/+t;`RE%e
;(Yq`=YE$3,5|@/mXG|%z7YbGWWmFH+IC;f:U$J6vpyr)hV7nU62e3PYwL~yj.31;lgq)EK$.>bGaY5`n6mlwn3@/u`vQ:9lc4J6.&&ga%^j!0joCA$LU&#g7trww.(CN=l2:G1CfPS:KH=d>Hfpi7[$X`,7wJZoJvRF?YKmSVzNW0`Q0FdOqAIS
n9lrNOU01c?:p/5y16+Zkgo}`M)D6xm>7RiQ_b%p%ocllip!0).myrT"w[53iGRZBt8z<.d6p9("_WYy1v;v.xBx%3c3&hfawJgMtuxeflyK1.-:wQo"f_z&)W';break;case'jush-b3a93b18444da26820ff61746521dede__c1fc09bb.css':$f='%X.mPb3V?!K0u25Dm[994[Zg@N#Q)YOC=2R_hE~4)=>cbdia55M!*B>tthOR~qe;I4(JE7nkwCcvDp&`dDJK
f-B>jO*,sE/El7[<CeY-WtHaF_.kmd7o@TK)f,bmf|yor7&?P^<qeg8="Q1IULQ7R}m#!4#2";]EMU!F1OpTIT56:qJms/Dv-ge06DN|n`FW&*;(q:eY(cbjkU?x]gUap[%36X.0y?`E_>T!nUPeLxWQNBQNxgW
`cW13j?"h~G+;>ckmN2+
!n%Sl8/pL[B8
M3BQ=4I8t<Wims^cdx*+69AM:3(4#D7NrL5JU;F-nR)NxQWs7{b*3#%wvQ^i5"h{.StgdiF@(/BD"LK.,r2{$e&b4#$3x
`jO"8tQQ1CKB,TYk$:TIGnJ]L
WcYUpj,c5;pfH7%lebNsHuwv44..jP:
oZb0YJ@}qOxs&)5?s0oVA*
swu
jafJPvJQCQ5?:;JN^klF$8EV7&
J?y"=oJ8]]rh5ZK31++0s/h[@)W/]t4{Do0LZsyY]Ui=F:=Lj0OiN0Q{mj^W2%j7&7^of",u?pagL&"iw=-;EKZzP?gdKNd/SR.wcU#s5@q~Y}(R:VF$Q8dZ,L)ey.Hq2O=)g/K}w)n3hY`Mh>::q<VI?VE1nU^-^D/qa9JwIPcYCppQ/X@!6ZZjyFhzsSz&E^ke1)2F[wC7uibhCB4fEU_/mdWy=C$?"]4Jab*)fpfaP{Xx=d3$rPj<p5Yo$$mqn$a[gdSRbtw^Cd:8IL9Ep@:NwGhGJP+=>bUa%5!)#P![lKsPx,lj]_TA`s)}ui[?@K5<"=yhHX9ac1.(pCt<=mY#,*@Ho8y`eb"03`0_6Wx1`zEp[UTJnLo&k`Z*%U;u_AP@.ah.ussF)SKcuGHm@)HJg~D|K@x[(QcKT7b133MD8{9lxGHdF&gk".u/dfBj;$`frs[]W0RtuZ<]q+TW7f)<cBALJpw,4G-PO&to>Pl>;:<vNY$aUe;BxF$7V3N~j=h.gDd*D(06YWAm/nioW)g>F(ql[`!L/h`[Z(V`2J';break;case'jush-dark-f8dac59c6ad1018686e52a0e0357e421__2ec7793c.css':$f=',Gjwm6?!R"-YJmoGR`r@~cEv;#i.*-_KUyr[0$CF,>/n=#+liP*01.(73:+G.C]Ek+^-h&|hnGDq1:ccpxU98SxFh5MU%c+]DCcezAcUOWmDiL$
)yZA,ICx<`.i#E%U;lo*kf6u&LQx+!%1t]iP#G9;zGT,4U2"ha>hB#am`y1YU6$z!l#C%';break;case'jush-31ccd1ce96536a294822e952872683d1__d5406f57.js':$f='%hk^;xqDE.!v]yOYu=(i
Y)vs/y6g*Flg$jPml~(:A{"*hUQ?DHkp(ew8qFkv9z/bpcXBv}iImX?wn[G_
ekWbA6Z5:wmClJDXdv%Xk,VM[XSj&7tHp4O6
e
IPhUwoG~hnaCQ
#64+yF*h]Px#$6t%S+tPr1?zqmt&/1aIf^tZZXZ=xn$</gG`Z<`bt&UpdKt?BlePMu+lt<5jn>"1oUfa#3RP5:b5YX5vSXT^pFb}FNnEHJE?Yx!OB*yj4}L?
s%jO*MZVn;dqZUQ_gRv^N3Wbk6ZGx,V,CRmu*a}K,dKR0&J:bw.fhMePp,p7eLxPfit/meE*BE}5[B3iGXsk)yjv?I[qcT8%2Sv8:OfR96BHB"|Y8yaU7NP[2xUy57!,FFk(*u^]J;~2U^@mbrS
nRTcuEFrBQWG"4!p5sxm[cWdcNdXfB?+SafBL,_1>OE(]47oF5^6/N>13m
jYqP4J6|f/=j2h%Kt9iFB$4mNxZ!JVt;i8g(q$00gUsw?X=Bj<!fUM0KfU=E:Eh(0S/Rqdnp5]JWD$5xhw9$1ZhgH-m~KwLg[;,au(CHJ8RIkD=PireJw([0CMM.IthuL]X_yn:gG9nSX;$R[xOmC1D(HDOL8ql</<dqx+3Cnl2#t:1Uz)Aj({_V&PKrP2!w07?RCZ>wNY"YJZs2e<,+gO6yZ4aJkwEx[g,<*]JX%/7+D0+VpS)v`^eR(PfXJlvchIdqp9DEq_AU[90HWR=,ok_l(tSP-rn463#X=.ue$`bBI"X{z#qGx/fURd5z4u&Ua%z$"wp-UJGXrEvD%{0&HX/auoy$Y7tyG[x9UEYp=N,!%_
[[1ss?6yC8[Xoq4reQ%9a
D0tpLLOH8<h6&`rWl^+tPvz]VW$;_i|EiB-=b$,EnkZg>]2SK[jKsIGu$?p/aW+^/[?pSV]U|qH1sfkqPL]E-mZ#Pm`>F/-)*?+MhSXu9``mqq=r/M(.Cfbw60qPAv3L]N%Nr2l+Px^2N902&(LZ1[tbP#3!sS&h0UU;7^Q7i?lxQ&i=DGKE@t:nX[j$xGP^>ju=qsO]"sYUc_%8ow*F+$oZyB{.8+lcj%
_v@l5H**`KE|S(3j6n.p#69437DVc@m1O@ulMmmTA;X4]-k97z[`OD=NN[-|YJw5k$$aF}^Mu7-S,rUhYfd~!RaiaJ^7:Mtx<(]!J+Wix*S3M!Vi9zRV*[*bq=
|v`AJjv({D%6~#)#mn.X8={fxkZ2vq~t-96*)u9^g<uP&4DUd7Gp)6KjU0nSO#/bll3b9;QY^l7
)ag8n0kxJX1!xl@VG&C[J4F=|(RR]]fG9=o)c,$?`>6PjJWHgcBugfD7$3I$>%~9iaI;%I@^$>~Z}j4=$D"4su|[7"5VKd@KHieQzTD]TmBg;1*$b
5RQc
]6cxY?76VH2G),&N*xfDFQr>yqH=IsjhQFLrZZ)@F!w1YPtZu*q7Z"9Ri[tXhAw*e.[(SX,Ne`U@LaYs1s396|4V%+m+)FHg1zf%HR#3(bym__D%*A].4.5g7#gg)?v#=49p"<^VZh
/Q$
*^6D$4(x;MI;:]5UA)r:yBvG0PLb@()y{L("L/i-%?>iJfDM?1rY2UJrDTXbF]]Fod=hK:pD7dFB[8*l@)@)U3&P-=Y*Bj2e_AZpca3($VG/[XaeEevv+wih6u.=6w(v&Q4
#))X-9u0L09oMZZyLC;yX-g2J9i4.NER*)~ngc}aC]nW:"_9$bh"(2cyN!-IR/jPxs99ID:/NvKDug3RHu$`!^gS8-COR.cVg.z
:@qy<j=Z+jCN
6pKqBCS7IxX(0]?BU[A}`HkKHf
1A;_D(TGx4&?#OW)H_zTj`rR[ZgLpSQ.eC=J!5fMX^I%|"H+6y)F"g<v/esU*yf)~ux+;HL$DUQFEMo+$gY
oogH7,dp>v&byN9*u(N7WS"#:F[Q9M[
bZw%JrlNCj(%=Y2Fn[z*Zyk$PAua+*DE+iet(()+rs2j6y=EvIlnYqJl1(dIKdCdjV*ppI=jB?o7$i<"b(u1r5^gZ/;GH)$>RfV"inW^k:KQ/?=azOP"+C`@^6%jLk]CVLocuIpSQ7yRV&*t7%57A"wC*_w-=Q<-l9>1^CKIMP5TZ8D<E>`0uHqqk%HUc;d
#qyIOxl#IWw2qo{]4ECHQA6,D)6%3c7!T<>s@Ca=$XDwxO)O}yBS_p~>-aaG]9nTfQ"xiFYtx`WT3blywFj6QH8!@Rrt5aK!iBI@L*&h8yqv6?;,TlglSGytv;"SkPim1p[r@i:AeHp]C%q$wY.P$J,Dxf@v5OT33V,Ycjc^JS,eL!%tyx,!ePwKeP"`9+8k,09+m!x44)jHqg1hCsDp
GF=9_n
<8Mn`XKWS&LW6ag61510ssa3c3=U&DC4-[$>UHoD_C<]mu:L7c]7`XV46+~?4gcQ-g1M0"GRNSy1]rp.iusB"$^7Z-Wc"f;/+DJ
A<*o)(y_E;R$Yd<D,1#SY-h*rdnn}2IbvTWN(!~>V3%[cuMO,+#7L2f&ux}(ps3qynCJRu<+O8_wG5|*wUCg`HIv(HPB/R*xoc[fe52&tsQx#(pC[
{Sn:RbPC5]-:bD@@;SDg"(4h|?`eHA3"D,h($Ord$Rm$|%j]Yd&5X&qQRm/?+:y6?@`"p`qUWnLWVrl_~e<Sl;
Oc,aD{#Ohn>^ArikR0+D?,t[9@&8u"AnC!3U_nVwRH:$WF-DkUPHpm]^fS^U^,%(l38525k)Fo$kcz<DcSa2q$PwAO(sV~j~,J<cD|Kn;>-veb
%P7N0EoYiwL@,Nzc"x,82`c
z$z@3.l>gCN^KFryB;2xI0W
*6M(T$2[zO%dkP#:69RN+mQ0NiW/<k%_krlSDoLp=c/7Zj#:
SM(p@AaY+E`"*F<2J?NRW9PQHF?%t.
xI(Eh9,^(9{HXUx:lYvTc_"qGQU&4LiXH:i7s]7.UW>oTu-)XdTVrtw<8te,ZRgO=%6sfpi,S1gXvH.SU6c`NY0L9&p;86`Nn<s3Xl-lSaS.PT^Q<[3fz:?ek"tdzsD>;#3wZZG=&k|Z]h``
(I^m`:`1qy@]N7a$C5@C/S/:8*KS7EyF>G((jL02k6!HQ-No)XgvO:25&E[:2$EWs,H,M}
"S%RCJB$NR`7tpqZ```3(G0T14EaDB^yH`&c}"~0]R9$j<o.q<sSlkj&@E$btZrjHo%+W?$"z^[Qy:P%p"PASo^
a[Q^NSfPn1mdc9]1-_SRhc4x#
n<AZb6^JC+-nq&2P"GLtMAqIT,W]ytP)E2P:k-IsdM*WpWHf;bM(Bik-PX=6iS8I5&DkSt|5cBT)&&sZC),^f9G6su&>_izKeGP9Uhzcw1!ckY,_j`6@G0kcW;|UiwH;_hYA_k,cJ(h85%a=z*DLe$&V~nSB]Y*@I&#Fjl[Ce@.Q>C#Gk5BP"g4.tA@Q>D%AB,&)KlW5$I:?-TyBKYVy@+5
6=#wrMwZl2L4}i:Kq]uHa+R;g.b]:4mft!aMXkJ%0_{K6nU13TP!CTxY/A-YBT_xsMAW[QG$KU~r}Z(>[2x*%r*w@I4h%,ZYO?Y.>r7o,l"m.[To$>u&J.H@Gf.P`?fd|&pD.d3>|,>S#-0)dW0=t?GFMN
&[LJdz$R_=.pa(Z`1PAbi|.9XM%/e?Vw-KQlU]uC6)j`#Wt]Sh/],re%-JO)a"@<J4ntME@Or%#}S):h#@(l(E*X2P;AdmQT
maoC^0]:TF;vYx_a$C]ZbBplLeQQOq=_`HD"m"DQ|^zOG2aQ7lE"g.
#oM1jd@/V~jZA7+F"_`
$l^j!ajTxsD3%bU;FiR/4G
2mXc<xfM
*KVFV@x[ShPque_ME7t&.jpPuH.(3@:E`o#r
0nJUZefA+CxLF<s0DKc0t/E;Pv]z(j^iN(s[64[G0a(`Y&eCs2{M1d.-`H{4K5iMf2=/G4-^].
^7?cBN>:P1VMgUZ!U+nAJob,nl7Ok*y@j=Ln@CD#&q(TI}AoN*]=xeDMl~]gl-v}:^I6!*A%q5?m;#O8k"<Zn=J=
bBmtbQXE(OmWvw9%cMo/rX;YZ,Qr{99t0N"5Uc=F@EfU>yE1c@^(dNpIbs$nyGHxNbmt!UgX.a)MNX89Xp-EM^7@nUbLxM!,(?70{dAN9eXBoM/Q|U"a+@+S6j{]a@;XnO2?Ck7;xFlGkH|n{&D8acQ,6_i9)AKBX
!YL9Z:$@v>3ZxTJ?c;UQDx/Gzj5l!;Yq<)47ns%xx"5S>KL/8hwW7I8jc#]jRn%*^kpSL<<pW-1,b9ZBU=>5N(iX:A!3(D5BT%0:E=t]6am,(y~]=f0F0,zW&c{)F1-PCu)dDR_c`_Y^ka1]l39PCWqVF+hD2Xw>>mTL6`p+DftM`T&[/?4#a5ST_JvQHm<;3SsW#*PJAs
]Zg=hTG:M9Q;Z^6X8_Lm9dE.0SR0bR>Ef:[Y9xq3
P(*TK!./(g9myKvc^)Cy+RLBhThg0;DGMPX/_Q<.sp=<#.;s+RF1MK[m[q?fpB&sd"1p@#?A9qW%dq)9sM7TC/bveum,~<
9mI_jfoL*0@NALgwBzIJW961e=?GALSRNbNV5%jXB!pE*Ltf3]_yg((_#%FLZ9Ak6=N%f|VWmwEiR:dH>7)MR*_`p`v]J
x+Jfj9)m8yMio`8nuf=+
jVI9lM*Z~?<"rf`aI[)Gtjn&8&O5f3+S^I,F#U4
Zl<cC8#hg>=5m[c.fDxvy]N?P@Is<;CBmz!8<1{Kqfd!L*TR?Bu&wI``Wp&]d*3eXTJ]@s4nKp$Fvn%`V3hql#@nRZq+kWGQ3`S-Iq?W=rc<O
j@4T"7OqJ(ea+`|ps"LwV=SUSAfP
TJYq^&.5P4<O@nGJIDE+J9Ff1$[|e0UXt6+W%m$.g$v"`ga|"qkn*%MCe0F%5)y4iNRG>Ae.r!?y8FhfQ:^:C:Z?Dr3^=[Ed&(x<34T+ix:,po2;`zM.k[Fa.i+1yq`;Me^_/rsBDiBqe&)4S>9&.IrAR!6-2gEgNp?ZjWJXVK_*wjY2^WkNO>*@3`pM/(J~vhRsdzuw-@=5K=gBMfj~d;O2[.m=haakm_]|k;$_>v"tm9$Ulcy:RrA6fUku7Lb`74>^`SU-]%AMnQE,)3&3y3Vz,!"r/gWa+,Y7xsY/uqZ=2VcPhebMWR4iyX0!Gjem$dBh$1,%-2UGxy?ei)D^)el&)r^i#8%O<v0Yr!SEtmSM-;0*D1=?,EJIUIj;[YbD+&YS3yTQbRjN6`OP13O=%DwzFk;Wc
l0_fTzX
LrI8Bfu!g+hyv?@$Z|[%8YCJ%RuL.-+s7Fc1M[e&vA4Sqw-suq+y%u`9u?4HH2%::C1_NXjr<Z;qe=yCMW*MX)4Y49qj
xp/8ln|/8S7i9PkwM>+S[R!PlW-6oiXd)iwOJo}eM($PKTdFBsp=dlJ[<_Y!N<TS8xarFWypGj1k;]&urklsxC$?Co#*.?V>7eox:=|N@Dj6XF%n4v:b^C)^.smlj+!#+R>w]>&rOv!@d
+-DW*#X]
"y"Qq/-VeQU|l)yN?JZU8Ym%QMvrA,O!<4g|YOBHObK:E&+VvLLengHq@~a:kOM~#oaIR
k|MwBH
zv9Cgp+W{^?i[hx*/BAx>GWM$kbLSlg>L!xM3/fkLl)Z5R
mhW;wLaGc[Kf(L;UhWRhkZm6a30jxS?nkZ1y70h1]b0oc&k+n5@(uOAF#"HCkBye3KiZY%<q@6x@5P
E3S#Ff2^"eZt=A*Zthq2R
s2;(D2@jC([LF^%^Q9>G$_3aIuRgXyWKnE*j=07,Ne&^Ii,Mhb)a9lVINz%HlfNs]]=^C<#-O,Oro_Tu!`y:g<`4`
r?2$i7n`yeG*RTvLobYXLjE8bpC/wooR"x>Sv3lx]v=M/30<FIL-fU>+t?aX-_wmP1,L>,/T)MmtF6$`#GPk$D1aZq@OAj>O1wOT;l])kNmbBD|Yr2Q<Y^i6ZItKg^fa:w3#YpvyU^cAQ<9np8wBa>@"Pwlx6ksU7w;V/5RO92:C6@+wimfX7xBo{wq[*m#vaH5l-0>[sC{L_y7%;-E@zPk/`@7bPo49o0X(Q&R6J_3*uC-Awl:L,Oz%?BY(x]215d6!_&ewZq~5n@wF{Fly6[cwBY,l6gs8`7,mdJ]=V5_#|P_.A;@T&4kQT1Dx1L)7Y<nKaeJn<jce;YG]y^EkDG{WOkVy/ebFCV_AE%<O0mEE6)[<2R2pu$vHZLeko_#BJy3yxA:/z?2Url!diItT&0vNaUS,)q](24LVD5,2:_%R#(c/x1lP-oU(90w1IDEQ(H1/2T/M
J^L6+)2vamCY-0z#$CY1h)Bu6OB,CQk8jVr&_$ye=l3;Je00L2Xf*D%D4._+6QF1n|$iC6u?
8f$]0wlm:ab_KUD;yK"8?c2.aR$k*Ki6~4Xoe*^AW
dDY]+aGOz(9chnHQKPaq|4%?H(8-_:S<(
oF9>0(1c[Bl]-<wXEt9>|8Z<(".0!nv.!]B,UtLZ5r2h_f"?#>00V,*SSLEI}0N/R?;/7T}/l:"=mr}[sk#Y@wjD"rblWp>Saz#8fe<)S?"2X9P0.r
[5@+0VA/:K8VX`WUkA$L&_mA^V1~XenlpxyW$:40RAaiOBQ9x(,I2/8iC-?O9usb6Ct}U@x32
a}yN:&>ey&Bmq,yo>{UJz$-w(B!lXv=LWg=(8*5-5[&HmNNhAD1Nyw]W[~u*^Jv9_j+jRy,{]K?FF]"Nhg1V`Sy@6au7B":?:|k4T$;oMWwIN*(I"gd6u28R&/1_uK!Bp`mxPGFE5z-_`w-/A8-!"h<zUxlYZO?_nd&4K=0ey*nHJyhO*qU%a&]T3*a58aB>>)SV[kh;V-pFS(v^BG<?:LWJCOU?.+=M^6?~"rY(N(?V=Wx#kirdgFv3RFQM23ishPVx:w+K0JA;.i
T*XHJeuU@-oS`u_Q$0T;Hp:b-c!!PiJ61+FTU,jW0H~
aB-kZiRtwf
k;]hAyG4]b0HVr(4/a4o0MKT0KswN^9QiP<Vx$2pG&$V>Mt4,"17YgQ>)TxXf
!H23f0_e.Kow"Too>dM"W^HpMZrjr}-h9>A(Y$9Ul@3sk
K*W^MS$_&$(gBG,E6J+1&o]}PCuz?kuQUsZc4lb[tOP+KR7khJ+A"P-Z*Fa7GRiZxIq.vEIA0u>qD3R8,6hbY)Oq<_tyUc;C/=c5RS(^)VtGvPbox/eo:sdcY)WQP<#NlfYZEjA|O-aGXv6W)S9;Z`]e!_A6%oG.;yqw`ovOpe.`mY<BYsDt2%Z8:9b*D<0A4-WGQFz(tW+[p,D8SiX?)fHWBOZm2=nCw7vB63)lVgz"pSi{.o(>Q@<fwY/O3;lz,!qpET4>K&;WajT,aj%#Sz,/`dV4LF,NWt/Ob`xmRdgnMq^`VG`E?Lt{Zki];A90slA9O6n:=KkMb=nfQSErT|VNKiG
-poa-rcY!]N-QwO?6><5`4rqL<(H^w[fZ:8ji-
Th8)?B&xnN+FjZiQI.m8>G*w68gAFXy.UJ~V#KIFd$gEYp1>;dL$p20+|dqbba*WRfKCg-14|LwCTE%]U&(-"ZITc?m_;YIci+f%WBEoD&EbZ;EG_al"w
okHjfkB^xBG;]H<$vBS)jaeTh&|4[dkB40)kK%en8>1sqL
XxXg;:l>k:JYAM%.FHmnwUMWT`Yn=}$v^IwZ!P^?gKUcnRl<g?XYUO:!VnHdX%x"kDovf|95/xuFJ4kgGt1+HcX^<#>?*Tolbf0rjb<566PtS5+
o#<Bq{QH,K%y#RXK7{p`V;>(iNyY*iU3Wj:BcR50DE#rrZYp=;17_<e634<A]K%:kh)"P*2da:?hL>64$]xcs_vG0yA$K;T$K#:n(?E7ikokIp&WdqqmX8KVKsdpP|NhamYz<GvwLiOspvMQ?AII4>Wt(5_{Ne;Y+A/DtZC"e9k]5Y(|!3m.cV27r[Hqx;*kU>YK=e)1o>?CPU1)lPIgPw:#Kt9)i68XZ5>Vy`7{PB74(s0{LHc>AD4^+gI-)a6BXZ)FG[ex?$@{J`:o$T>*_&aRd~UWz$-ac*PQbG.;2L-VborKx$I|LPd:C<r9f)e8sc!;l:a"a_$XD+tfrYH+,uQ!O+[4OO#?;Z4E+e;i]H4["@:5ZR8LAuc(Ahj3hX71QOr0p$-7;TpgaY5/;CYlx.&JdHmHf^go,<
UAT84O/PD-/e7cAw"u+=7/f"266fl:)H}Ry/Te>w#vb=OfR,
u:Z(7cwOu^tR`SJ:7ay+!f9L2Nt`^pN{5vE&>W*ATi#ZOuMx83
L9|u@:ppP5/P;eUwoTY`r*hD]DojRn?Xi`2ydN1eDXzi
$g2Oph3Lx
<%v
U_KHYsr.EK9wThIuf|
sW.R=>!MhhBWe"SPkS^A?JQGt7hQ"]"i=EE3S5Dxd_pJ^
cDwq0gho#8z2kacu&7o-=MT&0h)b4ZhY/Jn=GwnFkZ<G}$l^E.Y`i34^
gnOr*d4z"{p4!3-O4(v-lN6wAv6U8;1Zbw.6-$)|8^<%=hdvc,#YCl*~-p&Z2)^@H"NjDr$=%6C#9+uYjR9$v;n"Il!]=jcfDaKk#D
:6ZU,t}>JJ)9g)lx.*(/IHWF}blq+m:
q>EHn0)26wPx$t,yqZ7[I:DAgp=h
cKc;%%B6RHCIj{F#uTt}8-$>dA@2RZG><4R,UTtAgcR3g^AWP%0FHu#+%k9fAMMXhqWuO1,Ki<I6T]tYQoZSoaMAB4veR1;2LxHBY8UMbMU@aqvZQ@N3ZiYV6h=:t2$Z0[LJvISGw+>gCI4g:<Om
uLXhV*hvs"-96E/>h(/f-)?[a<(?IAM7qHEe`xfx2k6C]2$dc#>vOn|p<0nUdH|*|LTk$vW6RcT.#,M#F:Dm0NP%kE<?s%
jARzY81biwp6HR24tHLEjY0`@Eo*h!/e^?DFi5?igZ4A.1-v^a&xc=2Ynib?oIA3GQt.>(Q"LRPhK;nuW&S:0_5UA*e9?RYJXxd@eF>`_et,IXeAk7FJauVFS:VrVlj%,TQI,nd>"lrvp~1B<YET4e^=to*n=Ep5VPq!8Ke1R{#!*1J{%TK&9o`CJGPm(![cKGAsv0b/`1W-w5
b^~Ng?<v)6HLul,$lcPR~fS]?ZB`qI@;y`&F|?S/]uejD0IP7AX[uwB#4v&G_L@opyymaNi7&G-abpJM&2fQi]y=~$G=E5%f3GIt!>"<(VkZ%Fo2(rNo]QRbM;"i<IQ8R.bK5(3`_wfAarddu*EH!$mV`:6d+2l),7}VnR7P^aJ^nY>="O4)lYYvNACF%>lNF&fhB-y]KS1Kb!$wn]}g:J"jI(Q/6q-ehP?,`I,x7P:=+$w6>iYU5UK
,Np66&vy+^n%%Y=0v#Z#"_8+,$MBn^6m8MOZ:P"]~@#e]Sqj)0!1lSW*R:zG`<kKNl{Vr!-f<koUF#Y!lv/P,EeH1L/mz5|aViY$<:@NuSt6d^ec@LafOW][e0>OaY`Zp)8A4]"4UMt
5AKOB5H(>yHo@ypu;>}T-+~?ZOqB(d4Wm&<)^#<]Ikf.WCWONvhwj;4/j-r
bxIF#DYh#f;/r(ICHWmIE`o?.]]XrnNv
GPxG8I6Zw)Q`s`$K54`u>-NJK&
K>@`nRFV8"@3yXMK!
re,Oql}]!Fw
tq^3Z+_ZU7t[*-^jE9OsT+~N%s}/;!x;Hlkf,q`KC^lOTfRx,_YSL8bf#+PWSlsQPxV0[In
-w,7XkDshoM_2x#0+Ur2"y]L/WDF
&aQR8gMM
jZuV{3/D
>5OPy+.:_lgV1q!EGx>e9[gkk.P+`iA8_opaFAk^_f<[-eUF,-JW=-2TBp7kGKMCW(,|cxB%WP"w
RTgI*yTIQ)J>9oxJZpI[fQ1y<eQWk8ri;#8!3Kmp716U-vH@aLX^(qttDA,+,gO<c@!Teb19rc:`$M#73By[3;tlL&T,.69/DESM#n5!zJM0de+0rB.,w+`fTi2
YmRNwV|:?YmpV@Xb^;q?)D:1nOy@t/6$4#*ef0OPn2M(9N]M{_?Z]$5^okgs/B`J9m]*I&N">CICi_l!eMApi>y!kfS(D`r%Ht6u)IU;iJv6x@#QB+Ncwh+jjv^*g/kiX8d&3Z{W%gEH.;[
RJ^Hw*Vb9s{>k17>ST/
6*N!+kxjJfN?*F3%K"G*qMu-=1#>>?==D.gpis7Y)O_N.cTN75ZvR5z+O1Rj]vy_omo]uX$Wemu!}e5eJlD2!O(2+p1E}uwkTtt&.i/7<Lts>uOJJ4$_mRMiE:k$:Dt(xi&4HF5%Pz";e:q;3/YJ]Pa=X+bPZ*-m(^&!Mi0j]N)9@%FxImM""Sa%4T~XS6-c:kFBWAk$>2]H[*RY8FO*|E?#[Bg4a8fXU/n#oA%)Bu@FNPt0kY}-+N(^P0n+6=;2J1_X[F32gAGlHs//?i^.Y*+ieQ~oZ2Ad`0bVEF=wG^#6vr?r1&|<RU.4*uR!T56Ot@|aZu_208tiyn3+{I2=]?>FkJxBI(}GGtZXXME4oY(Cfe(OS@50-%yD]sf`KJkV0"*NDwd`eL>X"pI&Dv|lk0ys?yj4Va!P<r{5d?r&">g0>,_
FxedC9LjH-mr$2Sjf5VehM&,)Prm*p0#.8(9I`sWW^UMJ[J]]K*`Fn?#A>3H,ya,6Ht@CPiuh`es^59=Xccs;xL^Z<(w664rj=qC
ZIynrpz(q3]r=r^Pmpm
Jq]+%wYrSri-gD`C$sXz_p=)dPGn#1Wnt>y*%np7ui]rn|Mt:C@Wl$y}hU+"m3$lq{le:0.0XckHGww-kyGKssQ&MW2O?mLN]1fMu-;DSv7ge@AAg"3nI~+WY][#9E:{SVSRWgJ^_z@8)6%u(;
GB.jV_djg__tQ-|9a[%l.
FP%^JFK.bO1YDG~NVv[x@VeNB?5H,M&CvGi>_cuF[VDr[gBRV#`XV:I9|CzU[CS+jvPT/?;!&^r2T:&N+IJvn&hMK80kM
lZ259t[3SFAJ%QrCel:tN/$r$xcq!JKROY0J}6OS%B;#LvK&(N=az`J!W:nOskW3But:!^rumo~_blpW!)xXic8(j=En_QItOn5xx-92)70
ly^N>FSdgb7EA]&b4s/YMWs1!lorT!Np:RSyOVy+1CtoXZU>#S"a.%ppX4s!"b0e_)O@"956WLW%C,ofNU,f!8C)l4s.Pcy26ZeO>872,S15hj<CC]JZ8`F8O13Lr,{#Nv5Ae5
$]U
HhJ6i>)Zn@07Ei[G2]:oa>v`mC+k"MHQM:L*(-t~oMsr^Q?IXBRx7TVDO))%>V<,(E@hRgcjB1L[!@/|?fa<
JSPw.eCPI`k)mX<c$l!Gh
)2>GrY|<Yn2q?y9bG(OctY9gpSuW,pDKaIR]YvUMx2/0v,+Y2"EEHl#[dw2j%]nBOS4vKi#?tMg"trkkZ`jA,ZdskxGe~M><
V[Zq1;k0,L
MF(&Et*X;v$-~h&m!$]
[VnVE"/*.7]4z]z<e=PGOAmn)K&TG]oDl;;+>]1c9SenQ[FS(ccP3IUk,>+Etdfy^=BB67+GI%~y:0eKu!?i.A)B[j}7"cx=8Odx!Dwji,lAGC>;/NZ=uMdmEbe3[s_XagPu6dc&>Fs
GJ(GvDnG=6xcNYajvbNTYtGDQxA9EyJ
Yc3^n/=18l13ZJ;w)`Ue:L~8l%$Jy[@P,g+ueJQk]T*v-7"5LW
VnNcacTP[qVoTH
;&2!v<3/FJpDcO
?f3B0k#~px
MD*r>H
f&B:S5](m0h.L;n5YMd_y}[8xXNT^&xME:>mXT@OEBg<5Scq71O5f<XcL&z$V=@Qc*cy4lM%32WGM8SWntn3vm:`v2wt;FBnYfAz]1`W4)]ItMuy
YuG:hoLZ5XM4W:Hvu6?R"+zB<(Rbz@}DBWZx[*@)gJ5k/Q3wY_*pypnA.hkQzI2[}upV1%9tQtlmqbbrvfx[}13Qt<aLK@Lc_X#4%<oHJP*sYlEn+^
t$*a8%rZg3`OwiGw-K!@^ZwxFKQy4@%*a<]nc2e6-4Rn47SS]9cC=U5(@IE&pJ5|g-hu&OF}5p^3evjpGBvR?j+4AL&H,_uYF,aBFRd+N>QoQ}>D#?-<Fzv^hubxTexuL:MZ2SmUP.oSy4CEVrnP.AU2M2&lb9"v+*uw.{[R`wn1"r8xnh_8GkfWkR1mb;#?v23RJq:+);D^ew1Vl(!cgG
NTeq<]yHQK4<kwB_>m?h
I]Vsh,#9uA1HDk*#?n_j^}2$0GyYl=ovIdHX40Hr39;eNNKyL-tSIUv9:4J(*o>J;XuGElH"CLc}"p#=KQSU<6nT:cd?!VZDB(mpR+L*.lIh2]q~^bY8ItWG9<?{Yan*#oJwA7I?f%PD0Mp"dB%d-"#/A$9C1lF9#8S]bp!t]}`H97+IwoNry9C6Fo9,-Z>@*HM0jqBs;JcUBO*8"#Mi$)Y2w^r9v4[@U{?.&#[KkzaV8YNa$ev@h:^N`72k44@sj/,tl;@0et7p_*O/o*J%8T]T^,Dad_"`V9Kg+;YsUnSg
d)xApw;;7^*5Bv
LzON*q2IB:"s(]r?+~hP9+jR
s9>L>6Jlz1=3Qov,iwVe#$k2s8*h.`@g5T.stI_d
-2"K1go]L0V("|,O2{SS6[yDpI?jw}GGi]#URnJy_>c![9]^Yarb9zjE3BWl_rM{=x"5(1T;N.c1M{D*DoHy;:AgJl40kb68hVqfn|PVFpNU=VdcalWGdzwtr[I7kXWvhq$[,_Dd0Xmva*CZ=LkMr9`6<@
MGSg)hQdtI8AYu=4C[:xe<,A)_<Nm?BVzea+z+TIgqx^aZH*2]njr?*uavz>)W`r+S]ZA*0qNL,$X#(&NHPC,w##IZn8By1-@09k_I=dSfWkG^"L@RVUD@U(b^F=P(T
h9j:L5`Q+udyEw$n2:
5>tn%aDh/b#Obg&F7&Kk7:N{ta9;/I!t9$
?O24lf.dfP[_97i*,[u9L%:3&7wCw]A!RmTf8$[hue^Hq7i!4V~N/j5f6^zNK,!>L;rj}+p2Zp(2EB:ux">+pa{$s8_$e,(36=J.>uzJQkRtf
@6XrX/>,xKbb#K:iY8d#.TZWj-@b3M*Y@&BR}>(3y,a,8GM>?fU5li92fyC/Yo|<>&~J:/*rL%CI/v^V.3y!=cx
|LJ"AOz0lIg(c(TB#gByMV04{Tzae>
:_^z$hso%.N8,Gg1xBMf;4v.+@gO)3VqlrMf%:k92:V][j&JmQTl>x@Y7<GVOh^o&:w<5`aq$]vhW5.N%iX|F:ON?&9HW/8[oXJrQ?<r6ORBJ`^zAOZk<[>(+{3,I$QURIW#AsP.U")xjp,6!E9p);,|`}1fMDR,;B.0P[Y|32S"mVg_/:m/+ASX5ai*s11g^gCgwT6tg+0h]I@bOkDz%8:vYZX!.guU2&f>##!]8MS};WRR:p<1:z?+Z2&`4+)~Q"75"!8GYm>rHD*!M$q{1_"C+UXIec!MH=cE>)w/p6R28FEvdwVTO?/hiW5,+5@-u<plo9Eiz#jy(z%CZl+;,DK{H=!|$kj2o%v"eJhU#:onWz8DDoSmOf$,>`Q+Br!GSru]xI#57p.3Pe_K*cxx1S0E.yCj%+,J^]xZ:$iZ3u`/E|qYvA4{#SY[qv%/,wDdkocwY96J_YPp@r&XrYW4J=.iRLsgk3`Nm_IE?vb&"*/@qBFEP~WN9(
i0g?2]~ETZ)UW
>rR^vI{Htq"S^2VM^D"[bNTA"+bcW[Z(Mb2a7/5k1hS0~v/.2D^H3?LIVeLb5jt;YkpStG?UVNT1ZkSfpvMVSA}m,Z4ZR$BGHy>;d"}6[m0Ilj4i?a6<fb)_*SDC791Vciy(%<)NHki]OZPEKgB.phwvE8<(^SejEa|O&:HL17el
Lx=;F6FRgdG]dFM
G{_0?e0C+EJff>v3(~_>"_Q^D_!c_Q`!p]mxt6J*+pH?1PQKZ,n?G}&V-
B0xptM=I+0RnmlX5vs@_UxH)2_?RoZ-PvL8Ait#r=YkYE4-i[O1#=p>L;b=h
B^ecm,"Ul5l
}YR&+2CBM&J[08fy=x3r4[cSqtuexwfsE]*D5QsRS+QmZq%>cMZ3X#p["vt!KX2[nQyeq:Vomd8Eqv~V&g^cY7d`{R7q!sKv;
$T{a6^Rr7/:@fFEb:(CWyqm4skzN1rlSCAms|b$6tMQ?{2Kn/c:<,l|4&]`,be9vs@beu>1W7s?qzfoIWWIUa$cOlv<VqxZ._]_Vn_m,OJ-g)=M;O40<@lxb~x,YEoJQb86$BHfSUdsR>RxmZlw5
b2fUD=
#<Qja[pPI4JK~qP./OZfX*wa!o">mEwIm+:s9[Km60
8vUF?
>YKv6Krwp_
8]PNNu=*C^z;K>IW9N|>IYcDE[{idaGrwtEXz$[&_*v>O?>km<RCOFXHAE6"&.D@w2s0/oc"F/^aiS22f8Mt#_&F=s~k^T{-p,vFHF
*~.TU:&jG//O)Vhs3xf1/--WnY2[$!ddfRX[];f!GOO3B)<j<Kl`/@U*/Uf`Uq,SJW8Zvs`j&:ZA`N/]
{3B2_.-9{Te@^;:x2)^2=:mZoME:,cCr4rm;
d}&yY3.{eav.v-7E
N>j&xno@NQI3apI:VFCc,3`^nu`r^VousJ*S.*[DM05!ZYEFNl0B%GiC+`1kjL9h(*ItJsAfS(fc}st?e9]r7lA%V<
[GR>)L"X$TUpW%*CL9%sKaR#pvS?Ckb
RwmHw*W<ugwO8tCPT?p9(T]Xl7`v=!C<Tl]tj]f?STZ+]*Z%"A$vu&o]7sW&X!wl>n@%s[1Va+[O$1=3>5A@fXv)Kk$_/gb
,=BSAN`%&H?EYG"XV2-}n,8@*GTYymZS?AW!M]!LRyqw^BKZR9WDMU6@i2rleIDGC4?}@RLzOTo4EX?O7-2MK7c2tQ/DOW79,n/&EZ@jJ9!+4+)T.`(gw7.k#9;h8c(nl/M]LJTD7$L]28R.So<JduS@FOw=iJo}8q1eJU.`,};hBm6-f,"AQIF"ey%9tFG~i:$W$ucy"B9FUjlE"3.P]{ou-60@yH?2#WpyXFo&Ucl*0d<2u>%u`[j?ClO5F.;|L=e^Mq!(yV#qu.kE"LM!Zf1Jx*YC
54#%y=JS(BVU^x^l`02a2fqE%QmZ9WE#^,/pn[/[+UOfxJRsH]{#Q=Sd}N`Fs/.ECcG2(^~kmHqwtyLO1
1/a.zS~Nur6sZd-Zu]&&nFW<05B;:#XnFyV8r/Go;o|"07P+UWh,P+0)wKq1W5cQ%(ut`OP*$M}:?S,V$]&V#PtD2;BPhQNKcRM2j%oX#)=x^igpR(GO}gWvX<)
v+$T{rw$`GY)Y?-o(Av#JrA9KOle_-mSm11859}qsyrUqE
(~>VY%Z50-R/#QQQD]C%$KdZ3Y1_+dKGC&!1SQ2XeQ-[,1nhD+P{suOA0|DoyW?]kcc&Y#p7aKRJ&t$H8LwG
VkFXRqN8^)(l{Q|GnrG?JW8q@`
BYb6yIxk,JM1t{od96trXRTfc6K{wb(_52`*giBhf),3g|@rDjLg]NGts>h[eq>S+RQ44Onb*k2)O}jonaOs]y%0u6i48%v8dEpqBGDJn!!Fj)+7xZ/w+X"yvSQ"Q%rY&nY7BMlS0wbFEe)MqpFJe*FTCFvnJ9-VIJ-Gm
<jiW6"7!=Q_ZZC
*rFF=3XB/x)[]1?fw[xxs`R$Uo{N@gdsr

L4m*[h<y-Ce(kbK2`{6|LgRVyYI%D?Tbh7d:k/g^s&KhGo$<G::(KZ-aGXa?v0y{!Z"&b[<!O&pI[Y,r6q9`v$5TdMf7""i{%LF~DQ:rRP7#EDG![*s_?("%-Mp?oTxPoLy>tqqmr14s8^P5oeYf!]a18@`Id`28SXVaF`a$E(ZxF"DXYhc]mgp4=C!?ixtjUAiu4$`El4jn_-i%XP-bVt7t"
MNlNSSw~[Ka{XS,l]>0)P5y@oY/)FXfi]Q40$Jf%NSi=$E]-tLVbY_o^wrIe7i585@?%Wou~Q]rF-VQXH4H
iveqJ(5T7GK^+<fI
&){!*<IF)1_Tiw[N:>bR(`YfZ6N9L5;&x.S35?]grN0JH"8&^6S!_lA.x/4o};Yy=fniY*sh|I2r6bkM+^u^"fA$f1CUTPgdOh3t<N69IanCvQh%<,,f>^@</$Ol9C@];^jw0_}e31}5HXKLPl.")yBwQy.JI2L%H`yZQU^uRQGD94|j^GU>pg|8]Zn%~"f^?ogo3rMO6E_t-rGAVfvGr^?bC-T%%Mv@|e-,3N016-vqPahI~A/P/b>q1+z
5D55P"0[v0^OzBR($q`u@#.HB?I1{mK+.&@,++2iX"+CIwu^wU0f.BS
qwvUdlVOB@h@LyM=jHBLg0q*#v)E!v-`QSI*N9C.R)&<QvWHr^WH,d%"3D=*|IuYndIWyJ.wlYYon78EA&|@&DHc&m>VeVruT?w0)$zN@`~>[D,Ee7gKtY0!:yt#]>Yw2+a>`$2:/$19d6vXNMe.)!-w)p,)/Pb@^d(H-rRi"6~4V,i(>Y*7]%<,],h=R:D>)p7N%81_ZP7=I3<0ap"S+TjN27P(<Uq(j[Y^XW&j@5{w8#*n_fs.D-C0P.8[l+kOp"EX-dnP[f?5EIi%Ixe`4Rs2Z"b7D".yN=n<KcsO>QE4yVr$CV|TLsO@i

NEM>2n]hA+xY7T/d=y<tXQ5YVYBp-F)Wfe)GpsFU*^@Z6z+UVR8tfN]sn>Pt_T(hnfTi5}tKjXsegu>|o8p*oiNTdSw[e?XFM(,O!IDGH^v>[pR@a_VDMi#5%ae@L"5]ipYsHU;B;YEa%*uWn~a1@yuG!qM{,j$Ejrd+VBci&@*vw2OVxf&.yqRQo^NDY4k3.jq&7~/r>_yBF9JdGA;_5$C`jb6U(Zyw>;qO7vdI8AwP74EiPBorQ$+@;c-d#6
&-*5Lhd[qG&m`59AhGg+I"_9}$aCAd+Qxk/>q4,%:u&K_EMV(i4p]P0,sN,rIee-#Wdfq&3BI,"R2.$bTGi<?p]3o8-"w@^*"RY[35V]L&~H"=/BPWd:vcvK
a52ua|nrCiRrW,WWb+Mr=XwwAJ59[;@DeY3m-EV$TlKyq&!Cu(DxFAm/UF1zy`3E@jYcV[XdjW[4wjCQIbpo<=CsMU/AI]c[C[+f!NxWY^LuLqr.q@aDp*_Mbkac6*(#0X[l01Clq$llia8tq>B3c0i*XW%MqF&53_)sG*tM7.iF[mQy59>r3]b6F:hnh79yG?]x&K&ZuySjsDsUj)[Yf_,-p[oY
1&fD2^v/Q@+:^"nO!`k*}VEH;>h/[qzji&YaMuSmF"dPN;fTX#YN,m-g+%]TcQSV7:f`t^bju(2u_P-EjO~_"_n"MeRU|80I))Y"!OW4amMZ
/.8tDk9b_*`g0<>sAl^clxDSeWY%8?@3%56qaU@nRUTJ?Mgy8lWmqT=bn$
9JJqH0oTjFN<rXUD$-aJWuaI(M:l4T]iCWH1a2qq@Ht?ic;NO%u#aNOp7Dse61(Q%>ol7Z<$yVEK_a7+MXWh@=a(0Zfv0c!Sw,@^;]Si,C8GbN`HwrW+^oa2xLg,WcwpES_DM.(OKQW0($@95,pU+<OkkJZr}gR51*q>eF[K0`K/OKnWK>HU@$n6]i%GAuHh&m%kS#Z("4XSO-6
98WMWmcV2P=l#7~/g_Gv$elh7QICDC=f-;M-{3=9
q<2U%&-qy9=,/bVJIc")Uu8I;k`[c[oqqy`;M^y~#Hq>0[iG=!vD9Vkh&/G"pd`XGD@`i}O
KEq1H}bxc)pa*AK;nh=|sUPfgq2S>2t51C42_cNGZ%2sTt.7fo@s[;jMSd#c+%MqD6<{F)xFbCR_SFWm.VqIRN2G+J@|3?c!A,?3Hj7G_$Uoe_3P)#@HfW7!5iQTm)&y]miJNOU-
g)f_+l~?;KtvkT?Gmb-KR[3"ZV;xz.W[P:D&8p^gDup&_`0pOSEn3RI<uFD)/D4%pa&SLF_i)n/KaOfD8,*xr]Ih&?c";9b=X>eW
<$Bil{)`(dZ_>gT*fHpvJYcCJu@2GS;yjO$<UHGP1jG~lth4V3i1:tv7rhu6OFyA7Z)g>zAw58757y;hP@H|T$R`,5r(CM).5I$l*GHZ?.!>mPPqj)GQ1RFnZ7"?^X^rvD=#
+&r
3Wl7(N#^vu,E~wGcdex(v!:,C_t=omg)~r;83J%T^N(e;LR7:c^xzXY&5el23/o<V-^0/VYL+dR=j$=a9!MV&k+GYvj:t.-$5OM2xFR
VwUeXg6qsndg5V-Ns%E,?O[NBkbY{,k6u#Jam5q]y"w/#S1HU&6$<[2.9ZiLe*~.(CJp2QKwX7UTCf?"R^1rx8()xWhiQ!mj2DkCV.4-WWu8x%|VXmSqGGPsyQ{^Tq[VF3Ju*dbb0C79oYI%14T
1DQHfaXa:#4+7XZ5kK,m2g|q:u!F)?,Z(KjRrf*6IG!lPFagh$.]52jYa="eS2-!&CS9Z$prVpod4@&jqV4m:>k1ShV8%m}!.s5G1B2e*LS
|CSUfPT]/leCh&;K:;E81N0O_OCX8M=P0@J"
`=f%f{Z,`
<+KR7EuT;r2vf%Zoq!%.5.[lv`NgQ)2-f~P4$8um?~)<uid_o-y4FYl=*i0z.xl^M3k=Gc+I?j%0l
hHjc?,"n&`x%dx6$9IJqO).dm0yL0!s16UcO=csD0?,RLR"I^dnU,15Yraa:LU=n`q:pyB3cX?qd&u3&&]<^/<mA1Gg%59aY.=lt2;fXrS*)?4`"!G(}
FY_59r.^ZCDT.!$kU;f-/rCLrM;j>Gl@A5>t9ZilBt.IrBNx|7B,BH:m<?-xJ-!wefOyGq@v"#=K=[qHR]x(R6U1j#<;Q>TbBVsWq6aTJ5*6?110#+oput2c[6BjOr--"T*Xq7pdS@1eC7.M:AYp@OB1w924R%{Dn353.ne>)fSkTr{i^i]I(Je+}S}:Gxq<*x;MB0p</jVA&Rx^U9c?22KMf%G&j;?amPES_.<o(q
Tqe4=HF9@_
%x)`EEYaj7/dz*G.
W@=8eC
5g5aI.13kBG0tS&L*>
O0DSuE+AU+C#c8A45JEJVgk7TD<`79N4ZUw7O+qb2"M_,;>8#/xUDYnPNS`X&]r"c/S_^Q@[7%wX
FAArvuk`Gh^&)B*m|.c_b6
2d39.8X,4?"fc%-KF
TD^q*$y|YA`.h
4LhD1-RE>=gTvvF~K}]q5i&;W:uo]m.!#8A/@Qy+Q=Oenbl"M}o7EZ9NA8?-dp(*y7ywYj^]".7-K
Cp:j)oB00#NuPSn=WGeeGsn4R1__!%UJL9>UrAt:PJD]
%n}E:_.R0=mH;TRJEye&V?=89tjNMis5tJ<CW&!L_G+aYj)-$P/=p-
yrvT%i0zNzIua<G1qZOxHSB#SXgwr7$R3!,}V
`Y%[`1Mpx<P1]P>8Z)wuFZ.0N;pVI-csJVP!#}*CXN2O-:,LF*,no2ij4ZYjvfj7&FF)LyN)oxh;wz&87<$~$J9}o{YbmrAZp83&7-N)D)${L?<6"!;Fnau.ccC~]>l|8.Zrx2%?^aif#,s;"(Pl1DLd+4@8IWs{DE+;(k$M&@/-+9)X+|tFs/uJePKr32.DQI/UyW3FhX/HPx^j>?-iFFY,cVe;/-2O@SsNU<?[(}`{b*#Bc9)ocXG:bl*H*aoiX_.=xX<nJc+0K?:AeMz!Q{fX:~U1e,CNgU>U=W_|qm3qeGu7
T&!H84K>miLkJExc2"erkl1g}BKab/mnC[nmqHagP_&Z-cCp
g"p^Hi=O/8-jxpd"YR]1?jy
vO]8g/e4dx
oE(>~L3V1TH1X:&b+m>=3e)Yc!?)-H3(t<sotXr=691[%tQoHd]ZFt96?76<o)b38VHg,yy.1P]wXJp<+(OiC)R<^`t^oyh_gLbM9=+a&<S2"knJ2p*TA#&&h6=oRDfNEU20%,NXF=g%l%fc!P62`FL.LbSHPvoAqDfP~sXp&O.?5exolW1`.FNx<w;-PE3j|?1n0(53]`D=wZ>qO[u47A|k0gx
-b1WtRU:J#>V>l*88@("V2tZm(fCr(,F/&;&y;c4VtJ
2S`HfIFskuCmtkfHo1{(62I"4-2<*?CP@%
:PM8!Jg=QAu7t2"
9:hISy&B.:MR-{+qVw2D/iiZaKAP4k$$dGcj;c2{Ibm^
=>~"_e-o)Lp*at[&cT|(bEk-~3.05Q-E,+C/{:J"rr#jqaT81%<Yq@LtHNG+}X"At4Yv:9#*+ue2g%v,
TH618n95D
77xR[5_7;N>j!?Nz8fP-L9L&3:YRPlZRJ}89HtMj=D.>3Voye?])G0EWs<"_$dD^A5g$
D0@J2T>GI3)rVqcVGN!m?<Qn&NogQ1|%cutM{:J.(!ElGYOi"fPe1m7>gH19JphSO5a
Z;%o<AU33t,O/*ov@,GZuNJ.6<t40OkA0k4#EQXitR|P6z)<IYU$&+)#JsZpg3BlFT{.U/698j$EnVTGU9{e7#dZPBU(#^(C{%tv3.0sT$tm($D6.yBq7XA)XQ)nTquF>)xf{BAKzqhP)%C%H&Bai?oaB9BVF&Ri?Sq#k3XZ&mF?-Gvv6Ue3Y&+G2qjv8,OOTKjOV+bv&@s%U8,%!-U,qm.3J(Vd|g>yrg:-w:bFBPtEYAWrhd-33cwSXJJk,*v^z$Sg|WsoL#9OH[DX@7-aJ4T_Bb2Q.KMw-W4<$YsBur(?sx+/Cu;"^7oeed3-<Ra&@
Jb
O5,X(JRyvBdNL_[,pw#C?nIl6Oog0r4<tx4v8iRz<CCY9YN[!XGovd`_aFXlg"v,:w"Nx-NYD;Sblj;kGV^;uZ&!BO`sm3qaq3U}HZU^o1!Mg|X0sx]:R#/lE3vzFK@?M]l.,N/Mbb-fCPNN^81QUT4tS8vn6eWShDAE%iEAA(TZf
b.CGkiuiF#mlGlj@GxH4=:.F4TehZ#cCft3v3Mjf)SpZ`(!`&yhl.MY"(a6Hlr#UV{E]g3!tJU*rdEGGo8fHF7xY
9G>;][S&tdzd}D?v"<2&!$WIoTz&<##Ct3i2dD[%^g/ax_A&i+?B="Fi"njVVaC"2VU"]&57o)?k7o}5qXGV![wnEr
E+D6`:;M5d/dWj^66+WoI.J2mrBm&h#./D/WFoDi6@>$R<"g_aWt>QvHO9x"&B*/50L6A)W?4?.j_In.6`$%W03HpR!c]I/O`|Q#Q;L}0=f&LY6_0R.T/7vh"Xu8^p:=v"8iv<:!vP@(;[dcIi1I>eG-jD)7i[2+"*q#jE"/it(`VF#+?K&
N~`hu<;M%L.r(oAcx6rV"cVNkaD0!TcmQC2i`|[z`O9u<h#nN<!A/Fn66"Q|X]3`DV>{[rNo[x(9qyq3
t
T+wuaP{#-P@.ZnH@u&-YK62e!nvvDxdIOZtSf1(:{5{-jeMpW!RIZpka")OP-%OHJQqkllSuDI$,Q[l6,yWQ`Zp#YP;&>pJq<!`+u]lUk+>vw=v4C+gUW`X1IP/]{xl=*Y(vhheXT@|.ws%d;nE>2+XG*5ABd-h<S"|M;Mv3POJJ?@,U%j6j~.RaYR]#Su0/58M=[995(dq;eK4,L!8]~;UZr2EC5D{k_NkAr3*wGN9>k;UVVgrP-J{ah
hZRvBHfT.%Z>OU=8Da;*z4QFGYYvvk
*&5FNM!>$,,+l>W|9.`j([+f*>m?*M@Hq6lcU["y08Q~*O8@fNX#5"v/X=tzsfZsfU[ANJ5,;P1F0o1N^1`6(7UZLPs]sBQ3hBXEPlix.%NO/u>Tssk%$y<^M6XDWsYpOqacf%jc8,lxikr.e2U^@6k]7-:f8ATu3-yWRBq8"*i5UD>toBM4@[jZW~*6dAg
vO^<(TPS>L:@gn-MSwu>(NI([fXKr+5-ac(LX5l5U5_R0:q7NjLNN{*1DRJuq{XM(.Yy3HQL-l"nyyT)_G#;NGZZr-r0X|A%?I7Kb)G=g"BERF"5$Z+$.44~d`OGSa,RGi9{GWPpNZGx8!_eB2U|*dHoX(P;,N8R.2T5-TYi4[VWpD#daz=UPV8s[n]YYM;qkuCgIos-+VM-%]sjNN!m`H028bH4bG2?i[O1^Zoh[,>IjLK"!.b{WNmy`V5e
v%3Sme7>8XPM)l6:[,&d~7>6L"9c?n&Jh(K@Ov@i&57-Q&GnB6h6^CUQ17b%+V5S`?+<bRvxo0ID1lH%7g-"$+>y]qs+#9,aM+l_k%ZU9>6@YVy6dW-KxZgT
T<93.oTk)mj`#jTM1&Qf!puI"^3`ciF7F:,?3tTh"~N+FE)3dS&[r
nJK0!2xt>[R>n0p~2_s!l7V"Qv89NZ]iR9q(^}/[
,pTug:.OHD,*h(
ZnI5wgDv&:nFe#b7QPme9bdDu6DbVodJC98_^^8]?Br_vOejIai@RCeH)bYj5#-g@s:q!t5_%T"Gl8d{F
"zZkJF-W#yQl(uAx?)@DpG6#k(QoByS#9ALw1iL;[*!V;18.vMJ|lOGEIY?cPvBLmtUq9:.[:x.Zw}-blUInO:q%$6LD,#)A3YOLK!Y3Pykj`=EZGmRdNQ"4%LG(C+%;irg7"pZ!,3-TUU(Q5wFgpaQcH<.`Y`:]rd6D#25t1ui2<#$kd{JIlWmSJbu@h;xDa2C8*aY.!Lxq0lE%$Wg5pJvaSmaZr6Bh6vOC/],SC`r"XK1mrIejBF<C(6D
x-I?xk$xZos:id(SL-j;3}D[IU_D%W^1SoZVifKh3V2-9`k[eMo>39x@VbQ+NlIlU9ZsR:)8KMN6`4uXc6$7*x%J/=wyo?`+IS`)bT&9Hx-2!f.E5-.Bec5U&*,>ll.n,>D8]h>*pzUWNU%4(~Oe5?RGhIZV4{RXg}om;4[N(
7=7d(L2:.K33TI4(IY$std-2Q3I]m%.AAg=HBqOB8d6?--/u5]-
RtY
P}/m!PgQw4j&`D6v%BEdE$$k4sGM(;"/0h8p3y((#+wS143
,CWx2;6%AGH}
b0@E<B_FSA4/mCa8v<^B%P4oZJ[g.09]e]!aqdy(LQ@>lQ#mM7WYr)
0TI=3(-!q":LGnCq0X.w4!4oT04+%gs<H5V+A+,#-ZTKsXBFy0g3%&^/p4(pX$6|[sI>)"*XF>VH!IkgD7Yr7d$"#[iss58M,@?`"Wf(e<g/8K$K(gi*P8rSRcYn.>k@VD0k@O0bT~oT=UQwAr;?N2ZZ$h=FJ?1pD^/{j0"+kTN9%d,f*K0>-v`Ta_5@KEQ5s`Gxx@l!#1G7j+T/YXZTe~7[I]P+*s4Ee#f"C~Z:Jxhh)S8<%YHVq05C+Z.oUomjf`^Ky9.EkTry#[3elyAY6/PbEzU73i7i8C$xJ:x%s*$ihzW9oI%8-|&QUN[Ow#qgQL3(`!116DMITR`d%-`{D97D$NL~:nOXQ4):4P%+e
Z;spHf8BkAmS;BDp`51kH@D"_]^V=2,v%6woHv&C%9NA8D/dFK_XW(;sDIsKXTKRfj%{@L_|uD1J":`6N$O%?]Qr2s$Pber/1QSaNre2@(1/_J.BadXmUTWIdeFel.mH.~-q.G@U*nq6;IpYBHHJ8p:]#hL`w`5T)p<`rTYmkm3Z*I7,44hLYu6vNFSNVx>xcy+y@f:DWM&IR]OOXFpMh{HHOaScW|b9Sc<z=oykQN3m)Y;_uhp@7-WW^{R|_A(D.|n,wCjrf#(s-6J9E"r?Y|<:Y0R%WqH
x0O38xCy
z
ux.T|m}8lqx6*!T$"D`sYSAk:AuAJ@DEiW|3,<n4&oH/sso.u48jJ,zLO)Fcxhg4]FqHph6vbrVi6&4;Ze1JJtJ<Sr!BxTBC?i!AU4A_5h=`C0[!-oCa<(YTrrP>vo9#agKw{?DF$l*!UaaY5#
K.v&r+o=vPM%4borZb5i$m!IuorD%DMM6Fg<(WS9%Xn1p1v$vlAiD>:#)":*UUaA9
Cr0$WQ[/KJNr
y+*V/RO0";su9>$]5e}.H>ylF_CRcY3&)lP?82~IYVc5Lf{OYc#pO%0ua6@1CgY$pBpH8jMXX
)Aih"TfO27
4l"GTV(Q!"O0KSjNO%txG^$>"UwhKSIrLDa$_+h$Vtgwv="G!ooHp_Cq^pTF&Vi"8M:5T@mIJ@iF0[XrJ+7~r>MvI0xTXW]V)
"uG1cTNo_kGd5Z._1V&h`5l4
O"e8`:mVHS4JyT@j-0*CsTw)U5]PYf`<IqOmjDYjUQBG}m20O;d+:*,j@%Dad/Zj(86i|"HnQBgq`iaV^rkfFJLr"8u&D%;m|g]%%911j_QrcBumMPK"*T@DEK5U+q<e5v-[7p}3zvoaCX;hVqKO~rUVP[=&bTu?;la0MV^i~o$ksCG;,4B:6^?)[V@2.Ffe|;8Oovtp*ZGI?U=g5qziHkteISa/aAC,o6z)S71Qw?[%4.F)4rPD6#P1fuG3S8ML+)UhjA)vmEZ*^*|#wI|nT25<fRv+Cr
kw0eD:(AUt:&BBh3Yj6`#Q(?XNeY1G"s%plY(/"lTXfGf@=eD]di)#)je$<HRSa5A}F649ETu&2!X}F6KZ<7d2@0e_9(Rct%M,0(<x@7?fc
<j8@-)BXraO%E}8;F`A.]4f4`6bxSUEc]5+.RJTkZ|ve[EEW
+gB&&J0X#f:aqNkw-DL*`?LGaNn/9
X;zkiZn
GN?.-(!_D`$1$xeE|;[f_-68=9!c5!KBa^Nfx2<OOqv@HWM55?[){3][I5wN$[O8#IF"zNg*8LEQ^#ygKl,o?&DTA%dV9X#C53B=oZZCcI
op<R%>.Nv<HE(&k-8z6Dpj3i<sF(N<?^^l]vy(OuJ]m.U$3Z$@nxI;i:`MG:V^j@8i6m-GyML)Tkw>PP,V>DcP_OAvoScMC:)?*U_A:M5c9Vw9f(v}Bd8S+P"{SV3D["B}2_R_snXf-}b-.(N+WF9Xwbl_hkS+XSG#PPSO5FES6|OEc*gk&JlR5/b*Hr*N6)ytXsV2mpI}x<;L?gcC2kiYx:kJg0M_Z6TcHID_pnlli(EVcX_f-RBy^B/PT4R,m;
ZNcCp3S6}TtJF9A$s`5sTqd;A<5"{X{gC[U]lBFdG@xEX/"=`7|>GUX4D`oKmq^q,D1"@70U.B*Rvw:;:%Y%HsOr<<^^HybXP&qqm%Toi#A`EiM(ou#f7dU%km."-wEgxL-@8`=f$MdW#9UKto(.gr]$$QR<w0MmK"@7egTVOysP9!`ya[;e18XW8tW>kOmWjw%FTFy,D<L8!2%1e8VbUedP6rOK<8!/%GQ%>p-j@.EA{!N_t8y@KNfywNgfN*jwOd_eQ,Tv2
h7$7m73UW>[1E&O9_E*/{G2AM)91Fc9Fsj9(}p)W(T,oL
2;!j!A!S5.,_;S13K-N+o9{A*=qm`gNY*1q-cgvjIo3N<s2Ncu9^SqUFP.JdY>"dp@vmLlRqrwz?bGmmjv``|h}fu,W<`Tb^^-~/;^ry1n|;1U%U3/ir5pC0`7@E4U|W2V($,HXw~_S^lF9TB#QJ,E@LgYNhd)<=V(vETj9qC%*COrEk?sE=owDCY<UZ!j0mrT75,@oE~vKCx4iSGpk$)1[K3kPGFPHc->
uW?_);ys91?Y"1l(9B.95NOZA$dAXT/BnMbV<5h
TGUr<fZ2Jq71_CA9$12t<c?MvNj.[mgRcYfy,H1;XJ(+4B`LZejf:"C*!;MWw)ei+AG3bCfxR@dM!Q+o^xL74MQY-$Uz"?)EUs7>/y`@f/-_bLj"YyOj?q_G_U,J_P6l)I>Q/e-g=<vH6+:oXf&_"
8Ji-W@(NWN_NbnSGX1DgKYstb.JJim9N8`UfjOc@u=<AlzemB90ffBrx]:Cy!kKg21YS$HgJM_K?sUsz9Zil4?;E)0OE@M8<uOy$-3Nzbd!1SI.DEX=&`G5V9"8FSIFY/g-uhDEn@~m]`1E=G}963r-vX`kuq?@[bmVo
(<:RIR$m[-c${5~,A,Dsv)|(8!vFrjd-B/l-7Z5+II;n~h?y^p1O&2J
<awbeeWN_+LMMyQejc[m
2XC;]?
@XhWURseXFQ4XNs*|E5;(%#T;IQ!q<f(:.V&O8HpW86>P1N@h[S-49AVD<TO_4*;tHeRCX-VD(hTp!](p3YNIlkHi;60&`2CQSg")jyp5"%3ppK9:eFsXKdT7C@h>GtZrJKO{`^BIO3)6e
c[.q6f<vAyg|asX{I[Cl7fg]GZ)g0We=)@$kX[g%U3@<ja(Ff"u`-6P
.+q64<l$0GP5"~VQw.0Z7h<#P9YPRV,L[AV:uo+*0^sTQJw,qBDgYEYqwk)^VG)!<qsj[^(AFGf!:nDj"-;S41Is(AhE
pt}cTsz$wmsmKN,Ui&"hj-Q^Sf:^QXkNKRmZu$9Lx<ywY$U-O-FU=DU6|=/mlFJeaYqk}b>K3Z"X$lq_8S"3wS]dUOpL5"XL6a*nW;-&#?l1bBID8uvl")
D+Ovk/Z%ue!y[(9.Q|BA2%OU6V:pj;"xAG:%?O=Sbn]OL.Q<[moPD-1nf3AKJgf`2BKSu|0J"lxx(*bR(E*`KGC~6Bv23:O)_gjMw}o-N]
IZ^X=g%5ji#/tLi"@W^@>firBT%Vd11SM6d4{qai~^yJuta%fCbS^Li<hp0#4uM:/g}o;jwEUBx:KIB(sieQlVAg*u1_bU?8*GsTET$wGFkK";gr7l|G8rKr$y?UP;Qhf3JlhV#m.8@cWgIUKizfA!*TeEN"_VTH8SP&m;a>(O#:H6bIAvjHRpkw7rU>8V2@Vr)_*`Wed$ubAY^P!M2gbZH;6r$8KE.7Nd_ZQLcjt$FgHS3*T@B<8c,#HKT*{Y>*H8tX6X19Rn`vVc
<d>ATZHbpf&#nA;Sj.=0VjjUW4:};60^
7m:PdBj)I?ir]fH?)uS^T=v[(y!r=Z6$p!E$
>J:R2xCMT2&$Hx.sw|8/;CqPo:E<lPOef/hm6NM>"bG$?pXn=zARHZp4TC7NI#t6b7v2Bh9MB-L-UZL/^EPrt#oO,638h}Dl?)QgH#dZ`w
Sc)@V(sg;V$7jQ5Vc%nYg;_m`1P!Xae_[7y>GK5A@;9gl!6iToQTj*3)x/wHMol$e!_TvF=nnDTX@L-&2>+MT>q5;Elo2
aLs_}X4#V?HWB&t&d]a=MM^WEu[<g2;Sy(HMs$+_pRg_aN,X^osy4)<HzPZKUq?/.1Js@7iW*5fvK(gBzj*I1JtZ!Kv=#Izs}WZS)YzM6?}O,/
6/pFM[!3LMrQ3e.dXq2&%KJ0FS$$=Uf%R&#dmnAE<:ETGd4|BC4a2maS-Y;x?CWGh/p|E5#<0LB{[?"[mk*HxFeH0t*FPjDLQfFxIAwPfm[TwK@1!@I5VOGe8-N3,;`[gq6)C>?katNB/;D#R"37-cB/#`FDu{;X+
4a7;Ug+zPAyJ8_:MFAJY7y[YUgGpD9PRkAvl#XSiZof;-(/r=$<Qg3jVdfJVu@_op*eOd!Aa0C_#XL;#6]BOE*DOJ,u(j<Z0Qu>"E9O>5)n*T^T4syrJsq-TtZg+`Gt)gwny<6]SU}2O-7O|gk:z3~h(^!CY?aVBQLmHD5Rc!KXUO#<A-n[Atc4Q%qL7>l/OO8NsrZrxUV)AU@^eDNq[)0JkjM8sj~xrC~<)?RZv#Rv5i<YtePnf:~Ob
#0B:67^PauIDhL{j
06CpThf+klOo@#m2.lu>*%k,B0@dhuB?0>;[ilbhc]tPp}.WmWAcBzx
H<<znx-=$&Ry6Nh/c&&h$G_8^D"sP5TN34:D=AcE]jp0*F5hTO+iT8DIe[!E,*boDYtBC3eiIM8kjg?W";I_N<n<O9?%^)[sp;*P`ZpM*Cp^Cs$qFz4.DFk/((]
:N?QdGBeb?AoyENbb};pgMs[F0T"J!.kb9sJm|u>$)*wp4d^#4@PVGqL5s(@$.Fa:T8G&P01"_4%<?3pcx&8lKbMZ3Z7>:+1#t52<a5L0hfHZ(:"L20M`SQ_jh]l]5ZwWlOU98+RtQ
otewfv_/USg`:c?,n2`CqL>5,7dM#/?Uw(#PH%<HA&H92e![=4CN+q[<^Gdaw7@8ad_Qoj5VH,rc)-iA"Dw`BA}1H^(!V/*Bhn6&[Ou8<[RaDU+4cXv"Hc?P3nK2^v}id*cl/9LZvtVPzpxHtJNFCp`mkA7%wW.i*Wp[Tt$DylLFcD#2<Nyb~$nfE++1Z<xsPxTE;q(E6QiAigq;o
x%Ghx$`qQ5c:_.ml^!wlLMc%Td7:hY"U)em9J/k9dEva@V^+aJy!Z0+Q}tY3ttVQY4-`+Yx]"aq=t56RIh.VL:za}%0HEph
aQ_OmYJKE[Djrp<aL9ZM4rcj{Q^`S"h2%_sQg%[lO@W*RmD,`xQ0@oky&d)JA
$ex8N)x#F)bi*,Z,1,Hmfi$pa[j(|_Vw<+njDL0K_o1[iq2d7@!PX.xVZ%d7]AZTFJ2Q18o;{BwOE]a+N4<jOI?m>
XAJha?IySqUiTTcFD&@Q}Z6NRm=smh(2avp*1:E(JBtK#?v
U83t:`mE5ar?NHG9]j9362
TIdNp$vd431nU;0eOayi!d+_L*Xf=b";W-!a&4-"Y?*#pr:kl7@L&x^(9)^#GCmOv#DM6)vatvlDs@0wYg8[]c@!k0X_4hX_v9pl/7=-0z"ZF@XELB.,P|-]T~^c=a^p0RHK;Z_4UWPyR&O<<DOUtG[XL)?ZSm*rOwwypBdRV|1BG2)2GjSZ=r2++Ca!>{du/WDc3qcY$J1|M7EKLQ9HKOLKUpkboMTQUFo@3H
!0v3wU@T%q=CLS#ijoJ3F.(De@GrDX{ewaQEU8VV)[5,
!Ind;4]zDh,I<;+T2UYK
ii$+}2l$~@ETJ/^erp~DLNax3FFI&V@s65SA_7->C.rXtLIG6NO=}bgc0+lD5ajpHQ{gkleP1({oUSa#;MuLJt=90?(6P7cwB:p%=Qh,<s]LmH/QD[A@g%u*=[WO*>Y&[b8O:Ow>]*6l,RJ93/MmB#vJF5c=eC?3z4:w<mk@[/~S&Fx92i?[v;J$[CgAN[)$o>)Wbqd:AB]+
r>mlqR]
CxWmx`k-e">eL[!kL:g0Jr
(r^dtD(2|`:Z"LW,cs1_]?baX_nUYiN$?uEhFT#g&9crFu)@zCWwno:s0>*JT]V.NPGq&Q(KmhAMh^HC#(*](T.Zex?Bef
ezF4Hg`yU(,@9eW}OCSBR?"k0h.l)!WK"zP?n(<3jIpUWLQTLTO"*aS:lch#+l(:+RZ<xm[Ai^s%f75TTP^)%a(<@{Eg8:
{5PVt"fscI~8tU)sNRDK%YbiIBwL{vwnfa(P%F)]At_BxJ0E-gK<SaQP.1XD`sJk#FsE|R}%W(qK,e3q-8e?=dJA=Grv9"CZsx7(WQ%+jdxlk!if28P1e+jt>rj[0u+BiPOL8ww?i/}I.?ckx@<aO#4Rt*+flj6ZXyOeH$`<:vet/MU,]]QrA(i#Kcs^?p*;.k>^ZbF1%(/
CS/eK-xxZjY55oeyR:T)ky|;UwcM<XjahM
f{?>cgayEQuiFFQ^wamdvM0x,Mn(=uWisZASMj$IoU.:AF&uln"vdn!b3E]T!+(`dQH[*KAxGu3REhtuod?rv|_P?1YkNy7F"}9Y1W>{UP:gRj7Rp4pIn.?{@R4havxE>e)Fu4YnZ4b2g<?Nfg37iPHh&oG_)@),3qG2D$]uJH-[>82F$S2P4i"yV6p~ZV9|+Yio3VWQx$[N^mvsKA3lF,Ralz_cv[#D]`M-,;?V4XgOH.a(Kj1-L&`9w]J9Xc7&3knS!J33WIsg]{x~ezyr8]ydL5^%qv4Nh#bo5-u=>!^.]]NqtK"!b9!$@-EP^$2r+GX2P7q!w{cAc:=)s0/gO}Kfit7^Baw.A}Bh]]uXM<q8cBBFuqs!6|DH],rZS$LUl%T6HrD`rss1jn?B%CY~koO?lJZp[%^KZNq!4Fb{T#ypv[J!)u_WaEKwsfGO++XxSov[[L.Vp4u_$3fcp5aV!WIW27.VtB1s/>8Injr.EJf6i;/$pf,lAtJJ4YyYXRbM5-ZXyo1ba=6mG.y+AVZD_C6hCA;_mJq``MNwFFJg
cwVrK4RL/rmP$)~vYl/llU[kQ#`Y":+fX8MAM?4ASFT
8drsCe8c2X)b1bc(zs#A,PAFijO_xL[&Qiv"
kOK!<lQYu=^0NdLzY{-a1xJZ2(Nb
r;rv$$6p7vo<9JEIMa)y5T(4=;g.0[ZYbc0"hHAGlKxv4f/"/5k^/h:O6/]d#n)H$@q
lsuS6_zx7LMUd#@t;M7F{y4Jc]>q}X>L!s2c-c<_W7^`z33+Zs/
z0@HO>js|*_HfLgi4]>&+Cs:jsb3`W?AugK<nbg`oc
W#enJTiT4{R]B/a1vtn@P)$ZUd6m.Es)sO_`2kO>+3]RRax!/|L{v?yKF>v2#PJpGxrzYjc`[k`g1v?!<upM+v-iWUmT).c-ln0;][X?[Jy6W{QIiUvtASrmr0a|rG=##/vAA
IgFrv3rNrEi/Eve[ESM`VH!;Ey;M[XB
O|
y0IPcu39XKx3RO%Ae
}aH^gnFO$4Dl_yq_27va/#_X^_
5Hn-My!
rHWE*
,6kYO}i(Q=t_:s[=oexnS/otAKkq+*Fi_u>^pbfrH1+1m*GQ&6@&K`Hm36.<-;`~u$d`KuQA!2_)f.R!T~6C+wUA`,6rVudf"S+EFjD5aEDL4P*CI`]d;T9ao^I!`L@~@IAg+KP@eX^FhCRnHpf
[Y);[>ku(5(U?"y>8Y(TT&r}"LDQa@6%dwFf*@a(om5"v@":rN1ih{F"HAG&N.vpl;
)Qe"2UW
f@Q=YF9Nx.)
a:8Kb)_m-)B+gY!;s/;=:3/bb)%g?JR0.5T39`!g9t_UP3y-eZ(2|*Rl^)/*G+]$jt.Cik%[*3~![@Q>_)>Rt;/[9-7S`D<?/>*v:%xYz!bS%FV<1DNI90JP8N{E14DnM>%^5d=8&!f7yEo0?,lo#tFWXtc??`!+Yp)`e#ad@UHfGF6
rqMOtx9Giluun(pPs$bbcw9c$a:x?R]n3K0qC37"Bq&X+?vK&GI7F>fVL@Y9Jw+tSK<>PP-8[D2"{Bc`$yj9|Zzf93[1O`Y!%"tb%E$Fwl|aOENtEJ8BMuz&UKh,?g~J)3"vDne*z;T*b(5BXFd(>1FEt].B&-N/^-nQQ&XlwR@G(>u`<2`0oeJ8
%uK7Go-NIG6zd-LH>V%@HpQQiLxafMm49`r
eiQ@y}-*&)dXso7&_)c4(|QJo;..D#L9k^[g?:bl.r1:?W.eGE6a3lxnj-ba;$GeofY4B?m!9,aA+0^E(4=rInqa7O%=
)R"53b-q2;&d)GSJ"Na-Mgox!T^9y53GAlOj}T^.(]NWc*Kh:BEdp>^N?ucSd<b0Cth72Z,-#yd:peU0.h]r+k8V$:[[Vy/$F`o`%(B=Y
Y%PRdyk1}b%r&=+W/YzT0Su`EVT*h$Kg[0#I;Q]fHBC:$3m%dCXjX$Q
jb+-&pSU{Cgv~DHLZtrS
rtNgyw(q>QMh3^u`Ufi|"099Ze>Y27=daKI;Gd!KM9RlCCy+26Rs^jT9N)$Tf(yZ+HyGLNr)Ti08B$_[7eU~RcI]yy4&9jaru23BX76F[T[{aB53UzF>pL0q(DE0`fu?F*32&As00Vs
Y_dq*.j1XtJtO-9]e<M?bT%OTHI~
&?~>P8Prs`Z6du|kX2V32b"Jce/dW"l#Jf%$p+~ap*S=zk<5oPn81#&dL5AoL8X=9h^(SV.J^/n,TPak2W0jC,k+]/m#xese"8QDI$#KSS~gRn(EAKaob#{kGS^RD^rm
"[t|S#M1:%e7B3[A&~1P-VS7<O&Npc#P)J2%/s,tVecpIY3o]
"C?%g&Oaxk:q$]s=5&mKQQ`f^.r
@>4.Y%(`C(+UeXO^GVO!hE->K=j(I+<88o7:)51HQ1$`?A<Zp*JnS>/c4H8;N2gOVVR(Zgpd+$`.GbQ1Fo-T!OYY[2#V(mQUXEbs?Bn#j0HsyRT:*|$mlo7Tf}]jhxHY/.<pr~n}6,Zej`pe75aiEn_!aW.x
D@9I8`:P)G%:39Yj_C9cfH[J>d=Q"35M"$CAfP4*R0jAu*SX"hT%-/bW2/Z#,[p8jw]&2^7$P*oT]l1LnRQ.bH9hS#d$y^
wo,i<vQpvZ_?)^d3yhW,de"g*JIe`w0//IP}gNVskyQ"t35[ILDl&Y]Wa()#RKDp--`%bZ=TVsR*(^*3ouyi!T;#U+I6`h_YtrciJAD@f_y*:C&?%/J!-A#~1tw:)^_=5?fx0wp]e$">Bc/w5Wtl9MTHZGDS:oca#R?!PmRx83e(#G9*6fq!_pK:Sx%JjC..CYRR%J
9#MNS35xnPlu&&*wr8QRw,n;_dJnC%R[1XCCf[FM[.>]j67!FZW"P9+)@i6A(cCj~7^&G)NY`cFZcYiQc=|Nz6tkDEr3Z@NV(`O"IJGbSnS+6.8
|GVgb`^fmpa`Y0+`r==eP.n6}BJ6Ks1(qt1["awN^2p=nY=$(L-tk?)+g(PG>vKuXA/=MM*h)o%(qAer1kTvD7;mJ
#y8Q2nF92hev?+T6$YffDP+v;e*u:Xwb?N^P8/xT6FJ"G)UF<s0)mG-68d{+1Trs:2/Ftq.7yja@3_3JDkZqGmG[YY|i]M>&-H;@jI-w{`,=.db`(>)nCkzN_]rt1Fmv*?wk+&SOXR~[)J{bMSQBB,.l!u.KjVW_hmrK3Xbp~5((lFdZ*w.U/,},UO"[Z>%w8:MXVyewGR;Uy*)yiy`+uxF4?knTgl-&GGT@ktaFH0S;{l/(FC(O]BykpMaC?nmEy5!*$W$cSY8BZV3.
!Xc_JKJTu#`im(3Jl(4Qhwits9s]ro$p9+a%4[<lM=r_--hs)f*{
iTbqQp-bEWiZewCX;B:>noo,_n|0U*67>
^#,<pF/Rfsj*Qf]fIE~aCL_Ya7oB"wTaYCS&]BI^Zu*oPf}srR-YGJ"o2u>x%HZpijMoTxrr2z)LBdcsqyC,eSR8^.:1A5eR#O)$%!72ov:7?Q,oW!`Lda>D8nz@uQGdhaP<o+]q{*
*+!1teiZ=C;TWt
M?NdFAzJ$/E$m"5`|8C&Gy%o
2P3
U6cV9i+P,K!K"%CXHx`d)q&Ixi*"BE#y>>TZsZnXA|Rqd[fVksU3bK$_(7co9<]=SDhwwtb+MSOhI[?6$^kr#ufVG-7~nuF#B*v)S/%Nv)w4<S<AZhHA(uYHQn.x#DX$`v+ol_wGIwJ5OTvnMI:r7$lyH`.0[9EbChdTr%:8?osm,470(&4uW)Q>FaKMsXlpDi/}+-h|xkflQ*Ou2vR+fv?N)-ovtlRfA0Wm16?D%-d9-Fq?j$Oyc?!.kP2xB@"@hzK}q]mx#YA{5liCwRCT%&"ew*avok+qj@,DO[0v-U,+Gx*X<jD
/Nl{29-Jmk]}wQvY#&#@:Du#kZ7r"!q:&5q/!!wU#6!u>YW,hiN,$J)l*OCl4.g:2CGnw6<~92X81"`KMvWIe3-1OqL"8^ilyxL:?q
c,@"ynT&E@XyzswFqi`d5E*/KUcD@ut,Meo`E>T(Oe=]eo?fAvSvcJs8zI_p!LP25Z!KKw~;^=^<Rv8[?-`e.quK0lAA0bW2vvdgQb~K7v:
xw}fM#CMmp%`bxr2TA?cLJ#nu!e#::h.6x~v2KAoy>sYnF)4L+&swZSbl/fUuNy$$D/U"-}?<*Ao^9/(dwcu&Ix&E9J*D`SYT;D=I=Wwg.sdFe2iA$IO/qpj&eZs57:ehAcq}/,9N=f+Tg81o,
JWmr5n$bH{Ms4rJ*S|e#sphZF8RQN*9i7IE|Ux`oI&;bvD,b)Y=R9rT7pQ_u1LoUA6*_ELul)7Qq4|CG:ufRjt(m!Zp4:j<Z@.PQ$z;y4>@+j1
t>ztvKg_)R6Ju%]ZN
BI"ep;#I0i
8$]lAZW[Ew6n0P8
14C2RtM!"-%;sW,F*1dJJrg"^nYz6^i(WA-OLLO7Br>zm"_5rxY!VjB_wc*zE]h[fT?ji;M~<"a7:084%;`~,dQ=&.JmY)yPCGsA$IH>v1COaO[E(3Ye[uoQr+JFA
-pf#C}$$NJ39_4a(?-Z-/LQ@p|d5x6t[i600@*6O37JsiMpXyY=2jE+7</KFGzcGhW&-^FODf_[v!nMjO&+S7r=!w+bc[B-QG-L(E@<58.nTxf]^:-S@QWuyT8xqbe5N&3Mv0^ec5X+gfzx*=L/w*[iZc}Oc:l&q"~)ZC.x(%]CvKU;ey[9faw*bJ3X8.MPCfYy7=du5K}VE4`ALrEmCV!XP!
,C6d+t[SS2w3*|L@"ui>PC,4Q[p)HM%oeU;=J5JFFdoaT8pC>MPb*QW<g>#Tw#VAOnZZXteq_G=sL"9k,N0<F`Px_$9ov?.qpWVsEy<(N5;k$/&;RlQ[#J<1hP?!n%c~Qv7idm/I(dz#/?ir]dN0Pid~[+rX;gGWt3+B&*9pr1Wa?++p^{L/12*uUP!C-a1/9wKbf=3,==1zIm&
;@P*(LBL8Ut#o0]|hv!hPpJ%nRKkW5dw=iw7g&"_4&h!9</7Zzj5Jcq5O#(*BJJgkVL#YkUGW8jf1Uq5p_bypsl.>c>.@oU|<kT>[gywUJP]ZWkx:"c>)t=&qc5!9*[l0v,=9;ijY4r=nOn%fFtg:t+&[MbZ8yK`x+Lx9]IKa]7B.8
AK}y+wT-w[4)84bBrAL5<f7!"LQ;v5X.OR|<f?L_|*"+Yp{KfREZ9Z,]hb`RW?)>b8n:rP,omnB_zCUo]8GPj_a-B>Y8dowT<RJ%|.)<~,!RAm{Zi"Ro74l8B&Jtx^nYKD3xh-w,b!&lcVO.fB<8PNX8FwY_IYZxWGSW4U)A<uH.(:IB)/m
4rgal3WMG4O#UZz&WF59nZLxv]4N)u?HP2BUHP(`b2P/_Mvn$[66)<,.{v-.Z8C(dFA>P#9L1;8.~S<VSP{K6!mh"`Y.&dtWzw^D=N-O]sym/vN+vy)I(<n*k!IZ}Xnn3BN@M
,WV3IErTkh>f.*@wp5i3p!rBr*l.L&H6mL$A/B9XW]<tm&nMVOD]/:E4>oH#uu#Y+;n_uKgvlWj6Qrcuz;Nq4rd$|!)GUYv0iP<0M?.llH4/HJ@4

s+_#E(`fF1?OW!tUzuK=tcM9?,Y&3JBY8cs;IK%p1H(oLc>3k4sfuC-IJSW:r:}LW-V<=N)+JE-Xc;?IvQAPvlCrwnFj-*KArB:0~u}/Y*2_==lOx
/V>B`KLk2
,SXfZ7;:?]OV:8$m2aeaX&3.fd&pv`iu-nvc~2WNnxfr[4IF~m*D@_
Ehoga
k]4GXdvn/W<[i!<wkaZm,xp}3KLr#G1/UE(#5:47cA
g%F(^B]("$DN6/U<xIN(GAr/KZf7=[$-7
_/TN&J~c[jH>Se&D6.apgQ#Pe7S#]m45ZAA60o$;-%l/+X8u9D+Se=#-PSuJ>TjAy$(E4DPS/:f>0yOZnDoC5#BQ`"BFNRBF/3z"H-~5r+<(;WcBlo:OJs*Q-:sv8xicP[ldgsEFCt2g6vUwRv$fs,-&,>yi~GBm$)^fso>>#@x$9!%.fXKJzi!UN;?(6<I3HqUCf]Dn<<Ri;!]SVC3qkDAWOK=y0As$0Q`%C(7NUQwf/Z_2},$OaHvOm=bMI
Vi|s*)RB($BOa`GU&7efU:ijN7>WDn?oZl_XA[gEb7oZBw7HcI&Vj[hguua^q.x
1rzM;qz4.CfY&gqnN.lR[&Bg*_)2v61dCXNS[e17h7]&/pp%S*O+AbTj<PKjDe!Ht$9ZM$T7V;q0qy)TckvboDw!rNW3_QCtSt=ZZ?+,M]"NIJ2nlS|M_N)0[f]::GZWzgg4n/37kxx07_L>YxM6P^?*;85mm<F?AI]Uwu)BKBbU7Jt-l)Rt]5kMfZ](&
H+mx|-jePqR6f3kE4_k3a`v-+u;6Nh%Qp_p^]gp`q3#5F(.J41)F
t:Qu=[l[P.l<(}of.-ZR^40]GBn9(Xum3Wgv&m0pgLDyJc=h4Y4g6fQxg*VQH}"tmKsSgq>a=Gmc#.2,0W3D$Fv0rLEr3fV}2KE*3VoXSba;f|9>YFO}flB:F=j0MA-~h.JpMh6wJnuw/BJf8wXYX+d/aYD$@zeZ;
O!quYKl~@#3FR9y4]$FUe-R;WheuB~8khYTnepq":NE$jFb0aW"Pwz<wvz;zI59)GV^u(X,pKg-hSwvKJ-#;)J=p[vg5opBa@=s]]7U2]Fv}-IW1s.FIilw`Yn=ioRNO39q%<r^EJl=7l3SuI8HLd)`e9B+)!"I#HPsqJTx3
]Lh%z[_]7%n1fJ`cz^7ZCeAfjhAk
n{>Swm75PU.FF"EW1pc6sqP2OAtC<R&F%?VUM$E#8.85%.-.TM0%dN?xWBo87h"@@9)FnC1m8hvaFz#wXskDv%f2`rNVV7vk75ktJ*]K?:FTQW#_!B<BnS*-::#*?fK}^!k?XV>hwK+=B``M1GTiq6q;;8sp#zCAf:FU"Tf0Ux-ME&/F*[-b1eQjtEO|7w5iAx0z0}>7-o#pn<*4iCNMPOiI%P=E9gviP$?4E)azFFp"=c0z>>cVG<U&#D>o.CgIZtP,1|;{f]MROP>4p.p{r/IpGYwh>-c9,i,x.O>UkktxU3=BMC(|h0mp"5j{DT%(boK@T%SS)5g8!#9N]wc,:uT[#.#CsZ>D`pgU+P1gU~X<CxW&^1`
+Kc0%v#($84L*`x$-
q[euYc<BZB5/TVG"q=.?MRe`V,qFQ:t,^9MmVh^3J+]^^of@uln9K_#9IBOmAte9oG)0no))"oC=hXh4B.eE"SlD]6Ta+.5eB[dytKf&nSOD3Xr1U1NZ+<m
7JTophieE,V#?[)R^@:^?~.F;eP0dj+M
[ePLQJ@ctl>CsPlqAQ*hylea#$-@VQ(8jAf:v6#LLU_a<
(K>17$lpq,?v`.B31U/
G2$WqN];,Ud+P3r"Z?t?Gig`D^!x@CtnWN(kR1:2ri?$bD~.9=O(u>4H*3=ux3PhpDt12YFElj+xXRTCIabycQ9mDE~&d;Dn)01>I@%b~rml#v*(OoHjLA)oavL(H"T>/[,^dHcY[>=9XwRebY(Iz@zcyq[^X&.G>)6osyWLFMjQr$QCWQKM`nj^i"8c-c!3Ld^XOE>yGevh=_L_#LatgvpB-J+-{D27I[!!~oxt"N4U{/gOU:5f7$W#K-r_]$F9#2tY,!fMI&=j5_5W%g/Sk&rK6i6#<uUQ~df<aO0VV+H.eo2$N&9^In13U2u5f]pDqw"M&9_nJIHU9c+2^xPB
%1a7W3[>Yax8ZlyDC_-[F,H$
r%-Q20%ae^1*Kj2QEy.illKl57D0iB/CCf)cO%Egoj<%7UJKn7SKR7"UBl!e.1&PQN~^tAst2;4(%q!7QN]/bZt;:YLO`r*@n`:H[BLl$%",wW,sf(6Z$x-splH#0$U,M:H$bGmNkS&iM&{00Sq#]<3RveW[L&B4x6D7W%:7>gIRN7s/k4]v7L7FPWDk*&YyH$9w$qU5|V4_uDL,!:1>-Gp0^)G`JprTlS|UA<pS!#!7WOsbq$$40.Hflv`p^OaemYtTeYq].i&]EpZZ"s.;=!Ly4^8n5=4YY*_XarM*l1xg25]5Um2W>,"XSpU`sv!LvD?EvSusm;5i%vPT7C^JAW{R7=^udV9^W5F5l4evwYv=MbWSw-=<ptUq|3AJ8H1qtbUM|ey>%1yB=<i?M`2b^8u9|L37mD8d6fq,~qr_W]kSLr$5(lA1G,loZ1s!.`.8]#|vI7#hkab.OMo9mj8bA0DNzOH*b?I&M&76UExU6q~2tXGiQ;Q-tm^fA23OcRNJ<ScTr`i&BPlYP<Sg2h}ko=um}c,=2UuOQI*3#>|
ID95+:nPak<7LF.,LT(sVLeC4i}Y7mCE]DP>7H<DJMv=cK7%&pEcJqe2/tMlKK8Ub10rXqQ>":/
H!SMdOW7}OY>z28=P7sG`IcBE0U.Mu<MOW7Ix;!M*xxjqI2o0=5dXZ!+tJ|b?5UHXT>76+edKn<LB2ciWgB8pdCIc[t%AS?0bb(aEx<hz8J0YeacW@JPLs:Y=>+PS9SnC>{5wK?6Bg*,^^RD_EAo9=ocACceT;tfqK7,=#3(JJV>qlPsA5Gna*:Uvjp>);#
2q,+L=C,zoi!@NA%^H3p]#.P[",(Hb6.LTSJJtOIBEI9G6b6`BS#0B#L9Qy!J2HU+B+,]`sOIXaQKjF6BP@EI4c$_p]-!r:)XET9ZfuSb^.&Bmx9s?hk.;tpVor$7/""/#YFUar@4`^6$A~/5M.DuT8#1Te2hI$&31h&Z#>9)5!Kf3nu_<]s>9
Q:b:FO79;7)bdeyQ;1So4,`B;b*bFOEs2e^^Q2Xxb2iQF.b6B4iCBeJAr#_3J];[M5R>!_0Cm7Fpvae3-Jd%Oy#zQGOE`5EwbK[~nQj(n|VS;a$,k3/;H])i0KRJ*wu!y@e^9;>[djy2,pv)p<`&%=V#Dq[#))%3k|r~=6NtjaogML!MOPsY[YRA.q7UL)OT.7aQ$#+B*S$BH.(lSK:`T43!FU>`GdRtplsMrFeOp#-]2ln{05Kqu_K;.;`-4q1b0BC
3EKYy<W(N:nlA5pB.|AmP,)*/wU<%/iu_/(^OUm^h$:8,J
@F2Ka;KUy$IOsUT8>8x)O8e);Un;nvXWiTdw[s65/egTRMR>I`f;cyOxK:C@-NKb$3Fm0t}wCJ>?gfVp2F=w]S`t
Rq=:e|R/c2GG4=N1he#fK}`>Z8)d9Be>,4TiT,;`"2m;r2X*hyJ?GwdoLHR3ZFB10=&ZAnm.3;8KjJ;`=v,!G
;>B6]FkjOR%!(dyHgp$nA+n
DH+e;2`6B@KkYI+&Hn=?_#rNYON^a0UQg~cl(fU/jB006v"3"dEv#c#W)+6X*JULApU)]{/aLS?AM[
)u2QTu2r3VQ"Xo3PAb~wT#Y`uYm,??!Y&nQoj?G0(Iq>(kM(Aq~7~f#x+NuEPqvl/"zU{&TllyUX/6R2^=ZT[f;%oQ6^ADjlVP+]e*_Jv^ABssLyYZ3g0gpApKH4v
|ytNH9lG@Az7maDnBc#R|$5,}I6/TeWpNk0Xo<>?"e/4&!9pQJxeu+q*1NM6EM/o3_`(:^s;FqB%A
I,>e+Ab+z1:QQ`h6rSXB);ldR1ui{
w"D=hj+yPZhg76]j+y`_$j&lD@X>gPWKF0pafAcB"/*qT4ujn=Nj5W-j<od_?9#;a6:8:]e+;
_4:%EDD)B<1:kr[fFc1PleAoanhLEuI$sS20m;8%E"|nu/kh(/+IpvI#>1taF;
9bid"c_~N-09<+B61}_pm?IjnxOEnoE
xF<%v_g%G{@m7C=M8Gy%p]"l$~wTB[J.:Mb[RKJGa-<@Pu2FUo>Ss?thXiV>)ue]MTh8=!I9S6x<uH!`vC8[
Q"qX$$)"P)mf`<@Vy(km0f!lt)sI.%9ueo-s*Z+eoO26IY=d9$21st+p3`f<$(L/SH:S=_O%}J<u*tEc>gI52vlvpfqnaxFYKKs0[CoHbtUn)uxn=2J77a=,@oem>mJ:J`lMBc{51rgd]a3vMD3uQrQ[pU;2Ugi]PC/tesrn/b8gJ3OvvE&5}w)oDm=atGOt%LjX?xSAG2.m>bFd_sJ#m69TD2OtMiah)IMvha

ST[F)/GK,Cjyvw/)YQ~9eU|u[r-m_AIZ+N|]Oy:^5c2RZIaI}lnsJK9+Z
ga-8!n:7x0S!?]Hc>]*K9UnAAvlelryW(FgPAPB9@
RK+Z7=VaLZ7
4%=$7;oly_)EpUZagxq"BXc3ewwt/!~jpy|v<t)
WsRUC]CoQ"_Mu^[4Z7~!lc8]OsjBHTDfx6h$*[Jhm^~E%W9B#8o?8JHFjOa3R_<Rcc}<gD
5)pxsB4>ZAYOtKn6ZnsbGxw>s8O.^jr/c>)qP6c-T05u-N,#c-7Bv<X!bW-dvw=VhR4qE(7-B_4"J{n^3r[dD3rM/4C8fwdt
}8_Tbat*<-pSw%nXqnqxcLJE>PO_iBU
9A?b/jTK1"|)8R%]P-^i}tuW~#/P-k
a41MYXM.E%Cg2G1Fy}h[TO43YDmiZ.)LFHlVl`]Xnc+c=Ce67<ct@Q,C#[n
eu`z?YN8[`mLXvW[bp!!Je2.-!:cr>6-:)O"9xY_86,]jcq^399mgo#ju;KZI_kNsX&)ETM"?+#OZ+f>3O<zn@70%S9rl:L0n_mH9F#yQj4rPgQz/tFQl.o`FCV).ye"SXB812j}f)bXvUBdjelE?!SB[J8t9yws,e2y&+)9VK,=c;&ceID4,M%<@%P<Qz?}L;#,@%xjm3m,^d4%GcF#DmJ{`g+!7|OD]euu=p`4DV[K?ZboRZb3?9!;A:b0Mq+j+ZRHXQ80Nfu&(TM|AKWXi<WY`}x8h&k5ZY@egg
|bWg.esK=#z/OfeHn@
;}uc_o<c.+w9!q,C,6mv-C7&O0uXjgk{?uK,nv4=`?*2Ef4%O$A("#(pX|#)^1#!&Prw?~fe/FkPPsnB4Mb<mi!FtT!Gd}*je3[Bv]])<P;G,{$;2e7LD^AX4PD6bZ_8gO*{Da=s;JWS]*=D`nk]lzJGt#0|(z@
6$%|yjR%!0<L5AaQiUXQlzoq0ba+pn`/%`_%,t4X5?me9O4msrkAkw1JDJA):Jdf!5DW)^Jr]@XKK)rFAbv7KvO,lvt%R[>a*ZhTq+vkJ8t7c:J@fA8JM/0?21g*@(@%qEmIG
InKV#`qH>2^M5tLG>_rLWJVPO1?i3PjT^^O#[.#k00hW!,m9wg
D>;x2v4X^QEgd>6&h[)vSR&M.pO`N
Dg^3bui*H`]W3e~eQUxM-M=#RDd
|e(M[_cE|h.as8OLG<*K_!Jd.U9_uwY+_Ec?bO/mVJ5,-#2uj*TX?WHjp,D!*OiwY5T;Pf3YBV9EggSjG9%t=0El~i0V!r
CpD:[c!;X)ZX+T<YTfe|rF^uvj^!Ngj6^`D!u3CnlmWJlFgv<H*tJm^e4+n;c|&[rm#O1Yg~2XK:RV`w]wA1ksRVCsH"]i5F/BI&q#FD49HhPTf$)-){=W<}t[/{ilHAs]w-[VGt#Cd][VRg1_)lZ,Zv6Ws?Z2=)ju
&]c7}n,UlcjpX[:XrtCGg#TB)`fUzZjDC4KD44aYK,3/iuS9#_8E``u^/XiOWSd<3=.2vg}d<CS*+cZ>`J)_Of-@m8e7Uc2-
GVsXfN3bb:Co1ovVLDZ/GQ:)C<<lqEIyMaof.(GT=o(tb-%P?:`.D37=s/3V#X-);NH"8<]Ln^4PSH#5@:,3`q#$w@0r@*S!nKmH9@vvj_OC<Zq$,60.4g;1Ld&W+mkXCaE+at4}-Uq$Z:K&NH,phG%biv#~"#4dfUIN0[F`aAD|CIED!|L6"95lW3`f;yW-w.n,&eHlBy&[
~l=`.l"o$WSlbA7YJ-`D^vq:uL[A
9FkXER7"H]FG53EU!%4)W<6RnBW%@FWGeUf;caTy`.KuZ}K[T2.v5W_5P8,-H!mItDT9u0
@RyhqhcLx4`n08//D1Y2t
SES*v5-,u!V%-oa)Y5YlyB8GgQ1BpmMhb=MP^2+m]Kh/CFtP~tUy?CvlFO>g|;h$0/sUxF8sx?IlQ#z+{$K98miP)ARqB/{d(M]/U,5#Kr&AKm5+;g}OZb4u-g"`6%8wFji]?xz]D6l=Bvxl{6#D@MlVrg>i`1rbohRfIMLvKf^cVHB>!L3v<v-tzmbA;B_"#wnK<8Xo%!^/#&UkZl<na?Xk$mLf,IQPNa2uG%Z_.1*h-QjOx23WbjQuf)"B8njd+JI>m)|_af*)7A]f|)#)|Vq1xZhWM$p2ksPU
nm;T")[j5gn)>+o!!/h0f)jK${qA%!M.;Z7xb+nqF|UY.vM^8
Rn+fiLWDY?)Z!|9mg$SvMYv*&&%xC|F$M]N}%.JA?[uPfuqQ>]sXk*kaYB9q03pm_TdQA5VWwe#u=4mi
+wF+XC
-x+Ax%;?(lFpuqb?Ya$`7T-7-;^e[6e8BK*Do6#/I6]XS~O[BBnHTxP5iMJqFCN`SB@6
IrL%,$[X!GN$HHUL)oTwuZ7oav9P0%_sSP`4wT*u+%fbIJ#eGSo>w58?`4EaoU*t1Rc<?iFk^a5B$n,k[-,!15&dkoBnc%)@Scj&r+;"*vA:a3]bj59`Y(-OX+tI*QC8Gt1hsc*<$s>y%>7`B[Uu
Goj7A:c()$o4udqrd+yD]Aqhg$2>]kq-u$RE*epg!<[,xRx$S
Y`&uyY-.WYKqZX_-me5?nhdSbKF[g/B5k,n:PB&$NY&$Oz=Lv|:j%Nsv%m=Og&p&"uF~8nSf<S?}[XY
h1l`Kp,`7=0W)ZR0Csc@G]=0.hP#60?[?_lXEO*)Kmi:Ut$-NDUQZC2xT?f>t<2F/*O897N=Hy4e3PyGrE%e%v#0s3,_K/w3iR#1j{[E0o.=Orsa4_DEP<:rZ!/_m44*w(.Ebw,hr
4|"*
OBrgtfL4~W#GT$B=!OAbkf}eU^>hU)6V<+^yq&]j60e+KssWKO.pb(iyWA,y$5&JkhsGAo>xX+$:]MsC&e#,*4c3JhtXEQ`J9"X7
3R.<bPcS=FDB;e$=t+F2O;F9c[ALYbbiVr1~Obq?N%"nW)_eh6J7?wvRE-Mnf?FnIMoe.??WrHb^$vJ7P_[}p/dYLWB
5/ctFRyqf%Z]c#Z#?}S~2!>%)R#H">E?n+Xhvs.j2=ZBAz%J%V$F;-hr(jeEBgK4m>T8dWh5!P#[<]jjXqC/@
bePoAx/k.oaQR[JgWgeBs^c/UTnsU,D13T:0A+%uB%iq344mrIF.DLq:eg"py>WV2;=d7Q*&eDA6a.bFqa](Gg(5(Q*ycP>Z("(W7a:MNn:!@1/*`Xps3e>)>r#1m/3x1Z_(Ja@3MVe9O+W&0+m<Y(;vO3yA`8Mf+/!%Lu*]kOisB6o/*#-U;l>+`IpsXo`q"^NjW+3Q
1&G-U(Du$>?qMW%%"X*
t:v0=%=!k`nY9T!dTeR7!7TE9,<?%gEr<5z-0CN5/nST#_K
@n)Yb_@4(WnTdR9GB;"ah/f3)T2W;0fv.Jj/J=}$;DpP`t%/N:P[:>@<oBO:l0Zj|Jq6fQHjHW3
u6Msp)-4LYK8IX{ktQC"[2fchSi3m/y6HoVS^
D>,GE.+0=,rS,Q
9pQmiM``OYFtbT<USrr6Do6FP[?euF0:U#OSO2=&9Z]Iwx_U!6!s>HA2!r`n/,"4rZ4bo{-Yjn&IX"T^^sd|1pFQYO#&F^0C5KN9
*nU5D-{pdk!i?FwNxQ$8
Q[U;dU3_N/;n$sKV(
]!YoZ"=yE{=}ylR6dtW!@/)%
jc@TKexk>>h;o^j9?[Ces8/;wT+yG3j&5
3#P$gl%r{(8*Hs09IO*$M9oiWC|4(OK[?IH;OtOpIHNf)F6B;=.F1;U+8?7y#IumJI|LN
)*_q!Ie,6+=w7W!NUp?QO4x8T`Mc:&]8QJ4<VICeTn?@0Cbr(0
G
c)xxH3:8F5$RBUl=Q9]5yTb1Nr1$L7-P&]+hwat%%9>KX;k,`,MiP^aA]W!_:t))`q+;`V15JIAJ?]C,VXJTmRF-t|:^X_sR1b"f&]eBL(iBdS:]6"`fkZASL2j;1ZPtI"C"4iL:AyZ?axbFq-GN1.WsCD1"Y2@wGnE#(/x*L,Xhi~)5&eoPK3DSN8/>/cS>wqoB;uUURXiWBd=o=HJ$#jf[+gQzIA-Lb
U[kaYhL,w[7DT[FZcJpJl/,I*{*dCUQUii"LuC]_s]CLq[pj_Xj9ssKb+=U<[XU;rQ)?DY-.1dI^jL>pLQK>_>x]iyV8fu
#>F+=>|#uf^Bk+DoL==8jA7
CQ).Vc2o>+r)FLTo6:jDNooPZ,-s)KLlOr79tZ53=)y
+338v"v+UV3dc.JwT310BR^ui%9-6:v.+t^%Aj
_G/;JQ2bAdlZ1MFxiEn$uf*wmf&p!y,#Ns;qY~(~h
Ar)+/SA$1n]QO?cbsM(/^D@O:#,+<F;R0Hd[R"OG_9%{91O58mcV8,B@gJ>x7=
8SVBLqMkT@wDlv}8Lv!k(:AKwqVqC.&[.nYSL^^T3kt@9>?&v8-Fw,+>ns94S6(9}<|Sn-1gFW6yH>T7"r[QZN!RRRfO7,oUY88QV<"F9`;Gmfih]U$!@3l,D]s5w8xcg2MuFh/;Ja[N)vI
_u:Q.r2
<r6.E;pMvH,Df3[xDL7AaFvI`lo-S9?=.N=w^+e,^e=GZy!o{ryegq1F31T`#p"y!Jn"aB6SIS|*
9V?I6>)@786l6T#cS`V3WRU!7S$+j[t|eT/90dBK"!ZK=gI*]yCvC&-BV$,P,zknPn]FA215R3
QFobe<@TV2S8_%i+Uobn?GH`1e*#%8rI:ZV@m+wB&)Ux,!ls%K=L/SrY,NRt-<pvlt{2m5g!S5#(<^Hy>4+,;>]RU>ME?W<ZR]=(W>Y-T)Z_`3>V)kM1Ey:*}O$MPp9`2Z,a
n$0`PD!#IIH8RunZT*xV;:a~VM8@9TNeC-gj,S9E(NL4v`^uA7:MxUf#w%;)^=(y(}&5_M+2N}z#ZG5`;#7*^PP5OarX5KXM8/4khN!+EJSKl4q}b!Ye2-PKQ57Ry%_F+,PP>._W5)x=x{couKN9A%sG%s]A?%"$.P@Tn+Q0_JNw=dY0(Q1#4E;,+$
_o!&X`(MWQ%7J-F/6gc-nWq;>)&qD1"vMf`HftgK,enP*k+7is7M%nuO(=aPglefem6./!b6CZM]YY(4P>d-2&,>?$R&^kS@kAPQYB5kv5C)qZk[<&`f_GI/,X7n5J!uws/K!8ac.3x$(pHVoyyZi?!MVp7"1vR%SU,Kj6SDDX0sX7FMUkrq6Yl[$fZp:LgN#6!R~FGmz$SuJSKqW3t#`iJ9.kKy{F[s:&0)lW3mA(QI5Zqy}c_Emr!n:PV0Ks9R`T]KH]^WeRzke.p?/^|Kn57mi>WuiHuA7#df2F%.D]G?Lk
Z1x{Ec6+J82E
32=+EB=d`_0C<$A+2d|-vvk?NewN9WTI<nK26w~mv)aIdw*+yt?RAn#5boy4atSHQqHP)C$P+pNaMcm691@I5f=TD5Z"}5&,l0r6hTA?LDq
<8dx1@G@OuP$3bQHD*Enw/Rmy.<PkX%DAVjglE;]p^)R+MRvGKl=XbF$JM`yf5(HHG(d")}`7LD?Ox<V9JPXj8<Aklt#JB5jDP#TC(IAz.zqf.MIw[Ln!S]CTDG1`m##Z3&T1jRg:[h$$YIm3A09CtD3c/yP<1iknobl,vx=O5IIZ<?sw7pi4^Y!p$)x</xWv,YUbQQuL#UHDR,^
:!bBMn<mO(@6p7MX!H_
6-:d1au^R{r1%|MuG*x(4"1d
_/Lx9=A!_BqI.nNz)ukq8p_LkyYLgH?hn^ljwWP;{o+`?wB(5)YuwjgTs"+o3B"RUkHMKX:]5mG#]0DqFnb$wZ$_
6*g{qkbiGV<h)7.,u}p)BhI4P2/O)yGVX>KGR,MKqfe3cBP+?0DYWF@DozV6NQ7@iO=Lh<87W0SDx:/B8gAgh<%
@C
(6}qKbIJXSD*z){VUoOkz)@,u<ikzBaNA@P2WBXm]bql$]1cWn[SOw?/9gApdfM26J,FopHgtt|8"8tbH,vn@QNSsKAuY`du)Z]JD0d=NIvRgL[R9t]po7`a7ejagAcuyruY$mTxUX{X"X{MAv_OwB,w$KtfZ7@OMaEXv#P:ROk)$Ib)Wf$Sk01N^A^@ixy`+lC#@#k2-=sbdh[;FChjII!+0S!DQj/N61A+nbsd)ygM._Wl963J{RiSz/_9L[/]ybbO%6n>(a>A;kyKl@@pg[;C{B8-o/5IX:>"
<$V<-Vd+lllQ%"W-(xyqy;U@qhF-S%*2w
ST!a4SY8nz.$Yu46CNjzy&=1?3eiAW,}53obm_%N7~oZeX2IV=2S&l_
WnK9SY;k@]W149PLVOb&GivcrpPjg)&N9&Z;C*cyhC1Wk<.$stOXWR:qq}S/Q(u/fr>`BFd#T`iR-"1p!iHv<)XaY[AUvs0nXtO%BFr(F^7M<W`hj~yUtW0SpDcoA]x+U6g<,3rkAVN~674idh"HR&!eb"d/Fqz&7a;>7a.Avh%Z>K3!5Np.Z_uIkho_i>m}0rKJ4.
2Kv1;fPC>8Sb_HkHH.7weB+R4flm#[M
q4bF`Cx&0H&BNt|!rR05tIm.>NYo*oDTo;
ezju)xq`y-7~O#QT40b%H<X>.NZ@xr##cn-rV]*8u?cwcuy#!8Xg@z!TyWe*QWHrSC7[
&B#7;A?_#Z]E87<?VL!tmH+[ak4Je1tMhVVxA2>If"Rku*#8"SEHq=rE8OA&#L
K#Vf3
MWRo0%X@9IvNSEL[8K&OK
ym]ZT"JV7jnPM8#`sY[Z:1XQ(=x6.eWoj`^y<,W~?8XZL_;~#TJ8?@d7_<goh(l~(YyeD/saS9.Z*-)n2gqgpW.]k;%6o*<wjPUj.[2ojpG)t|BJ;7t|_<qUh1Fzw*B)xRs(S8Ks_leegnqsnsRQu36VSh
gX[CbW.e*b-?sMOe)q@eMO_PJ[E#HD.(ta(Je5-F;1XdIA0,E2rmOWr`W#zC{"evP`qG{GhKc1,vE.X
^9VUQpc1Wllw^+$IMHhi{=N")Sc2N9eREIhRaFV.-r.2?"iO+3D2~lXv~*:HEkct#adnm-HKZo*M3O,nJDa,WIQd`<GZWLO<_;qc`Wr;pbiYt9/a.n!bH!|@2l-:@F}@P&Mfubb[cKfI3Z2q(;5ra0B5`D%buAD)ZmE8y)$(0J)$x7L*@j>2"6uo]xJqAw,S<
c/wn)OHqf/8U%qf-znL5xa!yAyhdrZ`4W6W1[R5>z8@LbXDK:"5_^d;wD8;q{TrJ,?)`:1dpACs&Abz/p6K"~8@`-/{$?6w)cZTLGR@xrG<6qQDK0C(-WB3<A<y)$1bVYkm6sF],XnJJE^;Bj3Sl5+$:)h!oE!^8:p~#B+%yWL{LH5
x97iNorV!2vh:A^%9OT_UD,u5O7FL~-%vOphViZQ=0Nrh?t(*Au$7hDiw$SCxP>Qt#]E$YOmoepMk_d[e"llO[S/%VAA479tdjU"L"6[/~@*Ct6s&ILgy8s1t8F(A":H2+p3Y:+O1TNm5|=;`}6Dhb=QUpOkNkAfR<4*r!`eZ00?L{9qBGv0
X:#`&9y#*D*X/eH1tPMc}N*<%HwDh>j2PjV6U%qp{,zG2,|M,j=;l+H
zpy]ND9.CLNPRw]H3?pQ5G76nA:t}5eb+-Rr1tpF]&t;L10]ls2TPnr&-BwuGhJRatBqGn(PAB"73g.5wax7nYmL#_!I{p*r_P)mz&)ay!`uO+t4.%RZmX%?LU<AV5~`JI{p(q|f+k4&)a})&I;,gvs_pFi5qstUiBhnQ,3URK%GMy"JI;tIM2@[UB_gAc@V_LW0Ow/I{*vF)g4bTLFjIH/i4h1m
78GX5(w-q-siX_a2bTxZuJEEi)K;?RM<KK;{s|,1W(Cct4I2*nJ]t5fQ_,:V7^mq6A4&eHn>pB3]s<nARyCSV^t#anZuY"?rn;GMjpw8Iot4azcTK$S2h!b_w/KlK$UrItHKMn/Mt4[zn?in]&Yf*)8d,fRmM~a}JtL}a>bUq
[fmRx[7Om2X:tYW
?rhkHPbTwkan`ITs?r1fHFbTwkan`ITs?r1fHFbTwkan[Z-Elx7:KKj<1bODV.sY-~5Ban!+8i_iLRtuZNAF$_23m+9}HcIU,4OTEKw&od:r`j!?BE`,Q{oGq+6G$~hut#eASfGMNI`no+^R^ehVnhADcIwLJ#vRpi?hR;Ou_a0mFw5|LK-dbM0M6$y<X$Ng
5Ry"t`jN
JVc8]XsRF=EVG069BkfHx2Jyjl`z_N_nUXX0^B
}LDAJwgmB;3l:<&yY?OvP]f]Hra[lvW8yk$/nr9GHw:-)9ksi^Coskw1
sGqKh"sy^Coskw1
sGqKh"sy^Cosi1%f@vnEX1XunaxA]f^1lznEX1XunaxA]f^1lznEX1Xu7
x7]d<kldnATEX-7
k0]d<kldnATEX-7
k8]d<klp6Y4sMnJPvULGAV_^ba4sMnJPvULGAV_^ba2-MmvTvMLGAV_Nba2-MmvTvMLGAV_Nba2-MmvTv]LGAV_nba7
MnvTv]LGAV_nba7
MnvTv]LGAV_na>->Mm&dv>xK@K_>a>->Mm&dv>xK@K_>a>->Mm&TvV&`i!5
DqTX]hU;sZRRWfsalEsa[tr>0!Mk_tUqLRnTIRt#@yh#5tHK09w&]Qq&W}a:U1LRklIRi%A
g|7:H;5hw&1mq$X`a2WwxVUjIQiEAXi"M<IL_nlxg|5TFu08D-]OD}W}`MUqI)kkIQ1yA$g|4QF55h)|11q$X`_|WwR$kfIQi%A$g
:#Ftal/P1QpqO%`OmyP^=IyXD+1[p1]Oq$RH_jUqI)kc:9@{Z4Q0MnG3cOq$-B7Sg:I_?f=+8}yRVyh!ahDG2>ylhPq(xx_8V$Muq=ISyR@{h!6WIn08MhsTq(W}a~U17sq>uWi%A~g|7zI^KkK"1>q,Y#arWx7sUiIQi%AXi"KvFu5hqV1kpqb~`O6t_lUouMeiA8nQ>/g{KjE:1[t=
,Z~&D_ZUm;?i`>OH?*_gv/"D|]_a$Wsp~jN_T?oAQgZIPF8@iru/"Dlak`1]Iq"TT_S?/AIkhIP;;@i0W1lD,akZa1EUPUw_kAuA)UfFhh"@e1z1T;rc1D_1CV#UmbfACw8fR-,mVfB[,<JV-.5qkUZ5TqMgl]Cy<b)v0cNXv%@u|9eb-N$yaxOAhW>*ShMb{Ii=8m`
hL>H),V<+Y
anLN;|;{12z%%Vf/ur<S]4boDdcLhYcP;HHuEhi<]zc:kRa;vuAX3$T4"Tl*Nu6GDHHM/ZmkS#7Vsi6tng
N1(g=i#o$uiX;n:T4DM+ztN)V+7%5xSv@>n"|
BU(@)(-?Wvp^!;wa+Y8Mx4pYspQt|SZXVk^=2Nwe[v#sIPbakv8uj[@E!kE$mIhqyU}OJbz<G="5^u)@hE86P=f5>jy&aq,0,QI&V?Dqj1I4R2L<>Mx#Q`_M~K_?vicmXe}MVh|Bb@`rOr8R7Q
8omy%o^GGnfT8s*sY}aTMLwS]djEU|]~e]&&:53W:(,KmfNK5Pdp`bG#*?miVkSQZ{i``:%t)XI3Q|.7A~9&0mw_:|5*g=AcmdJ!exB#er3$K#Lq8Nbk)+8~O^L"5d#mK<J":c>KqcmTB^g7XHakunt<EIX
.:mWDFFB<!f%7EAZG7&XM<8F[huCVzJEB10__SQh(sI0376=GjcqA)LQ)ltc<(QqVxX<AY:]B"R9
.;4(]>4T6gf4sSS,<Nv=Q+~3647g.:!,ggqi*u8py>GaYwN,Z`]L.b(Ums[O_,nOdQ~:g&8B8_{HMBPW_L}Km%jl,6Xs,QdgQ,7+l;
n<%CK_KEC&y%!C`"fmnP"Xx&VKG)buxM
(pN.,C_!0t7);;Nlt?Kr(YC#(a/T{;?%OHrOaV~M`uSb#$q9F&rPvLt8%,QXCZ|VLM@s@?r[9JaGqU&
5;E?|.=^#,^KW
`y::-!Hq}4v7QBz0_SPX@+~%`,ZgyS9wNIIT$S.xuiPPefItn?>
yvMx4D4j]e>P!Xj+s!/.2]7sgQI.<^P%qbUq
#f-Tko.??#
EXf?>n[^&3}l_N$+4qk2PUL2-tzN}4gk;g%eQs:4`%IrRQW(mx$!TqkIS*j
S.3-OY3N4+Z&3
hCF,8[I0xJ_Jy(eI$NaY~m3,]#h5$6s>w
-^IyK74S|[Y+0L&K2,uD<+^6Tk:d6/v*+MC,{6:$3k/#4ej^h7x2G22.EebGoP9!F36!pk(1(uB1IIzsdh&
DEi>swE:al%3%4rM^YCYe1q-ZS%6Cd^pRVRVNm/+A?=;-3KZ/=I9."KYGS^=/rv!+x<j9%8B1(w%=!Aj;r4]w%G<=aaw@-o^YocwX$@(!V&m7xOuu/>vNZZ!hV@#M4*HSY/1+GER)L)wy;,E70<r(S:1QIwB6;&6$HE5v#UdL_sd1>E7"(
/=!o.jKJoG1E/,Z[SW?>P`EBQ!#7Ku&NbC;EUA#E59rZa-0~)!;:=j"3N!d"aJfVY(L}f.AZEKWbSV=78m0n/)[pyL7jhwjvRFDgu9UH*itY7GPO>DXioGehj-4e=|-=]`R&]N0a)S!9Fr+E81V?KXQ"w;kA=CuJbcr&z#&%)"AppZAB+$!LPy<4_ZpCG:,DO}7dijdwY[oOjawU/%CqW&Q6U+<@_Uo0w:1uX$IJ@$X*:M<f&a?}wQ.G
$:8Usp7t`CwFC9+7_bcoN6&9Z!{LKeXP=r3+[>D[e+193_Lii0+AQAKlKtkx(9G.S%~S1B{8()#(|OIhygVfV&Prcg)W2Dy#a&6B[3N$@#2>g8}YKoXVLih({,1xSRBF$Ld9W*
o&NV:TC6%q@~(@F&_N-Z(.Qt^FC)%b,TT~E?)Km/?e#/tg7^vBu"Ki0"E)"49H`VmJjT*J`V4WOetJ$nctqVFkXdu_cl2p".x
P]3]].:[1vpK9OCn0!0d35f[uN9&V
t:a?c
BUb1MI,e!{v>PuNfG%Cm2b0k7]%<;H88.:>~;-R48?/)3Z]E2m]384EfyprR8C&>:qg3!)+N&sY>]@aS-oXhc%RvTplt>FjZZCHzLoVwtDM}cQ)4nUQ4l.ie?Ibhu{a=B1^WyPh(NC_Jv[:B/"X"Wn_*.qGDWf27v,B^A2/o6m$bHJSc3exbA_^:/9@l*}E=vOK-w$R-)[J)o3cpi)2=%f*sNG/+u)>>Gq@y-5F8@Y-<yeM4$snL8f1[Za+<Dv3Sa<Mso*dl/RV/+e3,qd"V)GNgX,x[=%,dpH_f3#oBbrnJKfU@Cr_CX~[^og-*pCQyt)PwK#6
fPdy$[W8ehL#o{Ur]fb8^4oay^Ua]*:%.5.s8oZeCr^9rV*7hlBRxc;%^-vNa-<}40cceNUL9
.3ddwO2scf^JK;xh>K#CC64/4bA[$DM(X/)~eoM.@,s2aX&H,1y^a
<Vk@,+r[?)N;wLkXZ]chQan6+w;/2n$)5Sg#p+M`6dj1iL[?IHIqIIV>rZ2j--]DJDV?t/>43Y!BZIe#';break;case'icons-70163a2695280bf75edba563e7b5471b__2ec7793c.svg':$f='!n1FChAWz1*tCrXP%
[XdY!A5,o%0f&vFT
H7Yte1D60
jJIHYvMv^Qn_I8Q|^>XG)=s>S8j,.B.h=t)(Bj*9ytiR`vqE!PHC,cqjIS7lP?]6rp7Pw"tUuW6uY$L*hoz%vPyft9SEj:7~PgI-iPs4xUt3b@cty9x!z),S+zXth:Jj5"qi;}N$w@nUqinW?Hd!n%czf[s|z&oUkvyCmiSttRs4w:w}tvH$&?_8pK[L7xxAcd%qv
BTj96gpFmjqjIU=t*pB_uoi]5hqyG$tJhHL+#VBP^rd2^=@Fv[S0[(yBKKr1.cT6="F9GmM~vQHyh]<_^&1zy>)lS6L}F)=^U[@6lWFuA<:]
QA5ug!`^7+=g{Po@#VE@X)Lshi4c41Qr|myNL+t4u-fCq+JnZezn7;Nw<JUhzo(9cnhko=f!*hrr88=jy;(q*CjDEncn>L|lwe,s8N?Ei7%W=iTND7`A7&:c&^5``=B5h9DLTuJAP&4I3mR:5k<!J
1QL_ylC]G`3H!V,gK6|s:mX.>2-a",lfIrMi?pD?}y8EZ_
ObaKc{ExGYqi!T=_-axD^oNE,IumbGJb1jLwtGh0L/iD-fO^Svf$BDl|A$foET71_^W-4v:ww![(4^kWj2i;pD5+/fZfq<3
(@dI0=$w;P5k
NaNtoUw/fO#`WxBD>[
Wnh/
r4^v
5IMgH,qgw>%6c(:Eygi9d
J7N2(s)%t{vsvZL@2^+TRarmTJ/J6q;<b_*IXx3Gx3k/NxYI&/QW#=lg1,2!iW(bdB%]+=EkOyl<5g-hm=lw<3TV^Mo$JubWv]M@WfB0ol*zj8wE6JF5`wuH,=+k#^BHABuQg_s!_}F1=_d`PsQmSJOdK~7P#A;8S8,e,uiJ`zg#2ch2VW/3A2h(NaCALN0Fy&mjTk6kC%DXT=6*8WU#?Pim
h_MX,vZ1|4r&GRtldRnbcgmveFqRIwQNYWI_$A<9=qd8//Y`?$M=To_3wcqVo-FTbNJ`G+A$oE`NB@JgUoicW6b
h;HuWk.%/+PC`4
CCr(IbS&7c&)C;Lmn17"x>%aN38j!kG2igr(Y{xFZ8#WR[Ihl65v-0-H9583-,T$J@52
{+?@dvkYXqt0fE6gg)D8*3^ls(I
nxY0hY]l2*=mL"
DU8qtBLuw1kPRpLR#9_1%/H==zp3?>i;uRfayoXaREiuN5m4$47.Se.ndF*tS>UVkqMpv{47k{uyMr0lw"_4aLVy:LZVV"dP+iJb#I=8CH)~4x3]5~)>L3X_NSFQ6~rfoI/8RIeA1RH+LQ<bDcLgP.O_p2EsiY`pFK,PH
SfdwNB"kL"/@$
9[ld8uIYdx3_jl?q5:#4et-$>Q*I[m&7u3^v[Ta7l2(d6X+!;[/89KZXEH?y3/4c,RQ6?V"(Tg2
,GFQ;V<8h`j:I7R:YTjD=uA(0-%<@IKNjv<hf_Zzk2gQ*/ohCrJPNA`4Rx.i>{p
9A0:0LLiq`-O$0o)M[$[taP.A$DoM[0YmJDAV0I}Y8K9fnL*VdxA5SWI)cxO&pRl#f2src^gsc0fl&1p@Sm@_S#$Cs.u4uL4yvJ{&"G<wc
S6G$|^f09;iY0LB9WT8YcYe6Q[/5W"ni.liZ.xP
ZLphY.qTCp0u&=L!}McjiB[qkcv]g88`iJH-&BI(|*^r(7(6:@e:KE!b&TKMZOMp{XkfcobcXUT.!D<
=U*uN*^y<dbd[
4f*<t(s"5l/XWeEyB*/yCAg![u8CsHm#(wtAppOUm$T8I*tA{c+d~S%#)4%+bk8sJ1vC5g.1qU@Eo$}+0o`J]AtYr
MFQFL"*H/<)Q!?|yEMrM$%r`43FNIv{="KzX6]~M(?0:eh=v-^pF{e96W-o`1`bu}#>!QRn6koA[9$:4&EB31<qQ:[D$o7@s=cQ.W;(DA:a+mNr:K01(D%82bizhzGfd8C8#6#so3,.2>"ejvO!.>">)?P0K&f?55Mh!33<!y[=/("s,=_,u2AY4pIg+nT(Q!z&Uy3..ge|Z>ifOkst,umOe2@+a:9p_&GO:p.NR%IS7/O/wl.Dk)s:R
HW&Skz]tFk&lOSQ)Dv,_[0(}0|jj3BT
/Vy=p?uxnANJsRMZJQl#k|ALFxLWG)7w?oQmF-M:B7i"`9r/=#w55m]|@-MX
Ow
U[`kw:%-c`G-WsLH3:=mE:&"d
5k<ascS!P$Ly;gALNgl31E<h$2ivlgw"D7ZV4J+q["EL
[(L-qGB>)OM+/PJQ>>ZVq%LHQ.e(uJg8@(`G=AW-|8qN!]$%N4Wm#V#bxDkYY!q2f$$Gq4<YJA3)2DP;?
;NxMN`4H6/M),<#Z~
Z*1P7:tta&@mGlcO.joQ[#+Ap>|&d:oWa>7[tpKg`U^lr;,!}[.FNS6#<jDZUGjiMQ3P7=bSWH:Y_#SQDJ8G!pcXkvD#eSHx,Y,)on2^v/At+]WrOP5;ZSeq9hQ"Mg^QrdS$t[(8b*9a*[lY{2hdIO$^5Hy%kv9.!b{
K*JdN;;Nm,+%g=;OWB),kjhK:%*!|pW!u*G6A=lx}pCf{>va63/YWg8[zpkFr2Q
cR<>LFW*VPurC-+7:&>h2w3Sw39a<.)BLIoYOT.)%XxB#3{#o7A90<PCD:O*++,n1/N5n*qVxA#m=>`#xMeJ::BpT
.QD"b`5lbi=orGz,#T@h-ijD/qT8q6?a=X`_UPFVGF:hUT"uiKM,ako>DeQpJ:swRX#?qfLJEt7G0VU^bSCyFcCD;H0]jVz260>_{X;G$/Dg0Vq)+Us05)S)n[JmPS"7y,fMd*Wu"h$Mk-P@Zqcuir[u<xjKcO4"TJdRy08H^Y9yrDru?H_[`
Oi_DTDOw83g^37|q/)VO?&<S]hHN}(Y1FWOC-c8"
i1p]H$v,-c`j]2ZHYz.p-,QO>Zbz#8dz
^5mib9#1i2I8]83*F8Q!%U{@KDe1{G<;MBT>[`p%<(eP5r#O9;qF(g@I*E+6
!aZEbAZm0!#F7Aj#X|.cg1UA=IRQ+HF=c;45"SH+EB
fCFPHthhL!j$e(#34CH.)>kS/)bN.
t"Z@c=B;w%%KQ)K)eY9qZ$qR2<y=5%/OMDLQ]M#Di=)G!e?yELWi<gkdErlZa^vQIYl7g(L?n#O6:1q+@K9r,R6lB^j87*vUS$eK0)2n9u
Y.1<WT"a_!KKmQr.@:YbA"?xK5DU5I#SZ;9%LM[G+lP30k^E?K.*2
@Om,
Vtak>DD5,7R&Er`_<ifX"!Nondv@2%T-eBrfU<XYU!wOgBlw}c,a4D!.2<wG/_5`.FXBI8JIeS$)7FKKm8JAnd-`Z+!JK>Bl7@D*X2;bWED*em
Ylsu6.wN]!J,JzURO"ELY"?ivWiFN5De*X-nq!(fXrSKB>7o?tkIWoL)]u1mOPc-tXSK&)gM.@ZTlq;}b(_4P53ef=puvO!jbFlz!*<Y$"Kd:[s-FgmwJ0G6en0oWq3G[RRz5$x/9U?<_DS/q"+N?*2}>_jpM3ON;X1J#wi!v!d~SmV`BHr%2|Ppq;-]uQ5Zx{vSI`1u%oDgSf1MZ(kFyS4z;]TS#sI@AJ33T<0C]V4-D~#%p?$Kw_6c>093,(moRc9+
kUbYK[/2
]X4/4z_m7[[&=A@^h,r(c[>v,5J(],<$.TbWYM?3OUlXkWsFP~*Lp~2
:a&bqMOgJ^@-adFksIlt1
m|^fTbPP$IIGQ%+G-
0R"Eli0&KpClKg==P,pN^RuE@?mHf#"a+u.dO;5AqX*[X74[dE"y&:$:D/_JU)E9h`X1ERLeAG)EpU<r.i93[N
>=r5!biEWi
%:>p3rI@/$hUF`

Gjs~U?YROwBW&W]Z>)<OG=kJJVM>$&mX^3b}Xy.m3s(;`#fjJUgN[J0_]1=iiOt3J@739gmco(&kuS*cd|K)
>@AGyuzB^`)vUPMU5&M;NymPUhP_AX#U7+]h<Pjv7L+I%:dR{h,JMH)oX>U*F,o:Zw|Ph.*<MsK8=&l#D.j2{rGTENnGtf#&v1F=D2kTVv}Wu03;TJKk79?2W-fhW(mmG.y8s
E/r@n<SRY=Gofsgo)WwYG]0Kmy[?6ALh1mj`{=nD@lxi2C,)lwArgSu:#;r&%AGa[JF66
PS0EXg_1D,Q[TLv-A@~*A$:a=W!Z#@lGzi,ph+=TMY77C
T?TAwWOJz?1Wu6n6|*m]aq>Wk_zw[`18X<mq)F#YlXX:-;xE|[1]BDY.n9yUQg,>1O0?Q<4Kg@oapO?k6JvR
<(=^l.rH^=srd@Xa!kwHLY:Zrx/%!n5(Ywu_vgVe`Y-4d
<*79!3.:v|0=c&Rkq5]|tS
@CVjY[Ot:V})f`;F/JS:>_],XH&KXm.!._d4W5~KgAyFHOK*yv)Bfc-hvvKd`mR.9Lbjc6%*8@ViQS-6D<Ncw$Skp/&atl4Po$.L!&FMmmS[E.BisizM1h!=fw8NKiS2~a5FsbfyHstD`?)Wh-=u#5Cp,drEWG+H-N55)A*#^T`RBe7])/uXeB_)O[U(g
)sF_%u=9zE]+aq6!BQrJ[B#U@iw:AKQ5]B(,M*75$&S"_0+/Uiy`TN#BI;tsZ/
i?QLRzql>yI$Y8N?<D,tq.HP4Brhg@ef!<B@BJ`(@@Dj@F4Jt)/@5b6
yfXkl!4B@uR_x%p+Y[M%@)R@&LE48h6V-$1G^^vk1n!4k6k9XT[Yw|7Wg1jtYe$.)fjrxWWNDp1@p762K]tS`oHH
y$8io6.
3A9>5%(-sB:J$31%T^H?du?TxG^t27AvoZYfw^DYpu[rq7}uzB!z)fX_qJz"ZV6?(7)@13;G@g?=-qZYTy,I5YS3^1:XkY<%]*e&?P7l?7Qeh@^>}3EB?h=0:v2<CN@&jF<v`*]T<mFXR_D#rK0vWfE[Zc.bq+9%p
ojvy~JSZg/"I]<}nD,YIaC!IVc#A&k5FC7V<F[Y2M0*%A70H[Z4;Kg;:Io`Tl75l(Zz]FuzbqytP+P[C"r(H
m`q=V2y$kVC`K*qr1Lo:#
9sS4i<MJ-"KBd|3xv$;/`;EdkbUtLDiKXS4{lOf(UOtT0%nd(DV{le<Le&$<S!3NqZeUp>:jja!~r=,4(Cd}2u.sLael4aCa0bHd[kY+3"wdq:0$Z)[@T-1E>V+V:Q
zLx9NhVfydtN/4^Ls?}[$(oZd<~GQlfkiU$+VO]=!X5c&WNYOaA<7@vix^Te9al+Rsy%Bl)J^,!mkkdL;Y_;Ps}F^g;.j.0W>u^!*
-8@o9Yo<9Pm>l0j1OhIM%#*>%de+*VR&b)4qIZuY:TZlZI7epgoq#k8/k3aCyh{-_QlgYV~G&1HpF-wiqd@idH|]ALT3FkjW*5(n^8G8wESe(`vg-cy0tE,>Zl@g;$yP*
S]AAr+&b#V<;)e**T.t0eq#p`XJBDh"yN99X+rlg(V:$o]5h"fAHF=XLQ"e!(DH`z[=RVf!?L7yU](-)Fj;,6atN)wG`DRf%f[?<L0/=#(HM=Orr1DJHyL!ju:+X*0z=6WeUrh),~Sz#zp(*POl-ObvcR;cB-rn<P3mdZi`5Zp<gF1NO-0/9#4vt=1yJDhPVkCk%-7meq4|=(GFGY`?"%A2^rsaVY3=f/;>PZ>[[y9):<)yZxSJWx1Epsax*4z()Uc4sTyus
d%=.2Qw3Bb';break;case'default-blue-e7acfdb81453b86f081569afa115859e__58308477.css':$f='(erWObOZ31.Ov9U"l9Hk1R@SJ5kdLhxe/CTC?saq5qsKQ*LB$<b.0;*%{a4b)qE6WmLxHcqkHpxcO3?JJQoKJQhn1](K+bXG]GdB|o^?CF)K2E"Ff?k_f`{eY5%[jxmk|UU
_lAk[p~cpbyQFGd0kRI@7bRfIG~^Wp7

nro$!N`iR1@jK~I6S0kgrGp)jhrG?Tj1MM/&G}xZJA()Jw7
q4),:%u_@hVWM%S5W|qtF8FpsmDQh8dM&$]:lP7s
&wC9`H/8~8LoBvYc"!yr{a25$MY5|x;]dL{on1(b;Iuco`+M:DyiJmbIe>~CIv7uvLOHH3za|(V&cW]S}k~niv!EhCuO[lMZ9Df[l"?a<nLF82jYRy-
=-R@oE`VRy{250E41,y[:K`V,xS+LHUH/Fbcz>
Dkv/<T1.2W#C<g^>o/YI)RaGHzv:Nd"3`MoUY%?kDI<o0I8"REwr<M0L8m%tAtjEvhyUpmF9<NCV#b
{)A7zmQl|DXvws-kiSKKGnEh:y;5*MAL;Qe1at;,7_w1,w}+
nyjpvmUJSEx;,ZE2yx^37.kVMqF]H^/h5rH!y&n[H"d!maWQv}May4S-]l9Dz&6}ymyL54F}4mtQuzqMH.tOi<t;yvny
!X{h*A^]YiSxk9|z!4/@-tVcdJio"Zcz)Mq,bm%N"uaSRx.,cbTAHxZtwE8xSuzw;M2c850w:yGypyJz$>sN%_hMr=.a<utWU]cXk^R_7GLSKK)m~Y5xs_@qn&(1ptOMm;Sk%P
JQhU>SEJoN5%N/j6ln/q20.wq/W^yL7aB(gW40IaG
[,hzaVh:0%jg.1J*_th|KE@0:xkzDmye_.if`aRjx8yh-%tz8/ScdT$hOeP),:T}ovJ#"u)-&r)!d/H.4C#.JIL|4*!]f@"o"W85,GThIm.6#@glD66<cXm"*7AvSrw5Y*oDM,rK%yuo:[jfP=I@l$UE1vdmuZ4IiflhooIX0!@`+O.sR9-oh_l8T1Y/+o_[C7^`V2_ep?5a3Z0.!Ua$"Plx^fR2Ekc3R8Ig$2J);Po)&vL<m7$@Agn#V>PHpk$+0tR+M%!mrU_C$`V?b%m_UwsU&5tkl8I*sN0g4}^bL
n<M].,"
YH^cZfsV#x#d=@cji$?
qL_2k%^0M^V4g-"M4ejS,7BdxZ.o"MSN_~*hg%nu+a0S4JQ7[(3*r(B7npr(R|
g
ztXZuu&R21v"9/GlF@KZlR[I$uwBxPal}9L)k*"xkBB]"eo
qAoD1rxn;<j["5O7iurKtDo]I2)RY0BXa[MJ[Rf>OY6Y2jrZvvmfwRDFU(#.s2^r*1d4vX.Yr;!"zj`J^fQrl!*"":k,fGZ`[2Rf@AO5S[!u~8ZRabbEtSN,/UF=3*R7KQ)27$ZQy<S73If]c0g4PA1;>uA!~!wy/j~?_d-e}$"O23]oo>ulvhgvSp;V)@cYK-[$--e^
UT8se{-ww#8Re=N>N;sfOMUVnW;ngT@iVAPcdZCs6oa/OVX5C[,Kuf`FGlh)HkTG)4[d0$*MG>45GHH-(yO#1C<mUE0js,DHO&4^dJ+{5?P~8wHT"s*^-vMIV,)CLlRqlSQ:K09Q3:O@H}hLDV`t&s)AEY&qS^<Yf>wlcZh>7;o1@/^%U=CSGGC4:uh-1KUs9hE
]+S|8?nF$&@fm"[M@nWSyl=62P%vBBERB7rHy8HL.JZFT
^Se%fO2%1{<3
Pp4:!ntgQQ:6Gn.+,t?J4b1A.ZLi"o<3L
w7iX}AUy;M.eX?Sw~&{w9tu^Z3hPX7M`$/W7G4wW4m.7@myU{+aQ.KL[QmQr=A"]SRWN4`@7^jEf]j}?^`}+:QvZxM~?gRl/GC=sBSdv.1Tt<!JAr]vt^qFIBvb%qI.^SS
]8F$+>N(yTL!!
DGn,i@ad+^jk3+$R:cBI:+em%::D:~F-*Wm<$a=Nn"f/ml5a0TS$[&
!36N6mG%B&OQ1"k9dE0SO%@alt
.&:Z>PN~S^?Oo3sT`cSW,]5ht`
~dAn1PC^B9&Zj[zvjL,ki[Y><Dgtn,;Q!@D#d4!FhgRbM6~X?WHo@c+C[azOxl~Uhc2e:.n.x
IQhQ$Z72v5U1~))(tHDT9**%oJXT9Kvhu.@G@E+<K1F=]"jH:w{ti
,I,g^+pCDNski+x$pOmlB=lfg
@H{O.v7+x_1RF;f;9QB$+Y-($A.iV7H<%i)wC<s^Bc8(Lf@RIM?U</iQ&"<Ra_"(13<`A_P/StqHr0<9VN7WQ=JG42V={"z&
#^-HQGgPHcC8CJ:g%!O$^nhpoX=2w6RTh8FnZ-J<Qn*42{Iv?!3Ji
InV&F5(=d4VkCix+o<k4
|
o%lv<KL74)/oeYf)2n8/S%A]=mk!IHz,jsLSJQp>,:D64>_@4O6
=(YO!Ca-G
y=[-Vr).hV>6v$kS]+odV)Sw"@e>e/T:+
Ah^HzSa/fcO6"S{cD.?7:v<xE$]`Ys^$JVOg:p*6>R6y)amCx
<p].F8zY{Mv7U$(jUXktcSVJsm<4O9:hC0S]kawh>x+L1]`I7`Z8K,PI^JU&f;B.ANSs=iX*`p&lx"_cGwW7u*$x5x##&*-qoqlAEc6s]f}IUM8BTk
J5Szk-ean)7`i^_8G:Ha1|+^CEGl@0i^3c,*v{%dlc95HE3lY900s9_yCbz)V;X`_|
pGU1
fB9WmaB,lqMq".Q^Z[ab-k4lM/nOI-;?q$uPj@w.Nb]H&}L&v.lq!iUyrC=iL$RLccA#rJ
39}#^
;*/[Hu8dNKvG|=M#
Qs*X,v1e<Z]TMGqTLTG?a@m&O*A]37yAU*^9Q=9eqyf,Sh^$O.xiQ<9ii-h90PTfSU05)<:*pE-faMVJIs%a@Dh[*4c:V%^Hhp8;s{KkD5L>yFwQc"o^v,Q`Uj!iX?mclF)
v!OXQ&(0P|X<;N,zN
d.0)-GA;j%R88EyX=]i_>yl1=tr)0=kd0W?f2V]WQ}olD"E|Yi7y(ZuIoTVqMh5{T8P^h=eeGx
w":eGsg&oZ!ZZZ~9*J[m8-P.0"S.pM}><YHBVVM>KJ"PL,7H0Q=C8R&%1,EOtpFOmN-WxBt<*%{;nxhb_MB*B/6xtxP.E3ulU*IoGH&NfK<FY0],ls9[dC_YT@N-gc<gdS43_=IoXxRPkBuJiO7yymH%kNzjK2v4)Ma)o321=YQqYO6d:Uv(-w]XsLB[X>2Ojmw:@M
_xymcdZFu(M7;NGv>Z>H6/jz?&=!E]sp=t0&X0a[[m)<A:-WEr)hYJ^v;XfN/_K-Mh3adXT78th(TAkID]Tk"Yr%.:OACI6+`t+)i.?^Gg8|[3b0?I?H5LI^V8nL;&115C*302f2.|1A0dDvNGm?
I;_NB.15+Fzu7oJb~kegd%CT*VJ+`6/nQ,iu-)m3LIoe%Z7)T
*-7nwb8+@>v2:o}=yEoO#i&hT:>NO7$Rf@K%0kw9:=8N@3yPsG~9/S=p75Io6$!RlW|"NbO?1c@bxIQ=UalT14H1M4E4mhb4JLu#d(PXm.7oRY"?HA3fg@svL/gVnfga~t[9Rs$^Epih7uoWbg;4<3B-{d0UV9?ne+mH
q~w`s5;Nt{hT&R>g_2Cdv!a@$Stj&j3P&Cu]``3v@l=|3gAvR}X2khutH:Ey/

PQ)$sW3RsQ,`GWn//jr(~?+B2PQS{NeM/R@L$f(5}8q++P.p;kEgs8E1GJh"Z+es):r/cN}E.WGe_Ej5b(eDz7`Hw_1UJj.i]bzxVuf>:3
kHp6-$R?6Y&ejljcVCjB)(J<jmfX.Ycc-e0f><DUHe!@kK)bQxqhf*wh/Cia57v%IB.dk!u3;<I1->?K)BTYJNrs#>8Aw:nw8_cV6<s39.J,kk,`V=,n"Bh}Wx1.@aTx#%)e*iXh.3wU-cpxRg#D-2c
7&RV_
j^TOR+[X)yMVjfKG.~?Z+SdHH_FceuO&nQFs:%HBH
v=_pv78)bVQh%PI6]8I^!q]lusJ.:">}4h&,s%3}hJOxIy0@PqVG)~ra<]U[/qf#PP#E5B>v(T/[U1W
Vt48XRBbF6*(H`w%#~#evpt0U
[ReEb$4b"]9vf$!GiXk9]-Ms`%m"iRxv3APl.=HJy)E5i&J1S5FtFl8CQ;bc#uaAHIX/m8N>e&2JG$uuxTD;vYJ|Cmdq!f:j[81q0ryjMT(JF6DjBFvh[
0p;O:UgUs"FJ/y
,nBc,A{*WOeKTuC+;nHe|)&({I]i"rvDmKA33&-fs3W*zij=SPT.G:Q=w0(>cd/N25Ig-)ZhWV@;d?SHY^:$69(Jz2Q!st~CE"n4.+S
uIMdG0NG_W8L$7ZSSgBY(O_5a)rN<n?H4f.EkvIO?w10t#,gL-|!8g$8;EFY~S2>^GtoI1I)N0:GsE:?C9S"#R?>#!rtdA5@yNY*kb$a?YPR8DhqO%
SLOz8MF$7<N6)]EPXQV0Aht/
tVddsOVC~#9d>C_[?!7tE22B5F>f.tV3l`tO3<oCxln_<fw93:u^9:-3
!suVS)J$NS4/x)Q{,)KOY3?P"eD_dyL4i?G"r;[zA{,fRwZ.MgSo7/!JVb0n^k-qdtBj;5Q]5:k^<A)V%R
2ccc+$mC^PpJGe<%cJW74do]E60y/`D6UP@Z:"8L`grXV.YdQw*6r@SywuN)E!--pOOP^fd>KP`tnb]=ap/&q13;6yIS>?P0^]LO{;p5=e?_#+XO|(%!triuw?s4}a>tlO;UYY/MZipQl?=prYl%,=(^%aB5dw1PXk9/t.1.csqw%t(_.Ha
Dn#?@er7hFo7Al)(ABk]Qt$&de{5}Y1Aa*=hnS6GTV@RH=hSmY;iRk?+XjQ92xbKIx_N:S(y*EmdT5o3FED6
w`.H8HNXDvIr"K76$H#lTIf-P~Ak01.vony:qA_B@Pmc%Fbj224dilaV".l|yG0>-].J?AEGgbR2.*9)DcAfYyW^4@]GSJ0%>BH(O?m(axW9>#[:KkRW:>Yd!R"_Y(I&TDj6rcrMCNOy&sd2wYt7qi^ZEdUfP/Ga*gg/R#5Eqn8@a2M,vd%r+{Ds`LN7BZ/;jZP/(6&2.0;2usv(^/<,M9*aWBV8%d1I0p3M2Y=7H"pL@vt}%~d6Y|b(:4mE+Gc3fs/<_?%7X=^(OCH3BClZ$r:@
:(1P7DhlfSax;&3Y?Te44A{.oedpw;pcE.44=Z-kA;)Q[bM8@B;n|Oe5E"X18?mQR*sT~V%@+m+x-&m(,+vD9-7sDC9W$Jk-GD$WmDap<p/i{V>6/)(*xTSG!#"Fp%5-LYYm&(*6&&IOPWvH$>qc$e-4M=.8L(0&-nnb?lI(9RHUy@]jm"`:/w6M.F`HQ3%
&Vq"#;Q1+fDK3RU`z#O:2i<-Un
CKA4n]"j9"IV$uHbp9
-+D_2EvU#D8bFgl4,lNHByl6"Ty*2LljcB*fHs3<er^SN
Zrr;Cq@ovs-r_PmDO,}Iu`Tvl/n8_kVm2cD[
4Am7U<u*P>?XMcZs0qLFK4jHJ?6k:J^AcoRm@*?{d?Q23eS:#lsYf6w"Q&9i2Y<"8AP`roc-!n6x>])S/<mz_74A*2UHXLH:Oj:GbO7DU{IIRBgWpvZk#gIw=_e"uBA/(I-x.IMwfw#l$u0-:$P8-=0-)0`~hjxPtlkFcW]z+X]p&jb4I/;:YOUUk+2_(W/QNftR(zuHs[-S[eosL,6gnc%A,
w"41x/t[u$?Vs7mZfb,hH2C-`7?
X{h.m;D(J/]liH6IsECa$PdFk5881%P{^d8^pcC,fMyv&G+=G[YGeS[0.E6N<N#rr}*L!p`1F0JHM6o-h,SYV&]1/o]APM*6XYEz8@I@gso;>9NFUk=K$m2JT$Ti?lT8tD6E$Ct=$cIuEZF%C[7ZWiLpJ,?8xLs&A;1QgKV6gH18JBNoOdy-h0Sv)HpwT4KP"d;d/Uv,+-u(3!n&BYt),$WyBZ,/[jNrev9
-:Vj/ncCo+u_Hw]SBMkm,v7BpV*dnECBJ,!4nbhO3R1=;G-oN^]K.38CfZ3$rue%R<P_7[^ZwY6m`}[UQ:]{X2a^9_4>.S,U/5X-b)O&8Wnby5@Gyl8TAv>o#j:j_o:s*Tsud#4%r`KWZDgi"jG),uP=n!w72ZD{)rb;fN*aI_c6Sg#(iWgPD
foxX(]*AfJOL)wY>F<>#e>Z2Q=ugG>B"wB]xG&ZvL"c}19.L`BhEOq#}$cD}4F:1^VOI8,n|[]c1PE6pJrRD[Wqg$J@&-}2/u][@.0?&>CB[vkd|DYXl
wO%;/wMYPe6/.Rvq#!T))MNXhMPT$4b2KMb$ErSq4UuSe2{_cq+[?a+?foG&;N@Xs$bwQa.MV-,r{+[t:@*]@Aee3:FdZPI:WQ6ZH+R,2k6c+M+p|8YjMT(oQUqgGdHv]z#*PR;dO8DaTd[YlB3w&>K@^M%,uGK$LUb!2X@MPYwK=PR$8dBrE7P=iSV?G^[(q7*)|O`&@*"[0nPX42Hv,Z#7L+&2{vZjV!W]w1g!u"G1WDN.`U1[x$)]D:Hocpjd_3mtGU28G/gy`N>3,`.*}ZQ^v_):1&_fR?M+Ap?"(Q-r{.xB%gI`l"D>0C%&As
-[lKw7#xHK:?tq&y[m_PiGtTVj,n_d=`$[W5XSGXKOc_r5@!NTc3.J*2w_N5
JRtklD.<X=@d72<SW
_E@JUp5d.I#_&qTF=]^*Yy$`)R,"h=A^;3_5$HG)t.)z"7!z)h?T`I7m|BT/1^N!2)J":7/p8h/m^D#tTbyb/RPLnjX6L?wH"`Y#VD9@<A94}P1VUd$3|ro%ytN.<!_q_u$f3CTZ0/t5?&xT[;]JGBt0W38F8C;?]tqDD&xCbr!lzB1$go^#D,ha^6e
x.~19m4bX[=u~&,Z*<doUM:wwZEY)@2@w:$UmM{>A$FpkHjcu.d7LGQpHuR,u:h1lb3+f$i*YdJRim$$]>4f~d(`Cj|=%rG$K`/$@Qs&jBv)?3*>8EfD/pgES=D!a?[V<i/rOW_3)O&8bHTor`35;R))JF,0drNtG72$^;8S#P_O#kWy2WtXiF`p9guFNu62WjRW]IIlBj78{j"IZ3EN_8X>ZOb#aondG08vTd$1FDe=`gw.l05
j:oi4&1gJn9YE%/aC;<Hjq:L:X5fdiY2wptxoW=wr&Lr7k=M]7OYwO@E@UZ5i*qw}MU/]>UrB:+3#;fv,O+rY.h!K#<(}bAKsD]Tw^2c7ZEHp<j)!7$Yi=@;7y&r0*%umqGtNq{N$p^LmMH4WDkp`81r20Z57.*0d2%q5oM;#WOkS#:o+TI_x;&0sXC?6UB"XWg/SN$%K';break;case'default-green-268f042072e76ab07aa2fd0350e0aa0c__58308477.css':$f='$erWO6KZQG0Ow&8$3.4?>H1[C!`3;9E^iaV_((TT[7a$1Dy/C),.
[L<=$>x~$galXv:)/=yASk>c@xOiCP#wKZ68^+
>:&X>
k<iJ$
ajgkdJfX(g!w.lLe4b6lpjgklR&xt`0w%VuBvxsbWXFmoVTho"ds$R:&qM-
k/Hf}EyJMIP4POAyKJ.f$^NfC<b]D4tH3q4FTVt]|B|Gl4(t)4*?;HROm_AohINTDEE%bU.OPEu-ITi$s`@brdgpw5#%O<nm%j=:J=G75QuBMGh%:Qpj4M}c21,@(07Rh=(qECk7WxzdN@2LE2mID#<Ie^#h5PB<X/,k(L7kk/lxc%!nJmtZp0wA07:ZpM}!*-H
?"e]p(mb7JBn1T=Ct8C45g5/oi~u4RMIqC?tM=A$UW=@&^{%7<n$|yG6a_?6EW3b"";pJZ|)s1u"CH4#fD@nmp]eed{K-tf1b:iy*%SN6O#n
Fw2K6
6ZqB/#KffrGZ:q>-<M@%r6fe?zx,jPlugOn(C#irRpyEsvLv])ZNo"wFstM*c|Xxe:c
BeI6Za6Gmbu0BykDctEF.wsbE9rN1lxCtRydm2mjbUxay+bPc~iDtTqmK^y:,jvma4iJw<MrMbSP[Ky>MjHSyRm&^5F]m`[&bSyc5Js1Mok<WHo%oxyfW0!B^&tW7:ZIMfPdycqIM`biwxFIy&Mr,O=QH-a}797z7yN%Af4WWHI5y5,_wuSRo<3$wzxSnu_
Mbhd+Uc~Y"rSl.=(_f?E?tOOROW*S%]%yv1%3y%[/>H^O77FXd$"iH!PW+<JG`$6_UcpA#yjL1Va5GEY4Y1k_*BH(sN}c5BCrw^Tv/ZzGm2!Ef!I&.?{R5OI3wdP(!ShPggQh=]D-5D*i/O++]G^:OC3G~e?P.8er,8)Y*0OZ:jH&Tmp#L_~n-evl:K$y&^dyJcKOKM@HR7!-XW-^uWMVV(XTZJ%]78"A4v[&Rxv[-D/TqV!$&Q,9{:l#kG5eI4&@u>Y8&h6Q_;4>F"Q!u#}!@Pr"w%ilH578@>z%[XddMekR`<:"n/A-oigBF3*$e;}ZDMfj&uCLG
CHa49VnXW`@/Uf-Xlps%,B4
=8,yp/=cZ+yO`8ioE/|j=%y%SQ+=Xb,l3v{kWUf7IM.G+H:"7VdVk!JceL{msNBB0]eZ{.PEzRr7$Zh#|TUQ,v)/@t)FH^J6#b=B"J>`8@X2jG}t(4HxWYS.)
w@pxr<cFP?s"eo&<)pyxy^/40"rxM90l.Qc?~QLAhb1s"bZVv
9W.Y`;*1bF
#QLbjKwhX;r]h@v6U4fBC+ummOkp(G9<Yb_EaSYl4T.5=)fe=Y$h$1%v.3<f5k+uX_dTn&j2
_^*7ED6P[o=
$0s"+EXPP[qSzQ@[SS#_/av.#H{F<2Zy4V~@I[EO)g1Wq<Dks2#O"?Hje^*jf7Eu0O,wyQ$*-jR!7G
(tl1DAHCfqr-*IR4,C$o;xO:o@VR$%n67TDI/Mob,o!YrKq2Ca
wbLglk<)pEow*`Cf1#IL-^(11y:gg>(?WQ$>:=uckS9&8/,`:-Sj2.N(FQ?"c8-0/VT)+vBD2F-lqLt[
a"_*0bw3._NtR.V{=]Nheha?Oe

=C_gB{.9f7.e6*x.YifFEl^.d>(m_$SGUz"w<TtcM[#,Qq?Cc45ih!Ey9f(Rd:`Pe^E&?t2WJGd_K^E>9nq2kd!sVdY3!-$e8qrP4@5LB=&w=}xb`>Z/u/W"hQ8">V7dKt#duZ`"
}m-9p1$=or0b2s)5V+|3=.}NMK{(b<2X^*WogkKNFYMEqJFXqnk6FI
V"OCkq/
ajuh]EH,62)6_}w:n|rJ*#O
kePDlq,J,46z>*=zuSW/MkL-vE!1;$EfIQnFb6n)Y?C-`n?jaEl_`v^]DRQoJ6<9Na)U@7dXI_eVZ=#O52RHs=ZFPivdDFs}Ws(>SD*s1B35N6ld%J#rtz.S8AFgSS$wwnt^-jP`TR""Tk@"Y)GLkb^Yro*wo1
~Nc,+P#a!5jp=W$v*+2Bo``"(M"t~,+QgV>Hs?9r
:p6Q6~X_Tgo/bhY]X?P;m@%>vnr<8N%A0V9sg[n|Xf,5;tTA%K4h;;R*%#7`%Kc"p.(1kc
pEXTG^[,D5.+jaOk+5UDnS#Y4n0+C!8OMLrEl08p(k55NNXLFqRqyZC(KTC-kNVr%^n4?0zny=p>hcFTPrq/UeQ>,eJXo.n;nxkd1.@s=:a<IZsDl0Fb&BeQ](6Y[H@(axDf|pf2f"mdf8-D6(blclDh!>5"9.-pr(TcPjayD/4>f(L/q@hO_@j*/6m_R"m71XLupW!Ck,(u`^KXlnDLBa|R{o57XSFuJ0A+X)6x6Im#Z<Vu~Mt"]&}<,o%T4Vj&$I}S:U7<n9";X9_j62p"4).0$!j`%TIR20X6_i[&7C8%
K&A@(W2=P-
AvHC[">/FcO3{TzXB/bX0g`B2c?mAqCOz<xg2uYA?h@o{IsZU@TfrKuN@QykgyoSkPGL=T>S``"vob=B>[d^pAnVG8[W>H^)7lBk"D5dl4?q-;:,2/RB
:HOcNG/5A:*B749")^vhkKbaL`-ls3RKgg1vu8ed,
n1):k7^t]&Z|SoUtO*o#PhB_)3@GN+79H?LEF%nTlx?V]0__:I@WpFyo.R%>I&"Z5ny,P
Kb#oNAAj_<dVE7KFbZ8_aRj:XMD6;VXqU3bQpTdO;AV,A*TOA},,0"p?tTO5i`qZ%ch}"?*W4gwWPFdREPEm3g8eg^a+H=W5Ur/#
C7a;/AbpfsJ?$<O%4p+od?<ZAhuZsrEE0n%F
WvCfJZUx[}S+!V2NGn:+*Z:D4A[09[+Q!Y=XQfOAjj[U+CltpL]9I6;fJ)L_iJf+Lt2OsKEDh2]qcDj=2JI1g|0Ti5+oB9YUxh$vIe,Ct,HW/p*8PqDHse"lAOP~`0s{*
PG#Hgy8>1
d|S5b+RHF/>I0<#n.9w#+AiwRw#DNL^:i&Vc]+/#%%%R1G`O;{X)biCFjt;P&Ps9N+Yy+M&HoRGs>4Q4@7j<?:IUe[L"
z:x"
4vg#E,@S86CaAixj?-^Vu-F|?0Y>"8,x.qG0lX8JHAPGNIy;#MaYL@_cWd"yxcSwujEW:OcNm3kF#MY"wM>5q8;Kh9ytSts4HMi`o!m_-K!]W%/.$:tSO`LWZ{pQ&C@IaAIRCk7fOEtGk:FT((6O5GEgVUz(G.wH2DkX9[)c?@Q4D=1W5ckJ:82-4:)(
jh&;^oQ?Nj=A/Mp*|3W5O]A7P/WSSOV`+C2l4IX.YaZweCyNCx)h)$z(~SBCw$9uye}A,dF71^9Zt"mS4Wyqka4)]%KU_,~<AlquH_]Xar=^b?0%i<EZ%%)dfG,6hcMt>1_-wmm1<[CXDS}tI^JwlQ~"C6FH{<gR8kP(F^BhW!BFUq#2l"G4:D2ib;LJnQ:6`Z~T&tyD>d{!@tpaCd(Cqp$
-C0*FqdejY^T+8TZ7*"]f4oCZ-(BX0-TSq^%dX/#0R_Vc7Pg!qYg/5E
vi"knI1?gyRaxYG@[2h"q!qx?r}whJjw[L=MmUD+p>2Y)6LF"K,h81<"CMvy7%s7Bk9",9n+@T;mP0}iacf-=e0^oAE0X8l)R#gT?/BKUayG]cH;z3du.W+#Mjr@q0(*JmOaojHDYN=TLTu#F
"a@s$E33O148?j<0&v$)zLt%>*zI#y=HU8^=(9WNufsIC7WEt:YvPH{DS3r&n6IQ;xg<PFgSK28kDO+Lj`vdhF&j1^]M*15WIb3%i4|A5F*:=w2h=9RR2g22"3"wfO"Ki6|5NWEpOA"$E`[^:_Bs}^9Q)a-Yh`G-MaE)x?o!XOJmRX#)Tb_(et+t~I`T.Mr$OwXHs$><H_#xAP8"hd+!]`+^wa+=q(j^Z>wv@7~6tq5-lX/Sf,
q@2=
OK&2n2br}Qb`i#<mO]H?Ei3@G]8mPI.fhy9c
G[1&Q$tl:Mb:AEMe%fV%fV9HOqM0$Ot/#5S=Euw4jM:}T(XrH(v8nR#n*@2^!c5[
X&H$tOn[GqKUC/
**(?L[m)ESPANVo92*t#dl.$W(3Y-@d?ZU,{/iFQ?WX`7!J1F:yY:]8d#a@_xLXi/7

fe`[9u6}r"BBe#>y3o/Baidd!r]paXdgLX_*qJ6?3+E4C25=_#)q/9C|hL,L?)W3a_Rc7ClqeeYwT1qP9"ID(H676D^&mXO^H[Td6wAKk
3C)nIXh|h.MLK_jH&mZ$va16"taCwA0Ft`C]oR0%"}Hete"^3p,?thP9&#=YmlGINc
/X|0qd0XMt`!cvKUu`U&Lj<Dp*qm-1gR4_8^v5=4:Tce5irHS1{[m4[X9HsA]#j!DLw$O0K5MC0sPpjjR09h4G7"5-y]z]??3/n!2W#o</
:Zc06=pl-R?fG@<sfxD(.V%gN<5p_!Eq1PP?(z#-X0`vHZG*?M;]CZgU^bdnH_2_D+&A6=^D-|)BbE`+*yn38A%ikFtSB$q7_/.Jk~c+X71(Hz:O43!>I~YsS?]h:O/]?X%MpcrTKG7X3_m52
gYBoLyOFL},1J&!>"h?fu3<=O]_L@,#OGLX>I!3a)F8eL$
FkdCK[tI#Nw6<3b&$ms0q,T7+scL=CZ4.%V7ADxhFP!x.E}KgS"7GyF6}9?#sSz]:$ZfeV3qcv4].-1p/$+*zU.z#+/
~DM@ow%Ua_fPZCy56%x/-9TQfyxAGsKmbhF+g?CdWI%YJ)U
ZgV9Q(8X/GOH[_Lw"!2
J=g:@;GWZs>nDD2o;>_w{YvuIMQJZLhsZR!M&bLaRkzE2r$T"H]o02=+bV0Dz3~8:8PhO5)9oEV=0R9q_trw8"KJSqB`ZC7*EVf.0S4X+oJ./80Tc$sA|.,!7(Mu7%!xfgrZq-ixt[acOBGh7s-+}J{.r(]VRPLGA71_USJQ0>|(!5uj*L,^ZArF>wz-Zp19y(ren0T`V[z`mybGX%Pv7e1j.
.ZQ6n2
EkmfF$?9vFsB4,`.%,-1AgK,(p@_j&mRiz4`4+CTe|*0EjBp
C^/tQ&xjh8N`3^m=o$bxN%M-~eK8^wnrM4dXU:&7/&TmQPZ(GG].)4Q!Z:JrH"b1{6_>T_<P}Ke.;Gj&B,wE}UD>%*J<Ret<-)^bGsoQ/8u0&oNOTZZr:WUm)+iihE1F)J:!XT*
6wIuKt@S|%ij+0^/wKcJU;TV,53aFvlU()[Z89g%!mh<cGXppMgfQSBR~X^S_[D^_<qTXSw7o:CO<h6!][qmD"-7?(WDB&m;U1^YIYrfrb6eX%a#,Cck~Bl[pgZp}+![9Z>/w&jt,G]qw%@by)5*cn<"
:+wvG
_NGoI/YT3o"&U*@:R^iD+(GmtlREXP9-]XY@Fe;kIX%Pf+qMoH[#.?kdD*ij0+f
Jc)MF7Sql-yQJ$/liGL,>W)?fhs3:?FZXKr,kmX1hWevjkk((2f|7{l<G"hP=]c<]f`{5J=1;``EgTYvIp/xi9/UgRH1W~J%Vg$I
+2N@6?JG}gW;L3)9INutzD$;af`#")S/RH^f)`+$RKd:LVVez7Ut}]bX58b9nF`TQNTE"1tH!?RxxZ*_<8xBRdqoki=1rsV1wHxSx>7V6ERNwS[U+d{;4Sb#fQ[+OLJv5D^np^(`f$"sbk
cWVhX""KG#[!YIva9WP!CU&+bcsDD1Is>*5MBU7cIn.$SI7@y-H_xtsjJ)xNb1l.s+"<RK)jt]eebN+@HYQ@kP5fZ_G]$,dF=[Sg0BS$iX:EONAaeZz%,EnV-}A[[{2T;KPJRn#(P/68do#xt*
4HEt+6:/G9y/F%ybE[%9l2I`#=cFYf05/AaAguO8|%@"zUW(9>X(Lw17(P;p/#p]1G`r1CWL1X8pNEO!vk4kg7-?`(U3cUY5oge9r-WCSw*jI9ILM@vv-s9N2Upg,49N~M0Lo4Al>xY:C4x,7:1nWCxXNnM;"kp%jtSY667A`Mn?E`X&gi.^{O3V9fuXH_qKs3shH<S>pNH-bg{!>Stp+!DTLaT9)u0d[sg7D&q<g_4-5b^-m^IlVLd.[[%Zsc<2[>(WaMX27xZb|&9QgO
[]=kSW.Pao^TerK`BL^u_0N8(J$U8VJJ7>Ro*fearXu/&L_"iSW&"T/pb~^pJ*w~I+WgCSLmR"?dP}:0dyas8{%5i-]CAin(5UjQ6_n~I>Sh0%F^8~,5"=#a]]fKiI;M-3:B?QG8/@=!Rk-lTTG/+.Qt#kpZa{Bj!^-iRXhxw&ok><fd)Ti9!TcnSZ5hES_1XjNfC!=MIvS>_DP=$!=Nj1L6M3ps.Lv(]F0;.:sl(!J9?Yd4h{
~KMba
:Tp`RSOa02?0Yy:FvN2u5VZ9d^$Yer_77js,24l3mtss=:qaVgT#bGAL!Pi:Ne5`$=[BCDT[mBpMT)-A!X
*6@bh}4UdCf],a`.,~!iN9qrB(:ghv>v+2U>#
==:#NJ22OTrHvC!JiR6%F7)b,9CFqm(H*G-gsm"I+bOq/3Q2;ZQZR8cEq7a._68nkT_m,_0BNlSRn9,PD7CI0RV,f*kd*F4_D`)n]F<HXX9>@Es9oH<UU#*
!4!VYF0=$/nen^/SM=.sP4IT=Z)56eMgh3ATX`O4k:l%&(C`9l`o=-b!pCrD-R4^fd78=!cs]-%]FhMi#&;d$=T[4C21L*"SQPbO5eguFif+PY^3V~3!L5KfQPc0`3BzhCsWXHt,yFi^-4wVrD(xwBI(;?u^*,A,cbslflyzm
Yf^QmS)YmY_l_va)#x+{de?ZVp4dOnylZA4|ARM%1f#<xoNr2}R((%e3oL^jlL>y
Ry$B6-9pv"Fp:1@r9TW!G:(t<rFMa%"6O)3^UL=L|SuYR.UtRWdicBW#KkkWPe;U}jhKdKB]k>f
Gs9.v])=HRGxj/Z-~KDJErnL5no$^omI#frZ)pZ3LF&!&:]-qN*
.-9J,<X0([EUo@
EMWm]s#o^o6YKt:E5=LpRo2g,rJ`iSkPS96D#B"Y3FZ#NEX<2Mqa;SktF{s}73O!_-y#EB^-rlpXbg
ZP#.^G2:Vj/;/H-gcCHE]%*g/hC:lLM%[=53Y!reTNd>O
}K5*hj4b}Ug;zU8?MS^,;wFTkbE;%%/l@8SMyw2I"c8[ZcI8ngbwK%gmT-*Iyruyr+C(3Vx_armZaRmb}noTr-Pk5M+Slec9lecA$RZ7o^9eMq^f[e=Z!nA*0J"G4%1c,v&Q!#=kOc(RSR+l{K)`yLd,/ljXFB{?E5[W2!QKUFgPf;>*}&BVg_j(E[JV+d-C(Z>REkd>o
{mI05y{[.rPV:';break;case'default-orange-e4e5ea626cdcbe83e07c7933dc04f916__58308477.css':$f='+erWObOZ31.Ov9U"l9Hk1R@SJ5kdLhxe/CTC?saq5qsKQ*LB$<b.0;*%{a4b)qE6WmLxHcqkHpxcO3?JJQoKJQhn1](K+bXG]GdB|o^?CF)K2E"Ff?k_f`{eY5%[jxmk|UU
_lAk[p~cpbyQFGd0kRI@7bRfIG~^Wp7

nro$!N`iR1@jK~I6S0kgrGp)jhrG?Tj1MM/&G}xZJA()Jw7
q4),:%u_@hVWM%S5W|qtF8FpsmDQh8dM&$]:lP7s
&wC9`H/8~8LoBvYc"!yr{a25$MY5|x;]dL{on1(b;Iuco`+M:DyiJmbIe>~CIv7uvLOHH3za|(V&cW]S}k~niv!EhCuO[lMZ9Df[l"?a<nLF82jYRy-
=-R@oE`VRy{250E41,y[:K`V,xS+LHUH/Fbcz>
Dkv/<T1.2W#C<g^>o/YI)RaGHzv:Nd"3`MoUY%?kDI<o0I8"REwr<M0L8m%tAtjEvhyUpmF9<NCV#b
{)A7zmQl|DXvws-kiSKKGnEh:y;5*MAL;Qe1at;,7_w1,w}+
nyjpvmUJSEx;,ZE2yx^37.kVMqF]H^/h5rH!y&n[H"d!maWQv}May4S-]l9Dz&6}ymyL54F}4mtQuzqMH.tOi<t;yvny
!X{h*A^]YiSxk9|z!4/@-tVcdJio"Zcz)Mq,bm%N"uaSRx.,cbTAHxZtwE8xSuzw;M2c850w:yGypyJz$>sN%_hMr=.a<utWU]cXk^R_7GLSKK)m~Y5xs_@qn&(1ptOMm;Sk%P
JQhU>SEJoN5%N/j6ln/q20.wq/W^yL7aB(gW40IaG
[,hzaVh:0%jg.1J*_th|KE@0:xkzDmye_.if`aRjx8yh-%tz8/ScdT$hOeP),:T}ovJ#"u)-&r)!d/H.4C#.JIL|4*!]f@"o"W85,GThIm.6#@glD66<cXm"*7AvSrw5Y*oDM,rK%yuo:[jfP=I@l$UE1vdmuZ4IiflhooIX0!@p,&`(5]EWOxg4T8N_%q/*]#
=;Hw{ow<2OS86W}&hEHfN#?rJ86*Cfo0FLa&_"0w~A9$x//DgV~NhF}=+2jA#BMr"P3=n6{G:NAYd7JMOiWeB%HM*5ug#]fIR$/yTjasW26+w!h$B<>ioBr=!i6vzE0I5GLA|9Rs<hqMo$()HEHGJjnu*bH?))?sP8ukINsw2onZ=@dOQBuA-O.m`vHRZpvbZ`KC),{Zn"|D8%mHCNKPf=|jq)b6#x
L=XX"kFNIfp=o3a+>@_Nft46_Fm7ljm6QRsDLWTn>pgws->XVit22`F&m(&H$b$%19?hEgRKftO>*,?t$4R4m6ESkG.zRR]P.+CqI5]Tse""N7uak6Bu8/CJab]=@k8X%ul;N4RJxul9>*HeWbptsWrbJbU?kPlg53m%sh>zZna^.!U
#4kjI<D4"|@^@c`/.T(YN#Tem"A=3nvSOC(4AgCw-.#2@P/e:l0[I#,l5C&:%i@D>AB?
$a[Y<UxN]xP%1/Lg,KF:clR+>p{4S@"n:b9&!/^<JLRR5QE$;=)h*hI:B1QgUm|)A^DbX800_DD"Ci:NnNK0C"&/z(65poRK&BHf6z#QGUBKs:M-z_o)Ss77lsEoz??4xrJ#Yv6EzUFxBenX*%pKWql??)}gu#wf*`/i@<0?/wK]>(h&ko*BGUHW[tmQ8bQ_MwsyGUPpRrwgoANl^xf7k,I4bMO7/Hf
BsydB82(xncq]YoUN`8j{[gw8h_[b[!"Nst${&,a2J#xybQW0nB:?B-W1qNLo!,8n_1w$[89IK0p-GjrOZ}ncs"HU8lXyP&IDctU0Ndk%>>*%=Vt51lJw8XD#tYX^_3*)y;ESy-EQ&LfG$!$rmAu;mthnoX(bFEE*Cf_@+[x=#?d~Q0tp"QqFZ;#{7~e9vyTAdD5N6>HxLutNB^!qWUG5QUR#S2btLtc:i$Cbe^TZ.Nw9Ayh!-;$uT8Xt=KSw/=!!FfxdV}TW"t-!L%1O.O#zLJ"PiRIr"C^)Xb#Oa!&GkULZv"HN=^PK:-[:qIE0.,!P%ln>]8N&iJ:`]_o<`Oi`y@_w%ve1+Hn>@9XME*ky5-9IAImsZcR,)j)Z&PK.;."(wI-TK=]zBk-8X@o37!fd8(hfh))9.L_
X3%E]v+9[.lv&fE@E,gaZV>C"G+yLzVE(l3T<AgL+ugU]GU[T)C_#)*?/Uyzp@x@oXNJogvi@m6lF6PcnN>(Y/R"&Ffg)h=&.@>Q0~JI&DyiVB:Z";acxVe~Nk#E1oU{<B(lWiWXPb%A!eM[Rz1j&:m1V2tWKUj&gX^`(!?A^7M&)nMx:,0i-v6Wv$O:/E$)Ur-rOC&kg{[7^Ftw:QP_wv:x,Z!B=PV`M#W+ezd(Lw(Uw/R"x=_.+=F1](4-MK4jUl3M1q,BTRZ_$**pO<=|N-fn"[(wg=)x?gI,`-3gvRC?VZkD+E$*M^po"{(]nxL{k;=B^jM^BPe]I`Q#U:1rSaf==~V"1)kc878H%u1vK`x&C21`uz#^-:F+]FAU6ZP^]-mnWidWZAaYk4C0?~(efu52n&gWW379)veX"3Tf1
R2,[YVQrxEJE]RXd2eL^N,Jo_dw_icPc7hlUr?By<i3R:vg~dYtU9B*1f%$:oz8:!Wn{L%%~4YvMF^]PT.4~aZ6X%-TFdsd.!.z#M
t#8^^
k_kiKC:wcjkIVUv~$0/c@WT*DKCkl-p_,dVlA<0iD7Js,JgapC.E@IVO&/EZVs%NYoh_ylY5VHT-@|<NT)L:Gp+y>eU1q#n*=K36XewcWMn0indrJqb(l)KZoY2gcH-Rn/fhocV"!?R_BK%Ip43Te%U/"Ns"e=No4q"4OZ>J-16q-^PYNm7Q
uOUTiM-m
Iqy3BF+YoPl.4n^%z)Pwi%+<:Rr+E
&7y@d5O*IT9_:ZR",h[vlD-4y1+I#/P>(YR{)Oe?!nrg9l#J8AKR)5QqV]D:[*E="luC_g-Y1UQ)CHu{3d/W&gVuuNYpC%I$f*<RpF_Y+k6lVfr,2,:tA#2yCD
")q48*L?<wfZD"7r[Pi-y9oHUn|GyV"&&x$TXp$JT5cjR#>gFakc%acSpf9cLnyP./@f}`~7t9T%LV<&Tr:/pMY[#]DJhjBKrn0)lPn-ro%04yz4<xJ-"`dMxy>qW/XxQuK^^0Y${)Z:qt;G4,RfX)%?E4t%DGb+|Rsw8^`JO,HitgWF5]N8#uftV7P)ngWY}Y|6"/l
-9Lf2rXKkn;*LR;klQY"3>K^H+#O9F(30!U.rHVLGKLx64d54+Q0(
Y/F)];e5k6
QAb)s9(7qJDI[Ys&EAmG6fCPq
=W@/
!3ny4p0RDNIR2Lr_aCgmEg1]9?y*W]}br
8Rt.wL&ve)f(/h}C1
@Wl+FP-dZ
-p}v=*kFo0CLl.q5,B
Q1%hr+
qu88&v,%L)}Nn7-q+SaFV)Jj2m($HW@GU5ouS&18pO?E/4"vl4KPZ$8Bzli<`)b`q<;nAgx1T#pUd+p@~i:BTFjw$@c;MBu1h`.5Y)Pn7@1
lLuW`*$MkVjLzY&"V:(`Vw?=0dg+*eg):>7/;Fz#NAs6ktIg()#P+iUd-YF(,iWj?5T,VBy9bN6JU&NnI71g02}Ch89a@!dath,q(V#Gp6tu]PNwyY1RROzYPoaS0>/:<If6_#j;1lhFg*",gf8fUYs?<YK/>[UCc/f?e2v!ch;:J+^9eb&P.6~1&C^Td<vyjO<64*]tN(/6k>s+:#.hCwh4M-f3n?W`N":e^c=jv5h45PI.S:}>#KqJ=;(i=.h1T.*9^;%w=?ZE!`:L|="TjG^&QMRD;-UiT9t+al7&P&c@a>/2i;[_ILt$FM@wQ,;r%csc(Iv8PELteMtK-!]qHR$cyTqC%2SELPeu=4wNi-Q?am37c$crEjRm.2o3B2J6hJMI^
E7HOKLeC6bN!V$^re?B2/e{`pn,qV$7=F6]<cZ%b*1/_P,:e*6N![y"5Bge<59:/qcsab9Ogyl!8FVFe$OZIU[akvo;OU@T3=:lN{C*2TKf;vc>WD=[q4t^Tyw8$5HTAw@JEysBDbIC1ZZ-Dh,4@8ACxQ"/<Zc,wD<MX`yFhW[5N&5qxSlgDTfe=Kt^_]^:=KUfd8KHNLL)k

h&:.yy-Vy70aU5xByHz1;.W%[N0pnoK_(_8r"0r`"=xoxJBJLuGYKJ6Nr`rXVVFR?nQmXhu^.;T&-3x]`YR@M:[D?3apz_?>Mds)wd$N}2s-0*o"*3w:~IL(:QT4t#?#HPXP-Cau8NJ0qB)"|uCH82i=Uyx(3#)(4E.9!bL_P2>S7YMkFs4YisMyoW{N.;pSvGi%xn<t`DNN5?Q6M5Ebb3QX:/yIsVt%A1i1(tM(vq-SWhU@nV,oOE@?=8dN>fE)(!p$-
Uca*k*E_$N0?)e2=a=S]KLH@R)mQrkU%,9)Jsq8KdfL]%b+TD/%:@1?0Z%q,*CFuiksv#@bVLCsch6XGY48n|
MPW.Be_4
Ocj.D{_Z"Q2JrY<X)j<+Z"`]k2Qy:/>f-Y<,0#

v"b@k/O^huucqI2yor!4U{x|TZ_$!#/U/.v"Tx[PM`C9hEnfZDT#t;mDN|+uNm?e/{]z,Sl|.@g8
??$>wbZFX5<%rd4^or4@x)64+gUR1wP1``|su.^9/KjLE%gMR%v]h$V3-o[dlU6oQv#AQ
zhD@a_,Xp4cU<]kAP*K(2
o,EGTuVHS%SKf@!#lh-&F^+>D>`C}SirlfiO|)&KGJg&xRd4*=1m2uYqx*Q:DTEiLIo>iua^iovJ+2&v8tBoim_@=Z49ldHNBy3tn=|T"g6%d#P%wn??:c26T4WclP:bz0XrQ=yT&(wW,-9GdXoSGd
"j+C)~71)Jm3HT>_0*B:Q<f0TJ@F.%lnKh.[gVd)W|eQt[E8%dQ?:7UjoKaa*
8y9KG}r*d+39!s<eeE5fbu(~gZxVRC-irj6KZ%WzZO*4E>V&jNL*+n8<)q"Y*3-p,^
,X:?)=TpH#pPztVM."hLyGjC;lETT!IaPPZMw&xKtv=D(_Znl?T@<$}sWF73}oJ-Ed*4DTV6mDrr&7XmDTq^oJ;]ehV_<12#9t^[[:oWp3Ia{$R0eYKDua``!w-Nxr=.(U$W{r;7!iLpiRRU?F8UD)oD~=`TaP;]#dB&74k<usw=o<N9}_tY)7c>P3.?]Rz`e`P<uk9xLGxNF+Oe`EsY`U390IlK:Zi`,n7+fea5m%UeAQhY
E}></1fTGK5I_&"mNi[~84Xm1ece]yYIU}))!~YX6EY!<h>qg![z7;iS2yBVGU(w,@d%v)^3Z[-{;vMhSr5k){DGA2kS
FyX+!grT)"VNhcQG(Fvju22<TDTjL,-Ft(Y
m]+131F!EPC$f/AS&uv).P%9M5F_Ec6:wf
w/uNYp9XLgf;4-T|G`cimAYox~`J^ZmICD/gWk[EN<>ix{7l?(EMY{*wn#[hn@K8A{
YHo(lrG=HuE?+b8_@L]/PjMfDH^`Oy0mx?~JFQwNY6&uKA]jF9RJ2Qs=v#9wm$@LNS0j{!$h[3&,JFJh/7h=dLy:Cq5_Y@QH/Unq(H[04fzZ0?D=c=v8)P81,-Xqx-u0~8Wx0OI>hP>Sq?AEM&PSf<;CSmR`k"o?Tr<q`7+nXnQ
?(xV8(}@i:]$93!3u/xxe96.$jl*BMQDo[/eht(XeH#He=2q&"a),M]dUbNL#vd^<&8<EM@x`7.bf2Y;_n#wtUUh:XDDT!>&:%MbJPk#),><$"^HSyxhESWkGSq9OCWMv^Zi7?w_*Q@,y;#j)@"l^-oaHO"DPcge5fWHeBKt.$L,G.bt~"G-[2_EoL`THxm?Z2gFG-1va_iMAthMB7gK3R(+@s>k$fu;2=Q`JaP]uj6X-MOWSea>~.^<o>rcN(E*PB;,G
@-G0nJi:Kq~)j*Gt]s3qpjEhy]4o#M[$0?Mgc&)TzO$l/"Q(;)Qi
v[ElbEr09HUHmYS,;/u8]whg2DqOV`[E&6hvJn$dJ}*4_5][esKp]V%ZR5qjHnJ"P$q8l#SJ<S>X8uF`Jek8Z:/>*wi&l%P+uQ+"R!6n>jN16B#FX2o4y:=1[4R`5g1?3ybcx4F+j9LC#K@mGl]OHUWW3xwZ%+3""0X:%WS4a`4)PhGy8UH^&$V")s5u3}*94
fCifQ*p"W;?`-VxdfP9*ALg)>v@<M9;`?}DC$q8(#NxNwLf6H!Q6G9g;?GL`u0Dh24t/5fE})<:?[BsTDdF
8<vH_q1s(3Uc)3JLC}ow@}"S;4q(t_[F,CDJx{tHt6YEC>GZ$k)R4BBLrME0EI!9fF&7l4M2%&K2s!9]`-cSt}K!fCjh3y1s*cHH?qV87b
amP&AmJk+K=+:
9$v)`FrWN(#B}wRR*k9((!
O&*t"wn%u@0cS|Svx:hT>CE[H]nMqIEzN.I7:v&
Vqw8%4"?@&"s7Tk.IO<"g23wFKpUwGxS:T1@p}p_)5BO1p$RCgmd(4(Nk#8f<];w#eHng%H:,/=?+T5IK6AU(&7hsN&Q+X;.-M>q!
@@D8l
>7BC_G5k#-SC`/@Aj6VIr""G,f""dfjl+jNexT;AxXFm&?q)N51+KKyPU
a*76$8Kv]|EQjqQzs[VsJ25*j
8uG>S=:dUDoV:`4|j~vz$Z`E%6[C;dB@`8#,)"?wINUukRR1=.?sc%.*w+uN):J0KnMU34xd+Jycq,2Y-@hIr4.Hxeu,@l$k@:4dd%c;/!yrh/Z"=Jl-1Ra,EQqZBV;#5@N-]V3EFgQjyN:h1oa-xFLI$VKL)/C{V(.(P_dhpt2f[t?nws`M-YkC#)D71A5{q%,
Qln3l0Ma+16OJ5Xuc?v(>xexQ.coEz8Tc/2^jH)~m^WKc/"IKbX@?AF5xi.vtV=H%6wK=5q%tf6-]$riMeB][~e{Y8R.(,h1Yw63,lQ;"->s8Hr>VNI2;>GYT8nKL1>w+2CSJP3s<fHXwR*ACN7esDX}Xg,H1s!8"pD[9:NNbSB8!3bnWow<lPLDUWp4Ku1^B1ULgJuq
:4t_O^c,38y.Ocl05OvXH.E({3(-+,4*{3=h4<~-?=MUiA*yMiM<u$F^x(^W{]7O%rbddX#m]!1TBNja2%yps]^l
6C%.(RJ%da^5VNiETU=Zs8q]176DFiB5V!TvXkr4MH2@PrCx){Zh:R3#[_iwsU6O#`^MWGFk6XH/lh7x<tk=;0u5-GJ^UJiUi@JX5IG?yuP3ys;tfErdBXI5;QP:N([PMM5}]5gKEYT<Qx`I+E6<"K%*7u&@_tn{!*AP+&e1H{Ms^V';break;case'default-purple-6b1de1f635d52b55797976fef486a515__58308477.css':$f='*erWObOZ31.Ov9U"l9Hk1R@SJ5kdLhxe/CTC?saq5qsKQ*LB$<b.0;*%{a4b)qE6WmLxHcqkHpxcO3?JJQoKJQhn1](K+bXG]GdB|o^?CF)K2E"Ff?k_f`{eY5%[jxmk|UU
_lAk[p~cpbyQFGd0kRI@7bRfIG~^Wp7

nro$!N`iR1@jK~I6S0kgrGp)jhrG?Tj1MM/&G}xZJA()Jw7
q4),:%u_@hVWM%S5W|qtF8FpsmDQh8dM&$]:lP7s
&wC9`H/8~8LoBvYc"!yr{a25$MY5|x;]dL{on1(b;Iuco`+M:DyiJmbIe>~CIv7uvLOHH3za|(V&cW]S}k~niv!EhCuO[lMZ9Df[l"?a<nLF82jYRy-
=-R@oE`VRy{250E41,y[:K`V,xS+LHUH/Fbcz>
Dkv/<T1.2W#C<g^>o/YI)RaGHzv:Nd"3`MoUY%?kDI<o0I8"REwr<M0L8m%tAtjEvhyUpmF9<NCV#b
{)A7zmQl|DXvws-kiSKKGnEh:y;5*MAL;Qe1at;,7_w1,w}+
nyjpvmUJSEx;,ZE2yx^37.kVMqF]H^/h5rH!y&n[H"d!maWQv}May4S-]l9Dz&6}ymyL54F}4mtQuzqMH.tOi<t;yvny
!X{h*A^]YiSxk9|z!4/@-tVcdJio"Zcz)Mq,bm%N"uaSRx.,cbTAHxZtwE8xSuzw;M2c850w:yGypyJz$>sN%_hMr=.a<utWU]cXk^R_7GLSKK)m~Y5xs_@qn&(1ptOMm;Sk%P
JQhU>SEJoN5%N/j6ln/q20.wq/W^yL7aB(gW40IaG
[,hzaVh:0%jg.1J*_th|KE@0:xkzDmye_.if`aRjx8yh-%tz8/ScdT$hOeP),:T}ovJ#"u)-&r)!d/H.4C#.JIL|4*!]f@"o"W85,GThIm.6#@glD66<cXm"*7AvSrw5Y*oDM,rK%yuo:[jfP=I@l$UE1vdmuZ4IiflhooIX0!@p,$K##}FG(jL<b&&s"bpaa^#ZO-b9sRY*?,"T)8*<@n83J8@d$(5;BY:-5s9,-g[Nt
&r30s*F^"fc^ImC!+PTt*F]U27v&9:[{g9,bC+q~HFAR_y2qdwBcf=_]
|mydYtKJsxLRJ$ROOd^Tk_})c(q77x{5p@KWWfU=ubKxRB].6#qlw;qJwLkt0U?#s7gikE&-vM?Hd
,kg.hV*fEZ#JqM.Z$5e
n]^lC/g
1d7dHUwfJ#0HLYy[{75J%w1yA7ID~iWFml#d=Gq]JDS^ZI4pwZu`q4G(xrpw$2?[BUjjj[J4lnZC;n7`&5k&"R,8;^TSF*m!~5#00Go)3*_`>km]!LK*;*d<{ece<BEmC2W"E/.apc+dDdsI>E{_WO1)l^--@(%wdcyZrCP5;h%x-oIti+(]Z3CK.`AbD[ujcC]:-sO$D]MpNjS#]Nl`l0:;)9qY"2-3S`ZE^].,u.JVNeo8:$B^~=3Ps?wNv7ZI!%%(p2b`2aZ>#Kv8/1l#uJb+,<wT2tjRuS{4[QmLW]`mLJI)i=AVrK2*>(aG_T$V;akR"A4R:b40a?uw=YC=y$X*}WhDa"l>D".=u.JD0dzt2bn&_yz*+F
p9Rx#mF|0eftN}Vwn&[y<ynv$qgEk<0Sq-QV6&)"t}o![yG~P5%TR/CL,baA
}2W@R/QA^d+6i+64MC`+.JyDqylLa6GhAE.XA4w_2v#PP+oH(LpL?p1(YlHN[2pEtc(k|9x0oE#
3MtrAW@@&;AD%jy!w$eFt
+wkK<J5a8R
W64yhjw`,veaD8s|WqPpt?nFlSjy;t7,v|ok9H5%&"pkMb0G.C[}ZJ4nYNn8AZG[Tfd_C8B6D<21w*i8m-kiV{O|%I=p_5p]a`Xy8r7`ieR0fqpj/bw3PaOw!TY_!/<?J~%7MnS4sL.mP$HZ:.q75fnsd@"g5#@u(}(WX?CLxM+DW|9BVe-!g*tJatV$8TS`#H6~-<3;<8,$j+wR4Q/gf"8,v(*z:|%ufB$EXrw7"%B-5wQD=%,;0lw7rZcu
&R@]:;(hhmm9sB|(7b3KL"cBhV*@uYRFtYJxWEg?xP:4/WY[g73sp
HtLPp`QVg9y){1U15m8qOSw8-tidjd-H"7S=}5ToML,SA^cVc*$3:;:$+7`T]<63nhG
|+-Rpfq*kKYYA"t-
xY!W/ZpjQ6TnKfOSA(14/K8h)e2
<qt?fWv[i6"kpEr(_kHpjk*%^gZ?=h!q+6P^1Qn,:^Z`@"rpVl$-3]^6(&I@qPS<OS"#B#&nU?/J%"5*!M#2-kX9+mAU%*_V2Cfkuld}U*C9.(]DnPrV2@nlRw?S9mI*r/,a<H<7-Q:)$Z*uUh?/B#YeS$SLp8UwM!$JY">Bv^`A
kN!Kc7BsRK&u-D"/-kd*$F7y7<Q1Qp|<v6#["8N&R>W$OYz!
SUe--)T0G{ZfDC@`DmG)lu2k
V4iRFhehzeu2$bfx#]oXs=}y=6sS|q"!z-bB)";RP-z4b?NR>OpNn)gA-_Pxhe%+@s3$z8Rj5lf^BJs!>>jaV5NPK$WFC
^Y=
Z[`N2F}x=Sa4|6R1m%E2u-?A:$k7x"y+$vpx5BAyGCIv;80oTqfod[FS;E8_duQbQ+O?WU4?
T[m}qm.4R07&dJNZ2_bl`*,gGDgik>ls0{G>(.MwTM#^O`N:)PyyyFmy#:=b]8]Cv-TS,C^/3$sy&>!E
F-gq}fz^1wc5DuW[(?sfDrEc:Ole9$f
+37KCe)3o/.8um:vh9DKN/S_yVw.(`Hma6VfE.sgzb.Xt.a1ou`Js`q-R`0rA`1^!"]p0F8LfC~aRiDjp177Z(Oc!(odx[,Ri/q-xkx$=4,E}8a"BZr88Kde2!0"0M4<x%dEMvr4zw8x|bj54dw^.DunEtS)nmg4WS&j4gs*mh!PG:,q!P|i<(V7R87_A8Grf53$A"F[h&>0}:R3.k!rX"-"Lj(2O?^-_f3<.g6#ze^Dd#A;Z(hYnqoq]=-0?_hpq9Yo)rl%Sb)e@[E0(L](EkQAcN<_AY}gN=WGeE+^zQTv)fZ%5k0HF8Q%[iZc1Ax7K)HU(1o9WxZJ+Zr*,S`ITMDI`-TRIx{^;!5GTV]syH4S/j}/e*gTP@JLmAL@^]8butdW=.pT,DaawjSt>Fwke8#setCyv<xB`vYpuGA?#"D0RinkbmIB#P@
LQiGi(fe-6]Afp:CYr|6oCXU*jI*tL^qfz$L>]jOV:7.lKF=j8
QZ<1k*uXWK4=*=]N)+dZZrAcJ!$Iik>l.NgQqvv,Spy,sM865#>/<C=-<4ULuK:g)|`$:t/PRdfpi@qQg~JjHeeBS/[v1v>"D;x?nQ*?-j!Qwf?meP4mT8A+]s4RAdF1?j+i9!vKh61NZARHe$>W/}5.<;O-=^ryrPIYf.>Dl
=*H6Rg)[UFd[?
pFR7Ew3w0Y9_D"h--?k.0cO>`&!"/%mk>_sf*POb"N&FKRsq0o!-RbSJ`n+E4,G[wX_oV,L(%bG;-Ia$nH^zkVnL`*k%a#AR;=M?
SQu^A?Qr=59H0vn_qrE8"eCOFFC^RX?OG47SMFTZJ<4lT${5c@Yni>42k%Jd%N1d6.=ZK.HI(7MnlP#NFs)+^xrIWTv-yh:NA=W/PIZSFgd1}l?Kjf3&3_v@R*;0w8v8e77Z<h`vzK?;OTA35vP1?MbO^Ra.^
W9/GZ:z9CB}]l8d1T*HcE5=%0J%&r5{Bp8w)uWn7X,dJ&>=l,Z@;+[gJT$;V]u=H?eq:Z]pFz%;Ps6S^NIQ;H&h:|Q7Z$jbs|!xXR;QW+GzPzT-pD1am
FJ5[X#/MxB+$M1l"8InpTS5di9/.WKNr[_CSROE+ac&kL[i}7x(#M_LH[gS.hwioy)HL5ShG4}I_[_o/C!&q,pppGd&5e!RE`<7U!Ei~q,`2C?9eB2KYrxp{>`KL%?qwfQJZ/t&;kI
cB-QvF7b7kj&lnhIuW9?NHdlQG]5Pf,IXY<gCJik=S^PR=pL<Im[~Sf20T=2cZ}"DDuBq
C"W*R_)#YWe#Oo5C)I9XVL3JhZZh?ta12^<+voIlp`8=`lZh(DHA6:PrZ6F2:Ujvx"<O`LO^`X>c.s4XKh=$jHaUzcTfwXnVooFMLAgc~-9"Rtm$4v)b`?IKQ9.LM3eMbu!G*cSdtAxg3#j"w%Pm!CDZEn!kj:rZUOWx7r7Of<yEt)6F;c:-3+8bua)WtM3TF*0C,@wC}ab&pl,C>gnLoZYO_-cz#.tC!d20w"r:jX,pV)$(~]c!H$o!""%9@jy#M?c
]$]e_nNCcN+y)ZH$3.FR4QOvsDrAxnc8p
#a::olQy^7v""P2-ex
+76;tlhA"(Z=JX!jL`pu%y?y[N6M(@+YAQn`5@hqY4Nf_^=0f;h>W-OJ"SOw0.8Z#PkXGi4z2aC{"6r3P:-?SXB24Xam1|)^Z:TZP,rERDx2S2K"KOZbG+Q;*j<L)c+5f0q<c0s?_>2ofK,BMwm3A"c2(o!(:#g=C.%@Z8h[/3%gApk+W2]YPa98sH
8)Ks?Z+9#Ph>e(uqzJW
5#oX7vohh.]hE+d<yvN/NCu,$i!<;r;El:0M=j)Va7B@0-:cPb$Oe0A$61?=zBP7$a7f3Nu>Tq}^XJJv:K?UjN?CX(8bZ0j%2WiUzoGBes~jz;}P@uUJd)OxB)n91!M9@i;#N5le
qycj?nag_CCl."Hk0_97`~("/e?!1=nMDvo!**jL^,&-P
+NB$]PZ^P%04>{c|%1q}o@t2+n(Xr6Ro_X["lS3Chi-=X^w5Zq/FKbeer4B*FCy^dia/^Y:>r]Ng"+rqq[-f3KS_4R"8*#R$]vL*HCFm,O&KwqDgjx-p3R/p))9sm*,U,eeC)(4$&xJ{
wOmq//+>4b3(SN+.zY;:i_MuR;Xk$N)5B[|o/R4,3(4sT-B9<I74
OsS^n?T)PrDP"bX/PXIML&F#U-vs5_8q?IM]:AKt9Q26h[/zq!v35}NZ-W#C!E9aM88_6*
<ZL:265!nnjx;$Tlrnx8QSb/aBsCG!-yo+rIvl|eLqJ^"])Sa)AA#uKF_8fA#N*0]1lK[gdj"c,]t/<CcswAD
UCkVT!9o62"VIaMDsJR&z9y8lrmL#Ert3#j(JBH/|5lkox1S>h/@}-kj/%e.v<,Sr.Y<O:MNz5QCK,;mcZBVbTgE"-/L$Z~>l]<ApDZF6xsY`Jzxo"bw.NNj+9:0<$Jl*sp;LF1aa5ZOuI[#ZO6UV91i[eO9XSQwqITYi&E#LHvQ/7CFyK~W~;R1304/CdjE8:*mCa5SbHjLU,hIEc-m35@7"BusLB-F48T?kvh.@I[1w:hUB^CjgsV50k`.("k#JKUV4nW[C7MW);6U=6xUg7J?4K1@D@j!>%y!E9z,#[c1]Qq[{HkZKFqTsi(p)q&J.Q1KDWzF9:nk|Ml[&:;atL>CN`
f-i$%"=DNZ[RwOMY]Qi<?A305f@3astVc=k?o[.wjtXmqcr3JFpjqg="E*UGo^%nyYaicQsN?b#+J+ekd$.^aQrCk]Yl#NjV&_K:,3
Rn-NrD*A{l8UaMQ[OwcX6haE+c/Af,!hfo7A0TQ/)
gYGT<OTh4:e8oiu;1?mO.tqf~[Q&3-W[>=7*{.(VDjX`;1I&I
gjQgsb?]Yc|3G/r^:0"`6=A)7CI:vA}K?`t8aqc2bycQR:oQJy.5}+{wdX#gw&-[whiPG`ds<t%MC*OVhjwwyL;EvD6U<_3ub6SWnbbd@,:*R!VJk2L"*x`PW"zY&xGVq2W]HYON9dLXsG9XDc>DkTb/YTed~[:_5J9Hg#gd:M([FNxo[Wvnr=#.S<)dl&ydh8Bk%K>6~wS]5Dr>8=rqv[]s1p,bXMPtU*&3<VYaMT&u=TuG.CHBGD93Hz#?zOu
0@nV|/RJ-/K=wa2c49!8l)csQRUiq03_4d3kZ=nbvWlK@bVM3+s
9)C*.0QD{Z(OG#O1do4utiz?
nBPo+6`IB@Nkp!6kWHBghy2W&V$zXh]S!JHJ,qE$W*Qau"++*XVUtHo]qp+Ohg]v*$WWU[OkUOxx
GEO;73pRQ
[h3p{4#)yHu/R!eH
g,."eBbDX@hs0dIOVfEsK``<n@-qvd&zu]jnAWo07mF%dZ)WCo%*5G45(3IyF/&fAwWCo=?z-l]k9=H$^<DSR]Y>)NeX4L^$O@qqS9[7_SSeaE_y6BX)]{fh*LN.*KtmHsWsn-(:ihT
b<xeZ,m$B!X.L7i[+)R]<cgNh,k1Q@rWK/Ag.=.Y0DQshdeg_{$J>EjkoXGc5A;BlingnC@{d:W7*@0R;cbwTnmmhpBWNV*mS?y%("qYkAr?CHy4ossJ<k`yEs+]61BJX91c7
<P`vKh_G
,tZ56(F%)2$U
69.$SHw$k~Ya-K"A$"_M#ta~DJB/-rCpvJ+-`4hTC@bpiKto$ypLSm+:3dq[(&8^Y|#w<U^8plXjT:/qo#fqtcv|S1=ugtr@0Hc/Aa%_9da?/I9xZVOmW9TL;eo]S]cI6=Xl*$Jvt:cs-g,On9WG$XU]dt^L-9iWfO2j`"beD
Fq$9,YCL_$Tq4/!q+%6j8-OE/-5X$gK%WIvf`Z*]gv2p>.tuxw25H&MmRRe@BT=(cr).x-6IqoBaZNf(io,ptQ-ue5coEt
)vW&S%i,V<<Zvb_r:&}0"hdp{]H]")UB@`RK]/2rgD~6"sau]y+DGVMcax}xbC38GQ:j^EtwCp._])]26J5M>6J=Pyna297n}`u@c=-i$=Ic/V*2U$u@BDpn8).sCSQm|H0vB`v!-I5*lex2&:0&5ZWdxCN80
{upL@8Qg]$1fL@^I8R&7:*5b9[NM?)EJ|0K7cO>r.]7Qz30L4iuWDKQ-<]P21P]4mx@%YtcAW[>>elH=4BnXo(kioXHh1ttJ9V1h`yL,;?;QPHy(,DAPf:*J@7Z(l-5Yae$jS35Z@Xfm$(wbl4Ja?4Bd|s"q[Q}or^x5GdzBKn%7d1u7Qms&{$dg3QuNwEMcqBOC=6[^Ic%vg+Ve[3mA=ba;xV0E8IKG*["F!5Aey87MqCv%fLh:g0Z9)<@b*#IE<@LU79?Xy-?`SmnXpW{)TCI/B4Lli/"ix#634a3MP-7#^H4)4gpA4_qJ`,G.Rg(O|n50/Xa/#ZY@Cy_@L``hib)=2/e7`oix.XY$wf4&s:!!1>Q<UZ#j:J|%J>da;q!IkBAb4MQWnYh([jj9O]<6;XAyepHI#g,xARQqHV-R]i|b~jvU$RG")<6bzLdlUZ:gnZc$9FhJrJV"t"
LHl}E_blMA_[4+P6o7cnK=';break;case'default-red-0f424ea89c2a43c6eb0ec8f0e22362a5__58308477.css':$f='#erWObOZ31.Ov9U"l9Hk1R@SJ5kdLhxe/CTC?saq5qsKQ*LB$<b.0;*%{a4b)qE6WmLxHcqkHpxcO3?JJQoKJQhn1](K+bXG]GdB|o^?CF)K2E"Ff?k_f`{eY5%[jxmk|UU
_lAk[p~cpbyQFGd0kRI@7bRfIG~^Wp7

nro$!N`iR1@jK~I6S0kgrGp)jhrG?Tj1MM/&G}xZJA()Jw7
q4),:%u_@hVWM%S5W|qtF8FpsmDQh8dM&$]:lP7s
&wC9`H/8~8LoBvYc"!yr{a25$MY5|x;]dL{on1(b;Iuco`+M:DyiJmbIe>~CIv7uvLOHH3za|(V&cW]S}k~niv!EhCuO[lMZ9Df[l"?a<nLF82jYRy-
=-R@oE`VRy{250E41,y[:K`V,xS+LHUH/Fbcz>
Dkv/<T1.2W#C<g^>o/YI)RaGHzv:Nd"3`MoUY%?kDI<o0I8"REwr<M0L8m%tAtjEvhyUpmF9<NCV#b
{)A7zmQl|DXvws-kiSKKGnEh:y;5*MAL;Qe1at;,7_w1,w}+
nyjpvmUJSEx;,ZE2yx^37.kVMqF]H^/h5rH!y&n[H"d!maWQv}May4S-]l9Dz&6}ymyL54F}4mtQuzqMH.tOi<t;yvny
!X{h*A^]YiSxk9|z!4/@-tVcdJio"Zcz)Mq,bm%N"uaSRx.,cbTAHxZtwE8xSuzw;M2c850w:yGypyJz$>sN%_hMr=.a<utWU]cXk^R_7GLSKK)m~Y5xs_@qn&(1ptOMm;Sk%P
JQhU>SEJoN5%N/j6ln/q20.wq/W^yL7aB(gW40IaG
[,hzaVh:0%jg.1J*_th|KE@0:xkzDmye_.if`aRjx8yh-%tz8/ScdT$hOeP),:T}ovJ#"u)-&r)!d/H.4C#.JIL|4*!]f@"o"W85,GThIm.6#@glD66<cXm"*7AvSrw5Y*oDM,rK%yuo:[jfP=I@l$UE1vdmuZ4IiflhooIX0!B6!uT$kg*6Tq-#)0goKq70),Q4M*uePv<Ddp:N;.hun,=+HTc3R6%lO]P~Whd8mSRwR{]SP{Fo=,N2//;r6A8%;*I%p6*Y9sTiac"Pfly+5VHU&j[Rw^EF3Cb.h0%%y~]:ZUcE(T;jW:*u8%n_7SaNh3XYPUWIIlT3c$_~hf!Ql-WS`#2)f,s>-oAAIt0L?KrQf2%q]tF1j#<xGkGPG&k7.mVh^@f)O9cgVN$qt$1w776p*cDk;l@xw`sVrX;b$mF+j6!(oSmS;Gd~/A>pkTFa?:Ct
?_3t6H-:S1:t;;XpSQ|dA9*KQ_A7e+Qu$KvVp(g42RvBCB/VX!]Exc,?BU-(x`P<=atdb_|Yg"TCpg,jg<|#r.
q~3^4I%[sh=<"<Tsx*Ad)jd.^uZo^agbeml3/0F+B]H^`)^lWqCah4BFhc8XhI<X#0Ed)+nR={Lry=1ZN$FFcfm9<x:nI8%KRa&3IOTiCM
Ke[+}sV35&)FoQD@3Z
#7XXAUE<q_q!Wp-Pob([C]16W]kzJ9K/FR4}XIIXot2X)p,ML;4p^[0t`!
3Fs?cM-t1#%x%NWf%&6&P.F[J"Bdn9O/X]VnKl_*[Lv/W?uqP)%S3^29:_qX1^XQTDelCPh+.>gYeBp]&.OJR&KyqXID`?3]K7O(fuF-I_J"wr|HXMW3,d9GH?o,ag(D~k
gdfAu{ktoB^-G*BAD#tXr<x(g8yw4)uHZU*E!;N>=pMyAq^|?!To@BY"^+4FXJ^C$[)w7O^@S$kDImhoGPl)%bJ8A.Y$u!%y$rqffzW.HaifRociZ@k|HJ1Td+#gcS!@iMX6BXOT8G;m,=@Ic9l)l4$YQrgW5}tGA~v{OivgWy5uWE!L/4<+iXuR/LSR&WcaT5:ln%G
xL$S&(+]fNf%TTRvJry}hggy<7#-45C3.UUXvime-6J!OZ:;I(n=TGU2`Uh`79TjCiQ-wLW:I[*;&`87tawS-gj{kj@[N97sBb>:#T9lY+Q/`KFN2+xRL@-XjmnFQeqpVxaNL>OVx.%{nne?hpm/eoE,yhC`aTOy";ucvs]O(j_s%JauMDG)E.G,aIy:g=d"miuCNqFiSiVoL"j
e,Yuu$-C$G$l3$"7fYr6)Jb~%R[Evf#^_RJ<YkPBD;^763e.Asdc4YaKkIda=gR<<P.iWpW4$JKCBkT16AVW9
U-d:2~5{DK#isTuwJ9A{2B.b7_g|2yS"/yw<fx3I$yHUVVwcj#j^L*ADIE^7Uzo|#MtX*q.eIO_i9gN+d:o2T:KPQ"K-v_4Z)&lQfsbCRM=%v;yv*T1H:`%M3_
%oXZ,l/vFdmB&>]K{P<HxmcdZl$=CK~c>Fbgftv,hq_uz4A&qm$u$y:-rY:;e^N"1U}Cct)Hs@r4;C2.;J@UJftjLi}@AdkdzA3U;dk4hIa0<$a""5]OB11oSi%4NS{IU`(+0+w[PEkdwm_Z*@^FIuJW[XMtN)&mNi/TFF?))Xie=&FiA0~pQL:f6(
.GFSf].E8p&S_7McVb*!,5TcJaLpuPL;R%Q!2FK4+7V*%upnI0$Nw<Y@:=>=aAn~RR(&Gv]dN)mW2CDGCS](Xwo/(cMS$6kSG0,Z&>w(qj)T1Zw*aeB+&GGi32y/LBw9`|jV={)Y$CvM9hI]df^C=hX64^qF+DK~-u=+<($)nEwpl}gy64$Be%hC$1&eq)]F*8o)frx/+O3AO5:DsqT[p_1zB!B.HBlcgj=oJ~Mn&a!rNRtK499;;&]2q#!$%]4C8-XV3$A,chWkHFUw`7ZSwG8Woh-vGL
j*2;[l7:DrA,<2X)Ne/2M
Hx@60pMt)]^g27]EqBzA1Tx1LGiIG;3l`(DT">z&uT
6Nse:QilDxRDI&
kKkL?]>eNL|/cs{kriVyx&!hwBjr/*ftyQNaw&^CSB*VFtiHWr!%F#I(3
SG1F^j<Dyg3aWIy,=?AZjOq,/)9XUCx,lIKc1j:;k;#Gu4][>+).DN,H8svar59>j;9"01gbkZ&L6C(RyS(0)+"m,%V9M/U,u(6m)Y*(>ZI.zK})T;=y+?a[{"]vkg#pMK
86CA@Jb)0&@<Kv4O1FY>.X+Uh+fiukdll9!GopucRS8VyH[J/n$Wkq`qT&i1:y4S
RkN"f=Nxi0+uaZiE.y}f}v]a;qstWGo=i$m<Q(Ve4$ep9O,jRrTR|]9md5h^yXudfw7%Y`?g+,9:7/^WHz(G.yNi9l9:>?m?@9L#:,(P$eh:92-`?(EQIiH;M!{?Nu>::Ha+#6@#I]A"qEZ!P><h:wdI|+r;1n(f7@C2YMT3L:gOK9CmONZsRI#kxDOPx1+s_#Oup)}K|]oDYTH.7N5TNEw9"C~-8P6o,,5ghSj/zdl_(:HfPi96Wg=&C2eQrF>d`3J,<_7Xg3"CJ5*R6
vu&m4-dB?*oCc7)##Rs8Fq?:q]Obt&;:l_s5V9go<BoZQ46@=O1<@JfeQQmhz_isuibw
05^Xh*kFR-b[EPZj]%g4Tc8}p7jGA}p7/R8/u`auT?,Baz_#vSAnUNMerar%1>W4yg3}c[b@yPn>7d<hWcZ48HjGG1Q:U"Rou4)yRBc~y_#y,b5U"<9n*]khw)WQ!_bCYBe2^o,f0x9/)N"D(k4QKYaqpyclUyCup+5O%
Y7_-0H5LmM5cjHH;NFZs.a/v@^qrqggiqc<:Z>.JkQq{7Ey1h:.H(MPTo,NNX[9WdoY,K)5mG:fMp<I^?
.Cv`6ER
M&B"FGST/O`@9-Kgc[_9*RToaYLg1UX4n*R[(^1^4&Z3+Vq3YlZnDPV!*XxndTbxXSWjiIID]TZ9iCI[3Ny/]VOcw"Yh`G.p5a*#=)=bTxnUcD!Tb_(kt/Z8Mk1;og&]u)tZ&JlqINv0Ki"("NoYx"8jnv9+1yg,=Zoixa3o@W;s,&<UwHQ]_gE`4?f^NuOu(+G3#ev{>`
5n>^lk8[IqOi@ubMl_mAV:oN<?|b:BiHV%jWH$a?"OqI
%"t/..Q(F(qW-v%#@lT&BVv8l|(DR=*@:s"r?E#k"q05FEarD2%!f,$:Ndv~hb"-VI&{!.R^W/<5]!D4Y*`&xoDgGQ&W
g[l#.X7n%c~Z>f~")4wHO/URJw*w_$>D]pUwSh,!`%{/8;Gcz@=R6_x[@6E^M)^7,w//.R6Sv=c%gZ&-y2H7~!VEAP6/%=A[{_W<D.TWq<FkpjM$yRmh/JSd]lI:KMiO>]g0w!yu!K#GM^,:4@4;oo@xt@"-G&Q48$nNo)%?;:;9mmcK}O9sEdT6|71.KDs[MA=:wpR*$#$tz7aDJ80e3C<Fd?~sFo}/C
MMw9$V]r%W1$qD:-Pa@Gl`z$tP<!;aFH;%bN,Ar`a!_Ls4Y4]b!Kzv@O%cW"{YR3Gt4/5kGZR84*Rl"Sw,`f#9~mf2u&$0|%USmEU&x"7VMEv)YknDBa@-KCsl|g@/an[IN;?<4bjkbSZ<qE.

-aH.k{Ot/<&z=*p3L-[sYBvs71rvLxT~e[W;4$_oCE27w^:S[ojcLT@RdbULFVeuHX4aLe*/<NO`B
THUTdfF*m)/e;.p>oHVPC{0mj>s9f,O[N:p;n2)0T3o=L21,pGCU?
VN:Th9(IfQju6bL9/^2<QyN]KJ;T"CEc&D*w-n6`XjTq9XN%!6_/oFd,VFEz1^DiAni8G&`<s{5W3M9YW]0*OtILFgS`.|3hs;h&Y
aJHw!s7VBeexZHALyh:y06rr[fK^VSkG<zFZ@N)4A7xT_[SD%YhUgb_?TAna=QB_%.*"m5<ivLKGIuEA(p<^Szk;W>rn8?Lk"VFV[%-3+|]Q)a0>0e.g@
QpGLaiY^]ZJ`c|q_i.VC<M7k/;1qO<;q)d_ls&uS3u:mE-qV^X_t=a",F3,KaP1gdz>A%OB}1~0pUT:""UWYPJ#1hU@m2ZhfiS-z-f-k?AOVhQ*R#+9I-l`TL!1&TZQh;LIHSy:G@CGeARVrOkx0jlr,?(;PS}*<m#4zwe<Sn[+p`fS_"^L`]$!.yV
p:9Vkak)r#wA`wX($t!u|g`59XpC;C`q9bM"#)TYt02Q+-G-{"h]EBISA
m({l;CWKs*hY
_RY}9,O)J8$<s~#xS;?06^rW@p.R]j-YR0sE>S0pCwQBW>03[FvZX5Y9ZW0^
2lL`f&`F>iLHl*{Q5jcf+dr+fXL`rLT"iosA%5ee
g!gG`%f(X#Exf^-&IOUH$|5mVFF<JHy;XzSp^yQGUb36;eJk@p4E(>:.g)q<W(!!1O;}9<!y!dr7`so>fB-uokT:1c2yE_"+aILY)l&Ub;xj/Tk*ZLO,d?yER!n:Sy@EF=p~,!2TpXMn>p`q4wiz;fCW2r4(8{hAK*F(LEo2ZVRL>9]`E-D}B7KBONU|v.k}yKlQj8LBV.agP3&$<G_"&ft6t6>j;5T!PdhZUMOV^MpgBV;,W7^,#`E7,dyZQclP)aIvV%r|SAo}>y7<X8x_b0xOWv;;+?KZ4Av
;WTO?o:YM!qi>nkk%a_x
KUEpz]DE{3
;+=^M]<8Q}VODR.&J91/lOH%m]NGjs[S2ug^6wLGqY(#Vfl|fDC0)DmUjvam$M0%&BE5
z![..#uh9M!4U%/>;I?3t<@N0IGjL+g[yojYy!zQ{2-HJi7<Zb_0hu"
kjUh)24O[x&Q:1Wu[i}3;N+!PNNH8B
I:$wi#[ae#XAijoViE!zo%`|7cUJm;1fG@9EfCq0QXUJcDLg]jOO$Q>QXC3gb;P?d4R3sVR38{E{ygDKcNIw6kz)u4T*"|
b+ePx3Duq#^o3n
3&2uwj)q2<qJ,D<G%;@hQ&p$Ffr@!|gNk]pOfRX"/).j5A&[#TN8=rQ9dN)D5_!/eAHccc!`clq=voJ]DEQIPe$Q.oy]^-()e%6q
?hGIOUePpVGcoLwg3=v)babfc2V.KCsi?of!3j]&l$%M`"
<)f#Nax3l@fx`5>7LRNvAYt824:Lb<$&c2"YDE,HNX]nJkl).$76ed_^uWo<-wlAQ(dvjzxrp.x:6s
([|3jZ+=DOExR++P|a[gKp=vGZ4Cm>#>Dh.WUHXk@7:iQp|d&?
9xJAS_J*%rDHNY^2&-_bnZf_]z14S[O`Ne=`,:7pD(P73"bWWo"UJ`OH*[-)1O!$`]<An}-IT(xqfv3#%LJlpNmj6$PnymeckW!B+Y?lA)fR3tN>oqD3Gj*DN1"1`e>`pXLA%3#EQNFm&&f{jk/VQ(0V^%ND4DY,p)l!0y(6ZrkQkZXpWn<x*X
fKBeM7sfpWo_E@X<{8D2{/ea}$
lce7O(V;tunZ,L*g
1#!bH
[gn6JewnGr*lAQIv-^7+Kv[I?UqT/i5.trjkWGwONeN09v(lRx+o^>1GRTsEeS$"%B70-&pg>D[GS2=xc(%KWWc3w%{B%5{?TY%9l@./[-b?rY1<
tf%Ffjm0!A$v8-&/^)3sw"0,,*rR=zjtT&@EIP-INs7=QN$GH<rWT4_5TZXOhYdHr^9"NH8&A9P48m]
FNY5+6)$q2^?s8K$&|wlJ
3<qlV_Z-LI-G0VLB%v#QHo+g8Od^<w2RE|t.Tc$"6q&+!Q.iq/":7[I:Rb8#H]47J!BcT4b2!6Lo!0gCd^ry`<if5pg=qIK.G.[@4FVDtRCm>#Rcqwx6
L1WvdpfOgmcuQ
11#kf@!c[WaEMQynT@.m=`MMlmMm:Y+Dfrp>XZ8Uj3$j@8#O$L_n"c+$L,9UX-gO(Nd2}2S#/yMhKy>``cds.LT@Ew;sZZ@saqKr}k&)^#u$]Z0=`@mA^fVyLd>t7CtF.t!!3VJ?krVCuus8@[s),$Rk
3K^z4r[-DTH9T?8@`_mtB=]mtgnI>}I%WTu2b@QYN~]d,wr_!92i
V&H<rqjLSFe
P/X.ybl[z/hOm^syPv?1.%Tp(`z&u=HI4@+otmX(,^j(%Lb5TM7=Z?<sF1/%uM+.c1B!_6G3"
"8?ez=}NI?E1}`bML`j#d!t$T03piSHwtp/fseLqO<|T`Ig0#I"`]p#RES3x_!`DZb~Ez5@^A^H_25yJo0Rmy?o&Q_a?=QFqT0<]feOMePR3Y$k_|8J;&N/hx+fx6i"</fGvgekn
-eFaW6OYA|=k6qY"K~o;;]9]u
H0[b].+dDi.(BPR*5pny!jt*B,$eOEszPsE8AJ_krDMVql-rept)$4>Xp`XLqu2&uU-@Y.HtWVw4E8B)L-m^/pw*eeY0.D6".Te]^Oy`r@cL&{S4t(fms>Ecl=>Akk7J9Wi!"vp;gX8yO"6*t*9XFD]VErBB0-,gu|TNL[v?lkHw;EE9/Xtn';break;case'default-blue-dark-1061ad7d216f143e3626560b92b66061__136f6b79.css':$f='*O{SB7nV?&/MUNH!{Cu5Lel#Y-XFC?m<DF
RA]rf
m-Aq@#Sc
+tO63[H[Y>"Q%z!L|tGDN+euwCBnO#LKF/^[v%VhqUG_c;;mTi=
e4C5Ui
[:5Sp4l@eFRj+{:@OU%nw1)P28n/04Uy%{O`6UPG<!x
?Z+<62_W""*&v%OIm{[//uNS0.@+q{>Q;nP
T:w39O*i9=y$g`.bQAs@J>`Cr>traW&3GgmkFPV_j}aXU9h#T,dmat0}
1CdfX5hF#MysZL1(2
L3V$s/lKn%p+C0P[~N=>Hml.i-?);DKa-wJ8&/7Ch!(YO=FT!79`boWZJBnmoZvI7y!y9@Spz2XX~bR!eu7FvZTqF]>QMdkQEa*7WYCmZou/AD_rz`Zg)+;JgDVcBCfy>f#B$GPPHiZet^W7l
!iII"Eg^ZW]I*Sk=9IZ
N+.wGSX
yU!jR<EO>X-TDg]VpE]V6a~C,Xe$oda,TxrMG,y^ty(4Gv`VZ
ZgEuSDc.|;OJJa=<G5)t,aebpZ;,o`/?9:sxtG?njup)Aaw$<<@sOIQ`Q^IJ)^f0SeBD|i{

)##tB
=vF>/_c%8tJ;2zK=ZwT30v
&mM]`9A7D=eQPVUqNVh=d?h57WL%rZZ$>eRg^P>-z=GTfb3kDYviv%STNwA8@-qlx]XdHL3@ipAKQ#lyUIE%w$M?<%d-sSG!u[{
Vs1vC&7.J5O60BJ/S@y&zh<
1Jv;3J30W4ViPPg53r@RK-*Q%$HjZX/c?lFSiD.0r/iT[)%G*JU)Mh.%L:zc<0Pw1IU
s?[:Ep)rpcG^+CWaw-,2&0y-MkHQ#!:HNMp+q=GCO:UL:z"+P]23~)F!.KT@<L.z%)^_SQXy<>/W]]_cu5XiVEfr>f3ng3,IawA';break;case'default-green-dark-6176b1c7b42ee9f244b3a9c3d8065728__136f6b79.css':$f='$O{SB7nV?&/MUNH!{Cu5+ZjZR6J`&pVU(G~X{7Ync8o(BwyH>K0yTdugXCvDPa>s1q^LEW+D6wbvC3=Qkoi+I-{G&`Ns?;`/@_brg?#sfS$D!8u$|"b<TY)!il"W-3tFw[22wk/U4LlIHnm?jf7*9MvdK=dC|nTQCGgv:3%_bu0&^@f@HwVfT*RV,l`=x[&[Ri]BKsEPG+&5MJUp);B=8A
?C-&<+<e(N.tAT$&%10?A_;0Qp0~qZBJ5DmlAY69tS%Lb)Ia@^e!fl+W!i:5E1g63&,lD+*h":hqShqMQ?:.dLd+ff0U@21MKCvX-;IVdZ+Fvs*2^aO!u;a7Y)fIo}o{J45Wr!/.jMVC-TO>?gp2i36z2T:nmr)TKM^pavH0h`s|]PdW[7_donY/y@97
BjIyu/[pX:Cwmi}w~GiGHdE/cA_Yd&OXY[7IGsx?e6s<BpvKFV%hS+Nv6I[5^p:/L1+C#$fm2cerKu[sREw9t3UB[.
IM@]BW)3K{t4X6cjjC+wJ]J<A/&pMK6h0JSJ>^0!Y!f&7nmI^Q[44@5<IRI=Jm??AW"Xh}J1`L3HOqT1[BTyWYS"03?(AIb/_S^IDg"Joi_J[BmmvsU9?Ip;O0Dt%4+<m3U<^
D;<G<Vrs,OwqoRI>jc;DE|^dFg=05C&dvXcK@=%T7$_Shi)20a-a1z!S1-T9vZhb"*N;-+3f[u-e.dY19@k5Q`7r7epyZ([UyFm]`+<~ipSXPpT>`rw~b|R6,a[d(LJfch6X`tK~9qUy
#7B]-.SlHO`f$R^XU9a_n+m*rV:9ySj=1X0,rNaBQCXJMvUX3xe
MZUIa`TB.#B6Av_RV`FTwFVCZLu]2qv,innw=1Gf!p2K;Ca';break;case'default-orange-dark-ef164a8d58dc89b2719f881377f73c89__136f6b79.css':$f='%O{SB7nV?&/MUNH!{m#=zi!"d!l`6
z-m4O?`U:pKDm1x6U(08cc>hc4h0fY@!~w<wzn#sa$~7c4pSIACR|e[N194+t+(2*C<_VFtXY?DGh3.1:]=!^88>NlA@D@ERZR+su#!+G?}`/Ik3m243fqD*}C_!qlX(;q9%k&0y:P~F0r|b?k-]T"ttK&0+&E{nODw@A>v@7O;!h(]/K;dCp
:
H]"
e9gF=F<Yz9
g{Y^d
.lAH#gI#.jBOEx$I/Z/X:UMAoQ5#t%9/W)D&:)OQd;cgG3g+pDOa[82c;nVPg$ulNr-S^cY/q(qd7]S
yxYSpdx>wVi^<~/%t?x|/&0NQHxx4$hX&qA<dX4/E,:t=`:!c^M::d)7t{61EWHG+.s,3RiQi5K[n(cR
vXNJzIevQ80xr>lXph0Bky(=Rn$.BQ;V#3[5NyN+(?|ZcGg
;!gMj;_l~[5,VBwPj
Mtt=QSte]%Jv#wqAvpSm
HAsdJ"]mCXn*_!kh2zij]zQ~Gzwpn@;0UidUYr=T/Xh;S0w(UD
)Gq#!r1J/QdtKLjY<#NGp7<#oQ<(U6A.`kydBeq1P[o>~L))]N0A4+sUIW>`Fe(6~+^Hg0B>QmA#n=9l^tHBn]IT8,oYd:?Fp*Fr[l/Q(*$/V=G[60sY,!32?W{jXN5>]1kvL&VpP4vBCMU
eq2Yp-fTj1E!&?xLa8FVfHvNf;2*F<T9!1v8.i6O]ARUl:HHRMOm8&A^R7e$t>_d-`k0CmPQ9.1J]dBCn)5qC,*uZD<X-.F-Dt$L1(bbm9$NiY4*TTq
A?/841ETJgW?6>]0<j5q|vU1z:YJ$ycJpFrr[0F[!YU_Lw6cwS
BNpZvdCRa$xAyW31h=>I6G!I;;iJ``s3ff';break;case'default-purple-dark-0f4fa03fa2d9287ef390780e90d9b06d__136f6b79.css':$f='*O{SRcRV?$uMEW3
9kEdT+F;W]X`E)h),4#?fl%d$HA!etXhsF@cAx%aO.yDJUi9>SRtKXomE6k.ct{o?
60v;MG2OVBOri>(At?RXSvIZ!<r;UV9(x.V4"@Q"VOnOK%OX_C3A1pS+/i
wrgXL|kaWE2U&o&;9+;lEK-&_l)c-lD[jhXh``^[uf)Y5C
,A#pt<"hyY3yBla%
4&J.0sf&@*X.a$](HU>l+W7m?RFb*23yZwF/Yp1T]>O8M#oP;BH>f%V4)Z3|Gfo&s~F0Mm9/:H+;570W%<u<Q0?!.8bC^IL$e+WIjZ!<SnEVeiR*58dX/8cThj,@@Sq&X&xn).]W>=BxUlQ;T0*UaUNXX^*[;NVTK,1}tw8X,D/UF%s-QGvXP:qdEYb~t9B)4K,b1O:glcR[l@8#aRiIF4l9Mt@5s$dUcMpqjt/xtr+,A.!@+^8CZu0>k{<5/Ew4_g7VK+Edr*!EHrIvq.o8qCG/edn}L-;w<xHG8hxI_C_uOz$txca5Zg?WGD#Uc;9KNXDFeETWv<J@ZgHT9^:dZL
?2_a
89#zGj*u-e:CwA1j`^
)oAdN>:Emmm;C^^j!=!-nid`xPN%D#6h5rWTOajdEFh
zqM$VgQT*d/gbn!S~EV<G,8VO^)r5WJ%U6r$kOVmGNl@l!;+B_G]v
,OOn_dW3r0{?#Tw&q)a=P3vtQTJ%;OR>&Q0YG:x_s"GS5``<Ix1wH;|,ub9,|n;pFK4!&-j!k:@mCAzRbm,tmLE[s&4Os?RA1P[EHGau}Qe.pJ^YleE?*%Nqrs{y1XU?3a<Wi*sUM$&Vs=uk7$G[kdrllg(L7wAkxBf-U3P#"IN@=L.7|)^W;R{xp-Xs1]^yw5XHSEfr:fCnf_03_wA';break;case'default-red-dark-3c1e28afe2cc92815bc7347df0d5776f__136f6b79.css':$f='.O{Rg7nV?&=MUNH!{Cu/Yo)dQ".A5QkQaR_hew4s:t-.w/XVJr`uziG+>W;Y@!~w<wpybK)PRIOuLgt"~Rl3?(VC>&-c/0b6N
?@5i-JU
H<Y(8p/O[$99)!wVN^fP@*5n^)W^xfN`&J[rJ!ZVWr|:?kO`^_)oi.;)/E,lyp/,CWm8&$@U[S~EHf/#jjn&pRI11K}fu#&*Q!y[V9^CpOkrL
<A3XDgpF<Z%Czg}YW-W.+Fw&P;J1RXWKJ$96![[Vuuxe$F_H.Uva/_/=2U)=]n94]J^Cg8f>Jfu5iJd#=3;*>x*N6x@OT6
@n-;mr$(VYLs3+Xg=~d7btpY(+RjBGmifCSAioOebxrDgk*[;XW8K4$Rv]oK+$/%@QtQOYuzZy/^EZLntAB(v17c0$efg3R;u1L@u"[SGww*Mv56R..37in!jtQ"tq"ac1N=24U!(V
laU=XGh2n6siU#Au{#>+dn%Q>efu]7@D6tSc,/#%}A[ih7ZGC]R-Q-f8#sr
V?HV"dN2//F8TCc8BV~qMIy^,a?_BY)j<7,Vtwv:%Pvh06TA<)]/*?[kj_^>-Px.Ortr$d1(;R7]m6AFk&iH*GWL(Cp(E8VV51)n@)AkV7RM4>H36[kPi<a.(@`r;k|Pm*4PeFpF?0u*RxU%}dkVj"`kcrzaiP3`*:IK^
SLuB5Vb=dj
1Dvu:GM$dJUCe|"n:~*F1C9(-Pg&2SA()m5b<*cJrIL.y$7Tb*pn+)j5:1?|Ea>8(Phah?itGWAF3`G0VP%#AH`wmzQwjjD<"wcgJt=[a/S}^gQ4+HRjn>fbY6km6K%zn<r9so]=gE;[>TNeB{xI9EZKpjV.MWpNc3XxEz3D3:a6TKjpcl!&&iwAN&';break;case'dune-blue-73b4ec7032a07757722b492bcc01937f__136f6b79.css':$f='"euFD7NV?=K?n
"9P`&d4/FoDX
e6xM0at8fm#"^ZLl;C>-iFM;j:7K3|`p,H7d40w(OAi(m6kA_y$%0`8*JN__bdAuXQcX;-FH8,_ungTs+*^!Q8SV
lkItg[|G_V-!H+TXQM)m]^8W;h5TS>i>~Q_J#C;8y>f@RhJttt8:N?sLTKQVd5`M,gn18,Ogr2yQskaC}6):B36iYe_2*5L9O_C,-KzZI:XCLp[6YxBuK]G@48i!IpoA(*zJ8!
OSeGc7`rW#s:eT/2(ghs!j4O;^e^Xpp1H.r_`),%!j6Fqe/[ZZb;k5xHH;+>g$fGr1`b,9);+r=UDFbia[<Ms4i-H*t,IMx$an&k
uH
)22WZ}0S?~bD=N`u8mJ_68Z`x_PF-65.n2HSKxr5kwp!,BjkxWYJ9b5}(%giMf%#O"7oeJOHn5)H"F/W][",Hh
0jC9ur]C`aS-nsq7Z!bkXRED/b)AT9oB^h[(iE&]Z+l+ouQ(TtDAriKCbaA^8,$ZCn_^[Yi%@3JG+r0_D9T`.&&2P_4=6,g0EigaH9|O<EeL9RY-/kGhQXoVDU|GLrZ!Apz9xt=(S@ClwOQuViq.@E!N}x48lKWFvP7q!L(Mxyf?(53V
rfg|"ZB.v<,7&iU",6s<tY';break;case'dune-green-4d18ddecd75372ef6cc20a81a9e1d8d8__136f6b79.css':$f='%euFD7n*;!K?^Nf<T`M:xfsuh.MB}CR>m/Wh?c"=?PY"-KRx[Wy#~&(GRq3utSBx}h4yc#=YqUGO_aWZwdP
z2=G8Ituu?GghA!cNv9b{^/8apu(/oI;sPjNX-;1f^S;du0K:C%[^
8HQD~S_)9_"la.t3*?!?t`c$`(lBCnv=EX6o`A?qdE@qV3k;t3@G#8xfg)53MkY5{-Je}10&oC[3;D~bO5_K|2Bb$FIDUth0OWSgy.$0[tmAc@0M43y0X58/Z(<T[w1_+35FlZT.MkT^PG-EUxS)5DZo|%7>H
d7,0kqUUcwNF6SKHk"`:?C~!6]N7!m/cC,O,~t>d6:0*6?#wl=r.0g*TEZ.J>vx"YT@5|R+MDg^Y;@[I"/.1cTSReMfkz-[j-r=9eW:qc="dx>fqF[uCf9^^)ppC"7;nqD
4(=j1DI?PM/
?p,hdA(XApq*]xfOFyAcHN&!%QEz3MmTcSF<mREE6t1_s<3:W*$Z)Sd`';break;case'dune-orange-1abf8bc0f3672a0a03a9c80b4fa032ee__136f6b79.css':$f='-euFD7n*;!K?^Nf<T`E8.Oy)zno`3UbH1QBbCH7QLSrCGyNKxJf"~P)4hxFKyqex0q?EQ"Z=x9YB%1cUqC%JOqDq@_/L.kl`1TH7tFTi;D?in;;<>AHp%ZIe+&AP*jyh~C$>%V6j?p@b=g^F^>*<S,BqzFQ)0M"K5"=)cR.Z{$GF)/0T^b-(C?R-FFETs-s.?&;TiV*rl):!tD%(4P
-;+@IN>c7e4Aq,t/(KVaAc$9qn9w!xZaKE1qF~4qEnkC!O%sQ5gsK._+2Y
n[_/pkX^RG.[YxR)5Dko{%:Nxg+XYZY>i;mxj`0$#5FQ-.0pu!6]IA,`5xaee7~nS_FPFyE;FKZ^MN_#Qhn>&6.6K-dp]!J*c0$_.aB"Ut"gBQL*PqkX|q$Q}=WotC$RRIq0sdV1!3Xmd2RNu@%I98"W~,uYyEttKTJA7?MO2
ua4Y/^|1wV9]xmDFyB:G+&!$lEv.>nwcKF<mREI6ZU;hQEM6rg|tX""';break;case'dune-purple-d155c2c103c4fd5ea0859e0794d8cad1__136f6b79.css':$f='%euFCcv*;!K?^Nf<TA(C.[M^%GQl=tm>m/
lQaM[c9=N+xrxO6X#P(#6NxFKyqex0q?Fs(3j?8(2(P9"W+&sdKRM_)$MNDkr7dZIVa32m<;bwSZ`x5Q8GlWx-QgoHoZd&m6q#B)JF%_v.m+
$-T!*HX3FY{_FbZInN)Zx;lYv(wU|TE7(Qq^;H{DOZE3L9GKDiOgw@jRtY8D$>~[.Y]5A#}cnm}=L+iuYK8SWwJ)w0ty5Y]N0^Xt8:ZF^$?g`.sfi$8DYE.K-I)2]U@=8>%R%Bzl9BTv=r.doeo.%7&PFXXdcrKYWyZA!"Ot%xq()IG)|.
Lmk6b_eho&nS_BP6HR"[KZD?X|#IeuT+m3ALYhp]w:*c0v_.0OR]t"gBQL*PqkX|q$Q=*2uFByPHB?!|o=537$A_8eKT1rweB{7;cpCe/rs*SUP:#T-}0dH11++fSGDvu[J;vS[J53&,R-G<.>nwb(1}mRE96Z4AuIhJHx%Wej"2';break;case'dune-red-b617e378af72da12da8a98167baae57e__136f6b79.css':$f='(euFD7n*;!K?^Nf<T`M=Z_:A~<Pk{q~?vQBbDs|(o-]dJyNKZ6T!}9%mKJK6|IrKcus/^"@0&
;-XRt0wHf68Q)xMl^y%UJ`1?k2EMIi;D?ivF<8raG(MZId(ReP*hs
:MaZ&^F.^7f<gU-k>YoA$5_4-eQFgy&K5"=,LR/[&!0F)1oImb-TGXndne8/_9s==*Y3Uf)6/NOf&r#::CY2X#}b~nR/eS;M26Z3Gv&U|&cKt!h#juEt:/a7)%e(XA5eJNk%0A[7S!W:B.pk.ect.K<G
Ql,]o}B&5WNo)K
js*pok5ioMl]Y"@S#q6;!R?.b4?LTlyV=i?p3l~mO#cF{9W^U^wIEQ%Vr60AH&{p
7T-RH4-N`~
somE#Z
U&u%tTur$~/vx>S~
fw^&%uOIw-Zrr5S>Z0Pwo*92P],m8B&!UQdLt.>;1gGfH2`:BkqE0
WKv6JB!HD&m;;-+1Zv144=RUea:wuRc6e)CQu_3s?7A';break;case'dune-blue-dark-0dcd247d9a7497ee9b8d269f82fd8345__136f6b79.css':$f='-O{ATb$!R:rcv:FVt
;5QM=v{5{ZV>Og,exZ9<kV.Or;C^?NWwqfU&<?9(uVR?;,{)Q]():xM;9NjCc%DLofC"j/_!t.p3Wc[53$D_(&62nGjp]o5IP]mC.u_hKg=8IfvP>dXab&Y57/mCTlJV2XLsaeF*}fuMbI|>IS,_HHFGtW53?Wn#ze1.ZC621!F-y+m*{yIedN`SbLz<AcEC)
1L11oS:("V33}Y5I-GfW]&6`~lm&o9qI[mkL,B{E|dA?LHnnU^3!J<8U}lzn9NOm@0]DjpeWB.P5/d5
&0qt*wcWs^#Vmr~-~Vr@K<%/@k%UF;8;dr!Eh&P:&h%5
Ug(lgZ[j&^_kH%#tNwaBiSUN_NZz9@rluKegWO,ot~6Iu/a}b>FgpBGe#
a
/4t!)dYC[;>d]Nx$""';break;case'dune-green-dark-9c9c318f665c9f0415fc1c3736e7b90f__136f6b79.css':$f=')JT7-0MSV83o>xnB2dTRIB
ju*_*h>jf[9$WWX0b,(p($ba:cLIO_#:*VrSu3
rDA0uiQ$yG%+?q^ZG$@:S[}iitneu,=IMq0#WU[s(dxo>A,GT3Wi|b%+{@^;-%ojS4.:$Zu3eW?5[
;x?YjWOM]Tesg4>.<v`^3J!A.&@LRjQ3wCVhWW?q~FSC^%7;|,;ig3e.Eo&&n<HP)%!T9fvlsh5(,^D3h:g13]xef@=YzL6J%k4o[H%?%+Qxr*pTV^xsAIL,p&.';break;case'dune-orange-dark-be294087bf88411c8bf6a085f995fcce__136f6b79.css':$f='.JT7-0MSVN3d>xnM3dTRI,ZkX*_*hHEPGNa*17"
(co:,$_].M<Z`"/*LrRvVr(Ip23h:*CHH60tG(i$@<)[}j,wW.`(oDGi-#_XFhKeNj"K7m)BFFY?+me_=j:?b`VCAR&goD)_-*p
/x#h8caX569jlJfR,VObO50*n3>XK;ei_:14$FmeJ;cs5nX_)c#KIChBedX)[+]9X*3)J8jh>aJRG*2@V,M@a&;[ChK%PlykL(;S(v(M74|>fMAE}9]nhk1N]pNN&';break;case'dune-purple-dark-ea50a2a07296958eba342f0cf81c60f3__136f6b79.css':$f='&JT7-0MSVN3d>xnM3dTRH,ZbE?F.RJRbH6@*)nBT{]Sxe2_+[^Ku_!y"^D;/pqQaXtPON2zu"cVeRAX*bvhdt9m`/f$b21HUzW1"KA
dUOPD4p}_E1Cg&r?Y0C.Da+s<t_.$V)3XJLHTuB>uIqp@tKw78[1]J)~E%F=JpDxg9KY1"8R+y>/cqPZevu=60nMG}-7i[bie+-,VNc&0^0xOXV{:{+h!8#|b,9"uK=HYT,+>iZ"YfRYxzxdsTRs4aTG?t+5pua$!A';break;case'dune-red-dark-fde13ea21a7e0cbe487d1d4d28b6a1cd__136f6b79.css':$f='.JT7-0MSVN3d>xnM3dTX)
jbE?F.RJRck=+X=v-9grj7QLe:Is"J2"
:a!ngRw{
665/WV2&nCu(q`T%?N#I=YwW*rjB*)afV<88:1mT8pmlk@dGH_f3<DkJ#eL0c3ah.Ui`:f.Io9$>tD?*CB&7cJp[`y=0nDBrIs]JWWMI9M"4QD[,AVz7}Pjewla6cH;vjY=i]br7Q!u<XKmUD(k8mt6pTYSxWYyn+oC3ES"YT+h@.D%Y_&5x{@|4_S5
toNd>PPh+-2N&';break;case'main-0864f21d8576870afa2ea1b4d2bb6be8__169bbed7.js':$f='+`K]`nsZ51ptW"r=6;m#J-HDqh*t)HqkN./vgS8^BaqT;T&lP"K2r[-"6#xJ#8#n

sLT"FDaX6I;p+8kvSK!
mB,]gbG_CuT)T4Igth0`t]
BQ`i?<=@ZXE:0dUV
aP:EdIcTMyL@^
Bg!mvRN0a;[qP6vv=c}W7u3p85-:g-z/G<EBtou0-xz;K2NV`b=j0a^/"oq01Go;IhKpoR=8;vvm#H^<l56OgDRx;l05A
z-2J},/_qxc7vq^UhBGk!RWj5V-36Hk"R0ar:vX0hL4[YVCZ#hql3]PU[-wKtP2#4jQE>g&DLQd9:D~09u%hlcD9=KKY=FH`i<DGo?H^2dLbJepM17taqPU?D;f!5b,s[HgRD7Pki[=bkNbO"XSrN<wGQ]xj)kEV]:4NsXhgwqn")q`yNtD(U@6Z[p{qgM8:I@EX:8d9!mLnicAX>9RDYTlxO+"ZI+?X)V<o"-hgKJ3#g5Rfb)fmdNoU]Tsi8hqIUoQ:kG@omS-%rPex>fn+Qa#,mp15wHTEzxr7RsouvX2
=;YL&Z7(865F<a5u;K*lY)0.Rc(0#g5i++yQ%@;8/iJ.K>Cg+9Cr?0RnH/kaI0gHc/IoAh4=OV.yzV5q+OKOk<Z_{PQc)q"V7+R(_J{NpOBe0@!r^-vLs-?^<R9A
q`KKO-KtqGL|5rtlBiQ,1TuC
}%~59KlWkN"/]HPI&M*^[`upxur<Q%yBc;A61Wt;C8:ur7~<zZ~8z/5f5L_T1Z(/_8CtbA[A
!P;I9EgmAt6/B(<nfY6j6d]<=q<|)x*8I1";Y<)5[_=q:,_;f3TJd-$?G+aZMn$";9c.TEp.<":#fm>E[.lU5O2GuJD{V7N{g<Z#RJe9wfP8UWrl*/fhb!L?v~gg<<:I#6MHb<jG/b+mh+U~[guGLi%`,<@!"it@w=3nP;EI=*nr^GNeCLhrEdMu/#hy9V;t=2y{VRKf.gN2!Khcl)X[%%,]e,nyfk<J6X^2yaw&4Cb?,~&rf5_aTfJ9xXs9Pxm`a=Md59qt??c~_JUeoG.v$v:3,+t/K7YI<lU$:t-&F@Mf*Fk<Dy1B*$soM@/#BfT}<G*0sZUAJ/-$Az)_yzaU2KrCh6hR"^_hu$C%+*eBlLpbj2Z;8!Tt%C@c*luImjz$
S;YdJ*NUmk(C]>(^hIAAz&df;O):pcz?wj
,+.isA%:im=X,l(0t<^#+??zL?&
cJ:
y.9r"WXoIznE9o8OkJ>pn7;e0),QZLCi%@tIl>T$i.vo[=n>Ql/%S,yi$6_V37DF-".ZCC-BR?xu^=2Kh1WW:B0@pBGKB5%EM3g;hXEtTg_^2E*Y>>Zua<h:%7#$3}l2A&]t7B=vN
_,=C_L
ndf-!`km<F3s]4m*
&9($7|oYu!;8EXfKAgP?IWJkABl4-Ze.Df!I&!S?G*r,Kv[$kIGm%SgO]iFL)6nl(X6GFKiWU3iVk(5Q*A/vJqjNc@]"RQ"X:H[(wjc%B{qQ3wPCvb3Xs)Nj(0Ln;RUfLWb_JvmsP}!Zsv=U.;*:_^[[0v)yEL;8,PgbT
*s>ym{qzP^#NC!V^GcLJvM%jK17#Co
L#Q-:T89vSgYk:2,65NN*w<k@RrcB19O)?88l?EhYnx)Ad7y0[a0)yr4VNH)}g8icjx:~x3ZB^{!3nn*no#Bo%/.q"P%q_+$[8/pZB66.1!!K7+PD7OEKEkT:07bcrD&ALR6
q)R!@`D8VdHw#YfqK0t1m|`_+~+)8lHa%C38$[$H_vpMQ##5n5!SX,wRYE#zCbhX9<yfZ8,matR;h`:NBvYaN2KcH&[9C?o`0>
4uSC5+nBeF{y&[6
)6<^V6$K=mC-C@oo==`#-p
rh,zkN?I)]HW*gYy;7
9u+V0b9Z}x3Gy7}n,u}bMp4Kr(:TkXKLHn*=,j[97hP9Tm10<+dPK+5O5fYdP.{lfbMNP"H1Ti}5`b
Pw4@Z
R|RnG7<g2D8~0H$B2<_X:Se9"9^dqj12C),BHxMK*LV75eYJPrnXW3l6cua&k}<)/f.6r)*?c:+cfRyBj
$cIpw{LZ1x%{N;Dbr
WiDiNjo
Ni?a=b$`[,7Hw60~Xfo
[w#bQu[#.XE87Vhn<epVpWalw&QsSCx:):N[8D5z$_-=Lt*Eg_IGI[4N@h=4/U
&=wf6SFgS.o=ghnw2h8K3^IX6Zkp6!}#=".R"iw-@2JBUZ.tkUH/9"3jrO9_/Sd)of-?j"$3Y^v(ETD2jwS@9L+r&0S1iJA-o8=#9;mW+t.,gx56o)Ly)Yqb_#kdEX3b>j#0I
r$>oovT"YP%a_t|hmR6:q2e>g")J[MRF5!}BFrP_bRT>JVfh]:[1A(HJvk#ry-{D0vX<L:=mr#qcD"m+-9mu66e,k^GERK6lh6Pn1:b@jx5)T2KGlLaH}>]&#p1NJs8utg1B">uwQwCW^(/x8$G2lUgjtioB("3,q,Yg$@:pB8X)>;@9mW:^+=r[N?J6JLa9!^p[f6.q~z!x&GD#=$pGRk4!sb9:te.!Ar3)P5kKw+`x(B]PGfa8+[HV?.^d98TA!qU]C]9(vko6cOV"W:R4HNC`
A:d5s8e;8-!8s{n#.H>QU5jSNY<ZFGy7,ePb
qy1`D%#3wUZsULj)AJ($P@)?5?I/utL"*va]F@HN6/&&~b[&}>lcT01&5nWN6[X&SC71,JE"NM=3ZMz[Ex!FTddatXv:@C~Oxe(?;q~%xVlS$d1Bf:Xc"dIRg2L4mHR/b,8/bRS*C4NQ!P)o(M3Bob9hu;p!oh=G,+dGvc_6CnPu<Bz2Ud^U21jMBA$nVj1avLCD%.O^y-{WsL0bz3OJ}N]T$@cV&mjmxn4iEj]tk]Chd3`x]w;=~vQftM%M&]Nr]-W`u0k%!4mewK-v[[8`B&"/e!hk,
F+Oo)WGNNLr5ZmqOM9H8pe@!-*.d|o5wb^wk]H:u=K03AF!+!>R)Uc_+O*_c#-]c(.VWuB^g%Szy$pv6v%gI&HVyi3]NCboXX+WPA[[Q#4M*<A%n=?~=x&7Vy`35*@o[+V2p)`(EcCrkf)1j)F:oP&;GHp2UmMg(CQ(,T2AfPEM:vDV`U2L]gn[b+s+Y1a(2]NfyyvfXJB>^jw{q}WEQ,LHg5e</u6,3V-H9.ri.L`SCXa0SwvDl~l[qPx2)P<>a$:5Vu?im2/N%Lx]b:mXu|/sUetmJB7fo|gkKVe9BRVYN?W=q<n@sCEn>fJ)df!r6T/MCdM6O%iFsSDiUf6XZ@L;]nL8cfe3&4MvN
NKT(b
^AB6i"Pnp0y-dqwrAYp,jlL7ACswgP@j
GIj;Luf/V?$(#
0b5=S]S],nhEANlu^<2!.6xfKm4s"=I];BW[cJV"9pXW.*=mU!Os`(k"35RU:4_2#ThNR0paTYAjiJ)%,8S7G6!CGI6F0I6H*WE%RLjjyGS;pNd@mVse|akOHH6R}enYHDV(G_c7XFrBNPo&%H9WTL^`93Er1<GJ+]Rw)4~P,#KX$,zvj!3j/Z!]d_g(!s6:)u`AJtNII5H.]eDBZU,]F(USu>-2H#G->FOr-[dm~.n`2j$bYI=isAi"o..a63^]5X=&yLgQHf;Pn)M)hXIVKYec=N0n0FgL`dZUQo7_
dQ74Jzv<^?MWGEw1Eu)co"qC;=0TDhm]9IuGTFl9<$@0FY&cL(X.5=V=F/s(PT2=*US!agizmBqOJO>u&j^u,APu>021>AaL]CIMDl%uA2(.E.;;iY#@pKtzM)Pr6t&/je"=?c"=J.Qjbt7^Dz6wd.i$c}q;YFMqkf"T]|Be6-"=ma)E[tx|M<Og.E)YvY)k6q#YyXSSn80m5ibUE+jip=*7kp7dIX$l:88Rm%JQEiQd5Wj@PB^K9Qal!rt6j
.vkyY(a<_D#X)
OR>;=UY&=-s)!")J&Sw.8A
wlLD%Fi_3Q!i&h/Hg
nKz+?^T:(YXeZvV:-kekK.jc5xIYg9%
gnk#fp/tmZDIfCBVbfunV$a:4@b6[^OrKXsPNM"8BqNP+FLMYf^jbA1Bt_=azp]A_p;cM!X7GiOXdB^]},(1{1NRV7<""5PD21V8{8QL+w"ujBN=n%+p<:DynEp^)YA/jZ`?y7-reT#E%4TY2T+v<mBGv2$I5n/!"7{oN:@_jpyYJQMfL$*W9NAp4]7O#g:cf1flqVc*Av*?bP.0sJ,>*oQ)+t5bt)vvQtt*vm5VN.rJv/e_NT$rq7P&qodTKj)
jti-`^F/;B{Uh/75k;{O?ca"4M&ss01t^==hZ3FAC[]Nae!X&;{hX*^Q#Y|Z-VP?4YHA%@?7H1|TMD^1*SwJ;G0LGoz#z4$$:OfC?m+%5)O8_H=G#5;lek?pH*r"GP^xnV
dOhNM=iF(A6>3G!/dQStXD<#IYSn>tDuB)K2T#Wjq9`>8`!VZCP&.;%xbdyl=,^jxrMnZc+VHw]DBZ#}hD>rh-[:dEK_uP&Es6OQ=aLv4f]kb?PV&K+E"ma`GM7i;jh;2S<TdCDBy"Ey,!oSmb6!?A.lQM9
>aP<)XDD^*
|]QPK39!!DBq]IacLvLXW?@*$3hWXFP,W!bk]_62^VMgS$ATl<w]uL2y]>X!A.$6ep[u#%`/E7}W>()2aGjS&G;bq"WtX&.$595Qy#zV16$E|O-ZQJTL%`t@b_<ml72D}1;eir?_M030$[}nv9$RHL4HN0,5&G!a)vLeP"juyr&:XP^wXwbS^+PRdv_?w5bS{d,_ZK9sQKyP3,SS67s?~9=AIMKp8de,Kvl,tba>4xx+%uM`SL,il"h@l9%
6,.@H]-=oiIYj-)P.s7oM#>q]^~-$Z4.)W@81g$hp#kM/^M(ot$EIP1-k5:3
_wIZy
,@M=:WkgJ2kwAe$
e.9CsIr-45m:^%X=c,2NGW%w;o7m%:E3VP90YY#Tl:G|YXd{FL>
K8P3fAU"2l_;sqYi?[3rZ:g=<k?b7|%jKq-q1z+u*2`TMO$B.aoLcSWl"E8(>6GJs?5XH0>eaT[pLD^B7Z7MJOM!j{Wy3<R-7XKimfJrLS)qfe4<I]DuYF[0AO]l)2a#.h&8KiSs_i`AVD#3CZOj`>w#(%#Nsr);;Lp>F~2RbmFK^wm`G1N8*-ex4^81WA0M,73>qxOJP18FU6>3P0feL4lte]HV(>:^>uK1jEEK1P_6RR%Jh<Eio[Gg/M9KOj-=RmV2UkaUjW"wA/a5.!tcG
/H^>>Md9g+OBTX0FscqG[WY7M"GW1QOV(,+vNWFKr}1WMWy@qGE+-zT`!}rx+5KAq]f]h2Blw-8V*kVW=.TPhHhb(h^kjX=P<YGJC~g<TNElC}?FKK2Nln&u5D.KQ$!>`E1{efAZ"$4Ui[gT-P_FAhwO<|7x0>yuHc?{wa.dQh/F[^u&QZBY6yOfm9V-gs>.gyLEOMazTFa}y%<@Y:
,K+aQ
%)Fk|X`+vjc--9jtPu$q97SKCnBs2p~1Y+`O)g6fUDddUh_<>0$MCdx3Xu{Df`Z1^]fA25;.TdK?"%tQv1mLr=c]CwHRgF(W~9/d=ujc-x/?"kmtzLC@bYn;p3O:
/_k[/@=V-C1:3u*as,wO3gPt&s`NX(o,U|Yh,,d)T9F"y,V2%LMQ_,-?Ij:!1L`WiI#T>:N*vm;o2yNLZV-XN~od)[,"
qQBA8jFEXmL(>XKb&PxW.LKv&JrVKR=u
IF!?v9<x/ud&awXMPe4jyIQgHfd<1[/[Hl8UT:DNA.8ih*tvB&TenC)4$cg0
*As%9&8?F>_>Vo~)v$7G9h^8$VG#-nwYA_-D|WVnsnj:=IH4V)Q*Q%pi@tq.8cn]TJ9DsRiZH&6Yiy6&4-d"6YW@BW0tces>E)!TsMP3:;K@&_$jv7V"wN
hcArt$]!K1I}&?txNOnykOj=3d/c%FW-YBtnN_x[v
Iq]Znq+TdD5H="3phEBW01-kY42yn4";1
TM.O[Xh?i,k?88RtoeSou`#ah!I
>_Iv8_l=V`9_W00YnX1DaR1bou#Lq(BCod,~)25PGs[^d1rPF=,(GjB7%Q+sf8-)VQ[cL?&lxJ4<xcK{ag#R?=?oVw17H48>@g%Lg75lR:c,cQ6PfqKm@5"M@#?U*Rl!4VS2]%8~TeU1hur;EP0`@V1Gk!!;9t[~J~g?3[(PX,=2J28aS|
<
oJ.gtF|)QIkUu<jls9&+0NFs*f)!=M^S9lsOb/<Z-(DckG*k~]]2Wf@6w_(4Iej7f:!#Lphm(JYyMS>Ruw0.kGLTD-cI:/3/QQws-LD_74II6<o2HjUZcmcae0Ib?vCF:p&u&(=XlV~2Lo)<^
ZNS3Z)LF/.d^6rKL!+DRpHE+M)N!?T.GAjU;Ws^^w;{dWqHw4G2@1K25=f`KO5
x<bPJ]UVJ>hk>a-zE2&_`@1a0o1Y)R#8HD61Fc>}>rZAE/Va-&^fW}s):6I*FvBUtAj~yX`I.U9XV:R)9EO"w(
|@`!0vR7V8d_^UFZ}@AuX[C-w,8
?U^[E])]$NdnWk[S&AKF}WjqmXIW
$C[0$U:[Ase0T[Be$P`1"rV|?!r;FS6X^MPRS^8l.Kk=7.bA054/]2
R(F/+wqck&[Jdoc[FuGkJ?:iE)X`8,)9{Ak`JN{CFw@%R
N`K?)Zqr_9gZl/5*w)Bs,?hF1*vy$1[kgU1>z:jFsnwDRxy=XkEo0,Dg6ImLa,lW4ujQudKd~9q[8TjNUoWrMGEM9EN9#a.($@ct"ELn}Q+ExWd;Fklmrk*IfcX[r#xw$,7XMH.re:%UYcty
R
)tqQ.H+lGARQH05ys/2ytSEHV4"^1mX{]vwAt`<V!+c|^C(mVzq;rGyC:+ZM2<2v;7;MKpo5%X;::fVFRTLQLcg:&6$M$.q/`SD]nHG:UUob*Mxds7<1N.8=9=D6PGqhuG^Y;{N5]JR_*`U=P_C*E2w=X|Q,/0?C
.gW+^Ab$yn%y5F:BYGPPTo9*re62w7`,#QPE1XCpay|(kS[KcJ`+UTQuJP-#rpYvgl*unfgm"q^9l[}@f<5Njf:k!5d;D8j^Nqqlh:M[u!Ru3F.1x-rMw,&vVBGbCW;7o[w8rD*pB/Xfp.xm)QwPb@o]KP)+]ZKW)*:ZTeW96*IuR[u(A-07qDA;8fDpnc]!@]7:N2?4,=Gb%&59R$/vsGcs`f*3)v$rXg2=WU&_u3^JkVL5^@+Z9<YMvRVAGz"TR:8cIw[kOO5
Emf)m,Vs0"N<o"T+-dNG/%;c)2diT(f<6PYgXx<w"Y(Hw)kfZ5%gj4Wc@&i.,*JP0jB##iEKp=@af$.l@%ji?UkSgH
nGwXrZQaq8
YK0VW$b@?c}h"xhwNcuVFQ+f7emNIYldu7I1MmcGR;^3r!(><3{G]Ug%Nx;tbyj]-X/s,3]OGk_0c,N.bIU&i:{Lsgb7t_Oh)TV!heu(*+(w>3Nx*(IBcAeXi#=o_4;&p0@:9Rx##hUA[NW&BOVeVBB,&1xjxNE.I8$IJw-+bX?mj$Hz(efWj:(,"%S1)pExsS6tl*z5@fa!=$D!}ikvY"g2n$b>(f}LoJm5HhWN0Fi[cw|dNZG*<n]]_QmdBU*A#h)%Nb}=:.B*^eDr];EfD:~J%5pe4Ir
p%)9}s?$;&,ons
,#LR5R[_MS2mnDEg@mKdV&7*Ns3aN4Z?8l5Og;A<58x.UUTG"O_u;8#i[3tKZOl&%~+A](_kj/ur)]4T-{@3ci6hl>,Z@SkjGoy[c[Z>o:whTb>n#yr&Gyb77`!~>4f`fw0D/O:@r)`TD9hQn9e2c(joXU.hb!G]b^A%PQDDp9D!hek{(5<I%3n3>Vnh%coeDw`==;Swa-3#t(Dvq|B1h5NZKydX0J+PB%laHY,)5
T|G)GC?1].D>m{R-q[j7TdD&"w*3.&X$,MKaZyNW8EW&oFR=5v&UU#3_PH:zH84{]J[SvICu"bH_(=oU_<MQN(&?Az:v&0(;q|@kh9P8pLF5-J;-;!%"@m0:[ythfyut$u-/)BIp&J.@088ofD+(&Ub*"4<f0251^]Ng(L$*KI.buRr/JvSTR{VF!4+>#D,ce>p,IHEYB9;-e4Pb#HE_/feZa[n//9bWQ8X,)(j:qz9Rx9,ScV=t)5Ov8I7e6rhPpVIQawu1Y-
#G(WcurXKB-%+>Zp0=##d3Q;Z=,4q+:NX?c)/.~p5m+2CL/[Z#F>9lR4?O,`lMLrX1?%@Y5#U"7U(Vief)Y[=.Lg/e@RL
S:10^d~>G`-=}3z,!*x3^)aG{nqR~fJy(Y$qtlx3_VN(4F@&*ujsLTm%"904Nu+C7S!Ho/%9AQBxx6|BOjOWnr7bbHow0.AM:
h9uu6C%IBAxfp$O1Lj>ieGujr=;:k.$*^VnU@d5&0rw_pH&
!Am;KwB<}l;&@Q}.~@]PdG3hI^_o-*]A+n*NyVgZe-bhA-ya^xv=c#B7q@_z)i^RdF?Hd-E6D
f4J9600xJWX`S#;A83=CURJoqpnJOy!idp7bT.$O6n1k]3F)Y4KVuok>_KY9J_@yY1u6+j:({3e`&?y&VcHhr0K_K&}R#,!
{RwfM;k>nMB0v[XTfQ7[=g)bp.M<gRzVC3v
6Cbt&)^G=<>O7#-1yrNTecM"*;Rj%v<bhH@Stf[s!`,(sQ>4hGl<kX/g{Rf;p4?vL!<YF_aSx
k`~${XA,Pl#7~*d?2h[UCm<FGH~U)gM(D@-rIETa?y76aY(DXMkvuGBOka&ib0DisPv8x7,2{@!@G:j!~beZ7nZN<ezXneDP0cw)9Y?<Z!x>m;(2tfY1(p}B$XQZ^tSresG#6kqi}i70X%dWFn)E/sVJm60l`i0(iVDi@omoSYA9s_
lG)9O:Hf!STmG-G2?%$PRGuNrO[z36"qF_obvL.DQ@y5CY56x3_^r8q6JwYNT$3-luQH[Q/oqd3q]Iy2@cRGuj.Y<:rt*}b@&#b?,~2;y-yfPYR}4%x_!rm6Opa+oQ4KI`vH/})h6c?^ll17!4<K&qXCQ~&;tM,64e:]O8k*.e(Qywc=#-]G/ed,C=xvwa>FK"--v!(/p69F8Gw:N~exsXl>1c#)Q"Qet7tc:xte"sU6V15n_7j^-G`}qrxxhCTCRdOiQtGlKYZn
2<l/SDYx1Ofhb=YErxMSrD~L#4k4H#5UyO+e;Dnu|toW0Qq0fY^Yxy"G6^:l0TGO3d+$YwB)kU]Gks5(IHHp)>ma]"ve@e_y=.9uWpDtSD5%``]nsQYlHt(V><:S7Xzk_h28jgw8dlHVtf~=v0I&07W*JP97E<sY9OyYB.1iuD6R,e0xA8bd7h$4vfBR4Jr_n&$Uo
3){5>6@^GCHsgE:(l,S(MvVb5AUW<$kb<[Qt`-DY_Kq*:5jr-w>h85VTych&3TEB.$GNg[uA4[fjO<X/r)teYspJ?/jUcA=+F;+Zam+?)4/$V#mo*i,rp
HWm-3h^.+>jrbqq?*@zYC.*sa;HYlre6q,k*/:l4JYJcJ,#Qoe~/6OK&M)YXJG4VFPQ$
"D,(lI$syCX4>DYp?%wyr[EguxFrgijCU%<TW5lDQu&=At[o+k?grOq]1LZHUYA|PPr/60N0%dW[fQIR<4sbJU61Lt?;#~Y7E7M,$,""uj&8>@_|/p&U/NMeh`s|j/HPNnsV"An:bBwq.QihnBh?F,nl0],VkHN(DS"]@h^RbzY/(N3=*1/
bh+JY/$Y>c=XHv7g[bl$vD9XWWSR)9E0K!OPWZ63@lG@tMpZ("NKISBIq87W&Epf+3DjGCwvFY]DChN
h",qPgD"5H)D-aO`w8C3?sZK.wda6oU8o`dSy,XG$hOY[%DRlpPr0@)6H=8YsRi6_YHQUmXOyu+]UFvrG;jq&^od9|]KT}4`<_sRXSA2]YnfnKl54%F!7}gG49liPW9tdX#.FTd:BP9RF@93d!6l,d9IA+F418GbC@pO]fTO28cN6!&@vTlp#v9[91AcM@O[n&#/uLS8UYKguYZ_xWA%7HM+IO0^rjL)f6jH
_hQndAx_u-{@`1WX;lrJ87{b""

c:3rB
]`i?thbI}xD;/36IzJ*OF:}NNK{cq:K;}GBAU@//v;F1wFipLb_*n>$XY*r9Z]av"LDt%z)?gC}m9JMVHf:#08jv#l>iGEE[FX]w:s/J7(XJfU`m85Xl8S`WK+@bM;5e%`_qH%go(YQ!`Jx?D7.$/1<XZH(k}t02eKla6]{saI&J.,
_2cl
ut]88#bT[4caT-7N7dden=AZW6lYh2egj_%3cu*Ikef6.j<
T$7kTm/WO3YLya9L:kJcOF4D6i;<;>EG2yhrbghDG?[PRq7u_;5#=I"d;:CDnI8k]JD4+9;?{AUfL.lq|Hw*(:[Or8gK&se=a6>S;s7acHz]+cMDRCz/Ah"8Lj"+d:h?$L~[oI84}C5DT``c1P}v]wHb3/#J*Q&#bekcDj
aHxX;t8{maQhDsjRt:&mVKrcnz5T!3H+lD#(wY@#9Sc2]C+Av}&{yF0*IfIG*FMKR/bR.h?.yf5Jkd
^B8um1PBR$)X|WF.<v
+BYcKyYY7
qeax#Tj6^4V{GOoV$2Sx$t2FF.<E<eu-?VdnYl=!sP/r:UQsXRT~`nd#oqH)N[(U@IaUd2a`y85@8
qI?CKu2#D;+MXvkOL8dLYY+C9:_&2YDVOXRU:y(H?)/``m_C$5Co_Tv01
5V"jd"g#&T2Ib$x]&yG(C-mR>XLdu.E$Ma,"ks4HViN3]pOm2P"s)*eG9E+89raj_(YUvB_J2{t.5)814!I^:+py"h.2@J]A+k8BQqZ|apy)/Xf8y$>!)#apI:
z$6"@RD::t2G;VX"M?2?*+TA,QwJCog6fV6$^p~G#6`GTH9@Cv=N)1{^gPTBx#YMRb$e2+v&>2/3JdhIZEB(9mU.k%Uit*QByQ(LYY`I>JvXqFgxwJycDc46E:G1UoiE~7X8LYBIbZAWkX_#:@(.D
_oUR9sb%I"Zx?(Kns6tsIl{;
owhYn
8C@16UG<g
n<v`iQAf`,4(`yl-Dxoe%mQ,*t
mvZx!=>2_7c1d5}/j>vQQe%uJ7W.zqWRY3s=OP~5#mZV>hi^p-6f7EQi!U`d^(/:lr@6cwEn*o)q(aXZDEtN@k&L
-;Ez)O3B=n4Wof!^,@a7Ee7aeGDNg<[8hzDIaGhI1btvdfel#hZlY!5&ba4#
7EBu"oMDER6We]^!Hf4:Qg-wf2FN~u"c_/.s/rOv=^QjqxZxAF{Udmb&
l>#~H52ly>jz#qO.T&c{+Jhj3>Q_"y/%#,
Fub-bmvZVf}VHI729<J#0p|20J)F#7FKXy~tYNZQ;m=DSv`0$2TbRk,aLETy]pjF*s82_=ayxA2@V.bCx_E/NdS5NZ:]9;u0~H|0LXw&d&i]x7/6OQ]H#*]!"Q92q;:1T9sL;86Uol.XsWCJ+ukU~n]JtB`&*dEL*[z>p?w%ue,D:"3uENAC1qT>wCNa>R:H#n4vW"rwg-)(#Cj$J4pHV_UKftRbX=1ZH;NMZ,ul#Z+AR!Jw5md0F.UZkB_b#w}8}lr>c!;"IiAfB=yg
j|TfA:ihL>R^N}5=]Ou@_685?$4oj"P3Yr,y`rYl1Q"wQJ$LH@$A9vF9a:/+I+2fwqPw^Gk|cjsJ/n<3SU+DjgJ3M?/VZG?DVXnn0V.yQksje*`Zg97&=}yVHGKNI*[w[y:gw<z&a9MY^%q
tG!VV`^D2W"H^+h2ro3a4vg@Kq`#sEpD[]]3fO8}EctKF~GcuLevty#Dg?wzM-&;Y)E.O)jU7Jg`5]
M*u02jjc~&e/]-exyg1[TSSZ^"{=fs%k/UdX5d__P,UC}2jr_r}0ecaXpxXGx;wa*%,!Vsq@g"+5u,buh*E%Vhpz%4#szgWVqJ
AfR2]1#fD*1(:m*lkH_70FnO>ABgE~[HKCP-/@VfXis.pN2(mEXHo(mO$#i3B{iamkH|A<)m<^sL+AOiZiY#pu$t8[Pa&,<9]gI!Ep9vm@wM3+lL+-g&Fm0lgUFQn0/8]$l@nOE|*8*^t5)&C
8itN4VFAT;hOQKlKk6n3oeJED9$}Xc(JU0C;](__V/b)ih;re7x%]L:?[CZ>(CT$/{"L%,!cowbb

&

)R6/JK,,b(ttLZukmZffbmu"=x;isBr"scK_pkO9"WibVbJJ("qctsxr!rPbM@+F7/Z+G8?n|wpjwlA0_=o4A
7tp.+v%vsNF8ks@HS6t,]kZ$`B9_~A:MA0f_s$Rm0/jF7awDc09`93}
*%7`y5*5k^F)<D&X|Bp>l"
T<.afb;dJ$8:uD7p5(k)i^_|xq!hLwz#QNp<E<o{ixatuE!mv"Vx^/r:l]]~/g;nqM^Gh)wp
>XM>JTnr1L`IXd%S$S%]^QkTCt`o[wls3pQ.sK.UNdx@%,OqMpH[TkJ([Zzir_VfbFu!$nPxb2&B.H!kYxh^QgkJ(!K,cFFS%mkyd@NH;r-ZRaQ_~FNev7sXPK"8;d[
o#H^O9:s8)H2/Cu9Y>};Pb~CpiNLs^cyxb??U]gKU8<+bE0(PSaWPBI1s;_3`X1(36Gu@d1j$T2Q01/vR$:9_
q?NC0&Z5EQ$^&Igl}<X
Hc{+-!fmbTx^zg]KY%9X:)<G8NfAb/T6G@cq*,xxm/<NG]6WJsH/#glFOF0gqV}4#ytR~Kc>-e=w-9:Xmb:KDTYwP[jMJf^xkY"nvDec]vr7/0I:AX,U!Y`"nuJUA-z,Qq1GrZ"LDu3:bltfY0*s~nDLuUT6|V[JbemcoM$3VmyHDw[dT[#[~Pm8R1#jaAF=jI}uY)4
DlF4}F#t1QtN`/xGZ*F#z,^oaS,Yy@eb&I=Map=r"%D;+=VKejP+&pbMNulXq>Fl{FmnM_CG~AXIu$L>$#WaM0KY#e!M_?Mo/fP3*V;5Kv~
>s+F*oxpKdOJ>VteNt{nI.G,Q*i],KByc&_C=3uA=M)GTr68&XvHP6X%brWb,!!h81q"^9%bwV="0P.sE6/eGKzHtmEiNCW*KNp[1IH]S7"!eYjW4.
t[1)s{$`$ppCPZ-4?ad#U583&)pL68<TqOF{3]bG8pHXe)Ia1L`zF9s!Swbfi>,W!Hwi*UtfS+`()Li`n2V4CRwf>27buOvhXM]SAU`,K*.e
TdoTgZiug^[L?WAe4`qTAAX1?/d+)Jeq2$7*[?BDKVsWDeiuM]8fD-Bt(LH0hR!1:-leN=e7O1,h}RR%m?sQ:X(M*k^pkG/quh;+UH{M_WdF9i/*YM_DN-)h+w(IKm@QNm2MR;=+!M[*I51Y]s73E(b!<[4s+RD0U#35ce7ui!I.L1L_
)eh=d9anDOUv1aIKi<M%u)V8e7:Ef@5r%:<(>w;*nx`I.XxLHBbBdJ!HLXIO/&*%7(gt05v<:6P]fO@^1sb8P)Fa2Ypo!W5{lzw-Dln}EOZac(QaoXY;Mv2drz$c06c|&Qa2Dae{mc<!UY-JCz132tU0nX
uvPfV]GHrgGN41d8e[pRw9SLLX9lqlp(~Ef75%L!rmvy=rVm%>V&}F2$plZ)E9RB-CJx[RC%rIHp><*dahKV3lkmyfc]4um;iP%L8..i5Xx
hJ]r_X}nzTaYUuNGww8
MBW+8Y>a_EVR(T$1,krO5XOL5UPn
Qx,~?A*Q321=Ekl<8`xE":Rp/g%.(kci/.`}+kn{.&Cs/^+t,wR27
O[olc(fri(anfTLhQIFKV_FE#~;fOU7ClwQ,UdyCn>^e=>@2p.MWs1g*Yd
Bno<_Y8X=2"?+1,:U8BC`yYt_lC
s@WVv?+`"/liD?GOkp72VPX8og`99Myqee[2Cy|SK0QcR+hEVbLfQK1vH&d)et3mKqpEZ/bZrQWlJ&TdP5|";y,tTF&lUrU8xrsAz[Z#Q1V^+L1,+8gubbk("`(_@$~EO#O[@@#I.;m2ge@x>l/YNxluN=hVd0a;SXJk+k7vCpu?h@0>84caBNj@*L2eS"7-NPt#R5tMt5Em=X1UQ",,mx
J?G~b~P6;;om@=/GrvP&DoZzGXA_8bgn)6/|Fc3[HxM*<ip:Oi,$8YE0fKb7<9)Bo`(xT|g6fkD;0@qIFk8"Ua>h]:c3H<3xdU?=2U`JH@fCn/N+/A`p5G-vDU,Yp>F%H@j5Y;LmoTb6STkO$Ck//zwRK"@2KSEXQIU3=QFblgFzto_FtjHJs[`$A9SQ3%x`#Vf,]9/".Y
HdgWH+:Vobd+j"I#Nb&B2BJV=/wZHKe[_*X4"HBkmeqqyKm"}W#C@oa<>.
$i+qx?@i(/4KYcXioX=MlJWOH/d7?T8UJrCp-EE:%UB#5Zi-GXM-QKNlIOAL%#er)R<gvmBi@okzF
I?rp$Yn.r4r1_mMNX7ug)~VpYYNZ=G"xIJ:>`rhG2[z#N{Oz[Dn!7A#Lp|!PXShas$/+u4h/9E2W7sneP(W!>}?nSZ/90uUtuY"1mfm9Az2mdn/1ZS*LYiGo2m7+ojjNP<MN^*CUgPG$,g&%3{Ze3.ccQ?HQ+i`:+dfU%apl!G*=sqN0wuI!`x<&+nb60]#.Qma{C:J#S<`zrr`p7>Fi)s"P1eCT27PEo8s]m!2iZ~1xr~#O#Q<]=uc$tau$I>kKlSi9RY89I1^62]Y>YF%0YVW!YM&XmtAvG0Ao_(HHELVooa-qQ2DUPQ^!kL_"?]Pb89rU0lwHDoS
L-!{1;=Ax.-m+e^&h~S4S:FVgH^TXdlzy?@=MX5(UhE_Z1B25(J)2pUH4xBeVm1Xog9^CxP{>]:U%b%0y$f]L
q~u4Su0%!sXk,.D*BF#/SCX[b?=iigdS*1V0;{rBE*HGnnS+rz)cb^sVY5xBjSx`9/cavy[&<
`BqvQ}F{ma&pZ/]&t4i(I]QQ>lAJw9r^7GyJF=o{Y;
K*;l.Y
tBl}4G<;w:ico/rL5"(]sCf]WJ.>IE&e+9;3Y}"Pjf"3v-+SDVDWbw_m,4CouXPOaZe=9e>(2>Dtc=y7rw&Q$AKnkuUJAP:]09mY<<KZ1c6"9w,6FoZ7k)nS[Y>$XZ2XQ[+/-,FI59/ZwzffYzh*J@&6:yP/uJ>Bbv*1N_]z(jy
;Y>]#8@}(}K*$ei]2IH?hX2K!<+!&7`O);T:96%jd=UuJkgOw6&J-m*Ds|jy3"Rem=F6&
wSR,%ULdEe#-"nyEKge/sd3+n=qEM3mzEqFS,EkqAbn~f/+}id(Je}1`p5t-yB9{cWL}%F;(/*C@d@F;ZaUPY33MOnNUXld_8yLck?vTl"Oy?P>k3@ywRH
H
"O}#+FCrT6]W&uU.@g<3.LMonqpZH@xk{J*:c3n"<H?V+J[4q5POBc@oI9jj28Y?6COlvp
@}Fyn4R9_Zfht-#IL&f-tYMROSF5r*WC/~g6:w9b:og+7/V,"_EkXov##9c["xv^GNP}FfyXULaM&emnj$.?Z(+/c<V6jU>
;K:T
M
jbmG[qsGz53JYY/q(eqVU$>#oS2?D
MA;I_bVA,y[6:)b`s_[*]#@:p(+>5vMd^R(o`Cf1i-vWm)}ZT56c(k:nd[nEufbCo/QU0I;=8cf[bhBF>)2)a8-lvYv?5?f5"AcC(*U^g0<$burBEC
M#"E+^*NXf1yKL5CWBAq9Q#Qu=8=(JIZ&Tl99=Zrh
NVrk*T
ze!a<p2qR@}ig;Mj%l].WbTA)/BNz#)m;Z5]@88t@?WYkb>loq2!wk"(xeOVaEw1wW]/a5B/,JTmji/F(mO7U=`>SpqRMXJoJs>1QgZ$P-a5o$(lL
p[P
#((f);jmWU}vNqDq&Kf4=Ym6!_rY"fC&nMM[t#<8S_zue?xRd$v@p!B#|ZH3A2]9I<k,t?Td]?:wGyL^@=l#i@c:JRN_)8oG1UrZ<:bG[FLfViMl[;^dtdd0tDrK/2/l/P%5{5sYiQ{[yQ*L11]<8W(["P}mRXg_d7j)}o%2z]2P-G7S3pID.6U6rCkva:bKt["@~e#.q#J!$=WbEe#@s!!j)xYoCW0inocY.Rwy~#4S|l(+~i*U#h-JT"r!Nd`&GbtgJso0*4Sg.yx>DR6Ny]Kj`T?-Li()Q[Iel5rvQM`Tl=/!?N(#.xh!0O&ojOEnVoab1GBnt`2f8OVm`LbPI?N8/x(uok=yq32SNJI<*l~:12iyw$4[-ScTl%DfRpT!UQ|pH,"cpTs9hnjLVU-Gb9cP9a[<E/s:P(&ph`%b#f^8#2*,pf8BYGflF^+i5.X^CC2v>Fd#]+!+1T0k@c>iISDji]V`=o`ysMYv]*imd5oKeAt>{?cd4VZ`KXSpVBCyK/h"txtQ7,w0kIeC=%"r{MblT`0i&r*q~G7l-BknaHRCVn2O,)h,j-"?D59rk-e3LAB5:E2YmP,UA)U
eFL_i)o+aAv+FlcC#$HaZ4&XyFFY%&#XJ=f;PeN
gg#K"<:$"SrGRnwc:AhT(/jym6k@F_lKAHSc2XG3j<dOGeMB[o,vxgHu,*B3vGfd)_}XaJS@~Fb?=Y)awZ&TyCb*pyD:ivuG!cN>D(pPC#ZL>60,mc~CuA<$;(w<<_J.FK(S}_%)bG7"8+Mv.!
JBnEuCg|5%,{b<1-;Y^F!ZwC3x(:5d<4aH.96!DoFscuU&bS(*dau{h<jKi=(3dO2H3NSS0<.U_**pQXjD?aDgIZr]pGv5ZD9?W^9E4*m?<i*IBm3o9J,vQa8ARB+*NQRZ$#`A,FsMaq*z)@Bv;My)!.J;N)N6%?5NNaW[eG>6CsaRrUej<mh%0<uk9q-h]R%T*<xJD
^[9gQoHy,3"Y;=f#
:&Qq@F,r4j6LL>C@}5Cb*"m6DP3T!Hcp"X7aWN)UE,3n{k*7b-mX"Y|r<!{!yy)+|YW,*$nF:c}rEI0bfm]r~L<e|;!ymOi#!hZ9EZFADOjQY@J6j8N2Z#)hItF`%4/J`%9AKr3^q`0lf??sFL0=XX}u{IKK"m;WW=}PK/E!%4,kl:0KqYLqGF}g#kUEB@|6JOA?=`v4/d`Gn%yb|oavI8W"EU8ePCjIA_=mLd0ThVx0>Ro8G6>MM6yC.$Us6nHnjQl9lN9Jhdh@UIN2Y:33}j1=1O6!|oCgiY5s}H_e1c3qUFMQF,8sG#R*pOp7Nr~;<*uK/Rfd=[XDq5v;,e1HU%5/OWj#nbAezhIFJvd4+[m%uQDZ&@xpbuNds?8AL&N`X6G]E^jI%.sJy!K66,iM/!"vO]x$lm97`Jw
s2DD=iF:Ztr3[+YfhGB7TB05;(r#J$dC`oFvCmpSN-VS})<2J2_"`KuZ)Li,J6_$*SM%z@a8o$#s
V%yw2R';break;default:$f=null;break;}if(!$f){http_response_code(404);exit;}if(in_array($Ld,["png","ico"]))$f=base64_decode($f);else$f=decompress_string($f);echo$f;exit;}if(preg_match('~^/[-\w.]~',$_SERVER["HTTP_X_FORWARDED_PREFIX"]))$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define("Adminneo\HTTPS",($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));if(!defined("SID")){ini_set("session.use_trans_sid","0");session_cache_limiter("");session_name("neo_sid");session_set_cookie_params(0,cookie_path(),"",HTTPS,true);session_start();}if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){$_GET=remove_slashes($_GET,$ce);$_POST=remove_slashes($_POST,$ce);$_COOKIE=remove_slashes($_COOKIE,$ce);}if(function_exists("set_time_limit"))set_time_limit(0);ini_set("precision","16");@unlink(get_temp_dir()."/adminneo.version");class
Locale{static$Languages=['en'=>'English','id'=>'Bahasa Indonesia','ms'=>'Bahasa Melayu','bs'=>'Bosanski','ca'=>'Català','cs'=>'Čeština','da'=>'Dansk','de'=>'Deutsch','et'=>'Eesti','es'=>'Español','fr'=>'Français','gl'=>'Galego','hr'=>'Hrvatski','it'=>'Italiano','lv'=>'Latviešu','lt'=>'Lietuvių','ro'=>'Limba Română','hu'=>'Magyar','nl'=>'Nederlands','no'=>'Norsk','pl'=>'Polski','pt'=>'Português','pt-BR'=>'Português (Brazil)','sk'=>'Slovenčina','sl'=>'Slovenski','fi'=>'Suomi','sv'=>'Svenska','vi'=>'Tiếng Việt','tr'=>'Türkçe','bg'=>'Български','el'=>'Ελληνικά','ru'=>'Русский','sr'=>'Српски','uk'=>'Українська','he'=>'עברית','ar'=>'العربية','fa'=>'فارسی','hi'=>'हिन्दी','bn'=>'বাংলা','ta'=>'த‌மிழ்','th'=>'ภาษาไทย','ka'=>'ქართული','ja'=>'日本語','zh'=>'简体中文','zh-TW'=>'繁體中文','ko'=>'한국어',];private$language;private$translations;private
static$instance=null;static
function
create($hg){if(self::$instance)die(__CLASS__." instance already exists.\n");return
self::$instance=new
static($hg);}static
function
get(){if(!self::$instance)exit(__CLASS__." instance not found.\n");return
self::$instance;}protected
function
__construct($hg){$this->language=$hg;}function
getLanguage(){return$this->language;}function
setTranslations(array$lm){$this->translations=$lm;}function
getTranslations(){return$this->translations;}function
translate($u,$Ih=null){$u=$this->convertTranslationKey($u);$km=isset($this->translations[$u])?$this->translations[$u]:$u;$hg=$this->language;if(is_array($km)){$F=($Ih==1?0:($hg=='cs'||$hg=='sk'?($Ih&&$Ih<5?1:2):($hg=='fr'?(!$Ih?0:1):($hg=='pl'?($Ih%10>1&&$Ih%10<5&&$Ih/10%10!=1?1:2):($hg=='sl'?($Ih%100==1?0:($Ih%100==2?1:($Ih%100==3||$Ih%100==4?2:3))):($hg=='lt'?($Ih%10==1&&$Ih%100!=11?0:($Ih%10>1&&$Ih/10%10!=1?1:2)):($hg=='lv'?($Ih%10==1&&$Ih%100!=11?0:($Ih?1:2)):($hg=='ro'?(!$Ih||($Ih%100>0&&$Ih%100<20)?1:2):($hg=='bs'||$hg=='hr'||$hg=='ru'||$hg=='sr'||$hg=='uk'?($Ih%10==1&&$Ih%100!=11?0:($Ih%10>1&&$Ih%10<5&&$Ih/10%10!=1?1:2)):1)))))))));$km=$km[$F];}$km=str_replace("'",'’',$km);$La=func_get_args();array_shift($La);$pe=str_replace("%d","%s",$km);if($pe!=$km)$La[0]=format_number($Ih);return
vsprintf($pe,$La);}function
convertTranslationKey($u){static$qd=null;if(is_string($u)){if(!$qd)$qd=get_translations("en");if(($s=array_search($u,$qd))!==false)$u=$s;elseif(($s=get_plural_translation_id($u))!==null)$u=$s;}return$u;}}function
get_available_languages(){return
array('ar'=>true,'bg'=>true,'bn'=>true,'bs'=>true,'ca'=>true,'cs'=>true,'da'=>true,'de'=>true,'el'=>true,'en'=>true,'es'=>true,'et'=>true,'fa'=>true,'fi'=>true,'fr'=>true,'gl'=>true,'he'=>true,'hi'=>true,'hr'=>true,'hu'=>true,'id'=>true,'it'=>true,'ja'=>true,'ka'=>true,'ko'=>true,'lt'=>true,'lv'=>true,'ms'=>true,'nl'=>true,'no'=>true,'pl'=>true,'pt-BR'=>true,'pt'=>true,'ro'=>true,'ru'=>true,'sk'=>true,'sl'=>true,'sr'=>true,'sv'=>true,'ta'=>true,'th'=>true,'tr'=>true,'uk'=>true,'vi'=>true,'zh-TW'=>true,'zh'=>true,);}function
get_lang(){return
Locale::get()->getLanguage();}function
lang($u,$Ih=null){return
call_user_func_array([Locale::get(),"translate"],func_get_args());}function
get_language_options(){$Ta=get_available_languages();if(count($Ta)==1)return[];$B=[];foreach(Locale::$Languages
as$hg=>$S){if(isset($Ta[$hg]))$B[$hg]=$S;}return$B;}function
language_select(){$B=get_language_options();if(!$B)return;echo"<form action='' method='post'>\n",html_select("lang",$B,Locale::get()->getLanguage(),"this.form.submit();"),"<input type='submit' value='".lang(81),"' class='button hidden'>\n",input_token(),"</form>\n";}$Ta=get_available_languages();$hg=array_keys($Ta)[0];$hj=null;if(isset($_POST["lang"])&&isset($Ta[$_POST["lang"]])&&verify_token()){$hj=$_SESSION["lang"]=$_POST["lang"];$_SESSION["translations"]=[];}$ek=($ta=Settings::readParameter("lang"))!==null?$ta:(isset($_COOKIE["neo_lang"])?$_COOKIE["neo_lang"]:null);if($ek!==null&&isset($Ta[$ek]))$hg=$ek;elseif(isset($_SESSION["lang"])&&isset($Ta[$_SESSION["lang"]]))$hg=$_SESSION["lang"];elseif(isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])){$va=[];preg_match_all('~([-a-z]+)(;q=([0-9.]+))?~',str_replace("_","-",strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"])),$_,PREG_SET_ORDER);foreach($_
as$z)$va[$z[1]]=(isset($z[3])?$z[3]:1);arsort($va);foreach($va
as$u=>$uj){if(isset($Ta[$u])){$hg=$u;break;}$u=preg_replace('~-.*~','',$u);if(!isset($va[$u])&&isset($Ta[$u])){$hg=$u;break;}}}Locale::create($hg);abstract
class
Connection{protected$flavor=null;protected$version;protected$affectedRows=0;protected$errno=0;protected$error="";protected$multiResult;private
static$instance=null;static
function
create(){if(self::$instance)die(__CLASS__." instance already exists.\n");return
self::$instance=new
static();}static
function
createSecondary(){return
new
static();}static
function
get(){if(!self::$instance)exit(__CLASS__." instance not found.\n");return
self::$instance;}static
function
exists(){return
self::$instance!==null;}protected
function
__construct(){}function
getDefaultServerName(){return"";}function
openPasswordless($M,$U,$E,$gl=true){$Ne=Admin::get()->getConfig()->getDefaultPasswordHash()!="";if($E!=""&&($gl||$Ne)&&$this->open($M,$U,"")){$H=Admin::get()->verifyDefaultPassword($E);if($H!==true){$this->error=$H;return
false;}return
true;}return$this->open($M,$U,$E);}abstract
function
open($M,$U,$E);function
getFlavor(){return$this->flavor;}function
isMariaDB(){return$this->flavor=="mariadb";}function
isCockroachDB(){return$this->flavor=="cockroach";}function
getVersion(){return$this->version;}function
isMinVersion($Tm){return
version_compare($this->version,$Tm)>=0;}function
getAffectedRows(){return$this->affectedRows;}function
setAffectedRows($Ba){$this->affectedRows=$Ba;}function
getErrno(){return$this->errno;}function
getError(){return$this->error;}function
setError($j){$this->error=$j;}abstract
function
selectDatabase($A);abstract
function
quote($O);function
formatValue($X,array$k){return$X;}abstract
function
query($G,$vm=false);function
getQueryInfo(){return
null;}function
getResult($G,$k=0){return$this->getValue($G,$k);}function
getValue($G,$Td=0){$H=$this->query($G);if(!is_object($H))return
false;$J=$H->fetchRow();return$J?$J[$Td]:false;}function
multiQuery($G){$this->multiResult=$this->query($G);return(bool)($this->multiResult);}function
storeResult($H=null){return$this->multiResult;}function
nextResult(){return
false;}}abstract
class
Result{protected$rowsCount;function
__construct($ak){$this->rowsCount=$ak;}function
getRowsCount(){return$this->rowsCount;}abstract
function
fetchAssoc();abstract
function
fetchRow();abstract
function
fetchField();function
seek($Mh){return
false;}}if(extension_loaded('pdo')){abstract
class
PdoConnection
extends
Connection{protected$pdo;protected$multiResult;protected
function
dsn($ed,$U,$E,array$B=[]){$B[PDO::ATTR_ERRMODE]=PDO::ERRMODE_SILENT;try{$this->pdo=new
PDO($ed,$U,$E,$B);}catch(Exception$Dd){$this->error=$Dd->getMessage();return
false;}$this->version=preg_replace('~^\D*([\d.]+).*~',"$1",(string)@$this->pdo->getAttribute(PDO::ATTR_SERVER_VERSION));return
true;}function
quote($O){return$this->pdo->quote($O);}function
query($G,$vm=false){$dl=$this->pdo->query($G);$this->error="";if(!$dl){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error=lang(124);return
false;}$H=new
PdoResult($dl);$this->storeResult($H);return$H;}function
storeResult($H=null){if(!$H){$H=$this->multiResult;if(!$H)return
false;}if($H->getColumnsCount())return$H;$this->affectedRows=$H->getAffectedRowsCount();return
true;}function
nextResult(){return$this->multiResult&&$this->multiResult->nextRowset();}}class
PdoResult
extends
Result{private$statement;private$offset=0;function
__construct(PDOStatement$dl){parent::__construct(max($dl->columnCount()?$dl->rowCount():0,0));$this->statement=$dl;}function
getColumnsCount(){return$this->statement->columnCount();}function
getAffectedRowsCount(){return$this->statement->rowCount();}function
fetchAssoc(){return$this->fetchArray(PDO::FETCH_ASSOC);}function
fetchRow(){return$this->fetchArray(PDO::FETCH_NUM);}private
function
fetchArray($ih){$H=$this->statement->fetch($ih);return$H?array_map([$this,'unresource'],$H):$H;}private
function
unresource($X){return
is_resource($X)?stream_get_contents($X):$X;}function
fetchField(){$J=$this->statement->getColumnMeta($this->offset++);if($J===false)return
false;$T=$J["pdo_type"];$J["type"]=($T==PDO::PARAM_INT?0:15);$J["charsetnr"]=($T==\PDO::PARAM_LOB||(isset($J["flags"])&&in_array("blob",(array)$J["flags"]))?63:0);return(object)$J;}function
seek($Mh){for($q=0;$q<$Mh;$q++){if($this->statement->fetch()===false)return
false;;}return
true;}function
nextRowset(){$this->offset=0;return@$this->statement->nextRowset();}}}class
Drivers{private
static$drivers=[];private
static$extensions=[];static
function
add($r,$A,array$Md){self::$drivers[$r]=$A;self::$extensions[$r]=$Md;}static
function
setName($r,$A){if(isset(self::$drivers[$r]))self::$drivers[$r]=$A;}static
function
get($r){return
isset(self::$drivers[$r])?self::$drivers[$r]:null;}static
function
getList(){return
self::$drivers;}static
function
getExtensions($r){return
isset(self::$extensions[$r])?self::$extensions[$r]:[];}}function
get_drivers(){return
Drivers::getList();}abstract
class
Driver{static$EnumLengthPattern="'(?:''|[^'\\\\]|\\\\.)*'";protected$connection;protected$admin;protected$types=[];protected$unsigned=[];protected$generated=[];protected$operators=[];protected$likeOperator="LIKE %%";protected$functions=[];protected$grouping=[];protected$inOut=["IN","OUT","INOUT"];protected$onActions=["RESTRICT","CASCADE","SET NULL","SET DEFAULT","NO ACTION"];protected$partitionBy=[];protected$insertFunctions=[];protected$editFunctions=[];protected$systemDatabases=[];protected$systemSchemas=[];private
static$instance=null;static
function
create(Connection$e,$_a){if(self::$instance)die(__CLASS__." instance already exists.\n");return
self::$instance=new
static($e,$_a);}static
function
get(){if(!self::$instance)exit(__CLASS__." instance not found.\n");return
self::$instance;}protected
function
__construct(Connection$e,$_a){$this->connection=$e;$this->admin=$_a;}function
getTypes(){return
call_user_func_array("array_merge",array_values($this->types));}function
getStructuredTypes(){return
array_map("array_keys",$this->types);}function
setUserTypes(array$um){$this->types[lang(111)]=array_flip($um);}function
getUserTypes(){$u=lang(111);return
array_keys(isset($this->types[$u])?$this->types[$u]:[]);}function
getUnsigned(){return$this->unsigned;}function
getGenerated(){return$this->generated;}function
getOperators(){return$this->operators;}function
getLikeOperator(){return$this->likeOperator;}function
getFunctions(){return$this->functions;}function
getGrouping(){return$this->grouping;}function
getInOut(){return$this->inOut;}function
getOnActions(){return$this->onActions;}function
getPartitionBy(){return$this->partitionBy;}function
getInsertFunctions(){return$this->insertFunctions;}function
getEditFunctions(){return$this->editFunctions;}function
getSystemDatabases(){return$this->systemDatabases;}function
getSystemSchemas(){return$this->systemSchemas;}function
getUnconvertFunction(array$k){return"";}function
select($P,array$L,array$Z,array$Fe,array$C=[],$w=1,$D=0,$mj=false){$If=(count($Fe)<count($L));$G="SELECT".limit(($_GET["page"]!="last"&&$w&&$Fe&&$If&&DIALECT=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$L)."\nFROM ".table($P),($Z?"\nWHERE ".implode(" AND ",$Z):"").($Fe&&$If?"\nGROUP BY ".implode(", ",$Fe):"").($C?"\nORDER BY ".implode(", ",$C):""),$w,($D?$w*$D:0),"\n");$cl=microtime(true);$I=$this->connection->query($G);if($mj)echo
Admin::get()->formatSelectQuery($G,$cl,!$I);return$I;}function
delete($P,$xj,$w=0){$G="FROM ".table($P);return
queries("DELETE".($w?limit1($P,$G,$xj):" $G$xj"));}function
update($P,array$Bj,$xj,$w=0,$wk="\n"){$Y=[];foreach($Bj
as$u=>$W)$Y[]="$u = $W";$G=table($P)." SET$wk".implode(",$wk",$Y);return
queries("UPDATE".($w?limit1($P,$G,$xj,$wk):" $G$xj"));}function
insert($P,array$Bj){return
queries("INSERT INTO ".table($P).($Bj?" (".implode(", ",array_keys($Bj)).")\nVALUES (".implode(", ",$Bj).")":" DEFAULT VALUES").$this->getInsertReturningSql($P));}function
getInsertReturningSql($P){return"";}function
insertUpdate($P,array$Cj,array$lj){return
false;}function
begin(){return
queries("BEGIN");}function
commit(){return
queries("COMMIT");}function
rollback(){return
queries("ROLLBACK");}function
slowQuery($G,$Yl){return
null;}function
convertSearch($if,array$Z,array$k){return$if;}function
getNull(){return"NULL";}function
getTypeName(stdClass$k){return
isset($k->native_type)?$k->native_type:"";}function
quoteBinary($O){return
q($O);}function
warnings(){return
null;}function
tableHelp($A,$Gf=false){return
null;}function
supportsIndex(array$yl){return!is_view($yl);}function
getIndexAlgorithms(array$yl){return[];}function
getIndexOpclasses(){return[];}function
getInheritedTables($P){return[];}function
getParentTables($P){return[];}function
isPartition($P){return
false;}function
getPartitionsInfo($P){return[];}function
hasCStyleEscapes(){return
false;}function
engines(){return[];}function
explodeArrayValue($X,$T,&$fk){return[];}function
implodeArrayValues(array$Y,$T){return"";}function
checkConstraints($P){return
get_key_vals("SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA
	AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME".($this->connection->isMariaDB()?" AND c.TABLE_NAME = ".q($P):"")."
WHERE c.CONSTRAINT_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
AND t.TABLE_NAME = ".q($P).(DIALECT=="pgsql"?"
AND CHECK_CLAUSE NOT LIKE '% IS NOT NULL'":""),$this->connection);}function
getAllFields(){if(DB=="")return[];$Da=[];$Qf=(DIALECT=="pgsql"||DIALECT=="mssql");$lj=(DIALECT=="sql"?"c.COLUMN_KEY = 'PRI'":($Qf?"k.COLUMN_NAME":""));$K=get_rows("SELECT c.TABLE_NAME AS tab, c.COLUMN_NAME AS field, c.IS_NULLABLE AS nullable,
	c.DATA_TYPE AS type, c.CHARACTER_MAXIMUM_LENGTH AS length".($lj?",
	$lj AS ".idf_escape("primary"):"")."
FROM INFORMATION_SCHEMA.COLUMNS c".($Qf?"
LEFT JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t
	ON c.TABLE_SCHEMA = t.TABLE_SCHEMA AND c.TABLE_NAME = t.TABLE_NAME AND t.CONSTRAINT_TYPE = 'PRIMARY KEY'
LEFT JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE k
	ON t.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA AND t.CONSTRAINT_NAME = k.CONSTRAINT_NAME
		AND c.TABLE_SCHEMA = k.TABLE_SCHEMA AND c.TABLE_NAME = k.TABLE_NAME AND c.COLUMN_NAME = k.COLUMN_NAME":"")."
WHERE c.TABLE_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
ORDER BY c.TABLE_NAME, c.ORDINAL_POSITION",$this->connection);foreach($K
as$J){$J["null"]=($J["nullable"]=="YES");$Da[$J["tab"]][]=$J;}return$Da;}}Drivers::add("mysql","MySQL",["MySQLi","PDO_MySQL"]);if(isset($_GET["mysql"])){define("AdminNeo\DRIVER","mysql");define("AdminNeo\DIALECT","sql");if(extension_loaded("mysqli")&&$_GET["ext"]!="pdo"){define("AdminNeo\DRIVER_EXTENSION","MySQLi");class
MySqlConnection
extends
Connection{private$mysqli;protected
function
__construct(){parent::__construct();$this->mysqli=new
mysqli();$this->mysqli->init();}function
getDefaultServerName(){return"localhost";}function
open($M,$U,$E){mysqli_report(MYSQLI_REPORT_OFF);list($af,$cj)=host_port($M);$u=Admin::get()->getConfig()->getSslKey();$ob=Admin::get()->getConfig()->getSslCertificate();$mb=Admin::get()->getConfig()->getSslCaCertificate();$bl=$u||$ob||$mb;if($bl){$this->mysqli->ssl_set($u,$ob,$mb,null,null);$he=Admin::get()->getConfig()->getSslTrustServerCertificate()?64:MYSQLI_CLIENT_SSL;}else$he=0;$Xb=@$this->mysqli->real_connect(($M!=""?$af:ini_get("mysqli.default_host")),($M.$U!=""?$U:ini_get("mysqli.default_user")),($M.$U.$E!=""?$E:ini_get("mysqli.default_pw")),null,(is_numeric($cj)?(int)$cj:ini_get("mysqli.default_port")),(!is_numeric($cj)?$cj:null),$he);$this->mysqli->options(MYSQLI_OPT_LOCAL_INFILE,false);if($Xb){$rf=$this->mysqli->get_server_info();$this->version=str_replace("-MariaDB","",$rf);$this->flavor=str_contains($rf,"MariaDB")?"mariadb":null;}return$Xb;}function
getAffectedRows(){return$this->mysqli->affected_rows;}function
getErrno(){return$this->mysqli->errno;}function
getError(){return$this->mysqli->error;}function
selectDatabase($A){return$this->mysqli->select_db($A);}function
setCharset($rb){if($this->mysqli->set_charset($rb))return
true;$this->mysqli->set_charset('utf8');return(bool)$this->query("SET NAMES $rb");}function
quote($O){return"'".$this->mysqli->escape_string($O)."'";}function
query($G,$vm=false){$H=$this->mysqli->query($G);return
is_object($H)?new
MySqlResult($H):$H;}function
getQueryInfo(){return$this->mysqli->info;}function
multiQuery($G){return$this->mysqli->multi_query($G);}function
storeResult($H=null){$H=$this->mysqli->store_result();if(!$H)return
false;return
new
MySqlResult($H);}function
nextResult(){return$this->mysqli->more_results()&&$this->mysqli->next_result();}}class
MySqlResult
extends
Result{private$resource;function
__construct(mysqli_result$Qj){parent::__construct($Qj->num_rows);$this->resource=$Qj;}function
fetchAssoc(){return$this->resource->fetch_assoc();}function
fetchRow(){return$this->resource->fetch_row();}function
fetchField(){return$this->resource->fetch_field();}function
seek($Mh){return$this->resource->data_seek($Mh);}}}elseif(extension_loaded("pdo_mysql")){define("AdminNeo\DRIVER_EXTENSION","PDO_MySQL");class
MySqlConnection
extends
PdoConnection{function
getDefaultServerName(){return"localhost";}function
open($M,$U,$E){list($af,$cj)=host_port($M);$ed="mysql:charset=utf8".($af!=""?";host=$af":"").($cj?(is_numeric($cj)?";port=":";unix_socket=").$cj:"");$B=[PDO::MYSQL_ATTR_LOCAL_INFILE=>false];$u=Admin::get()->getConfig()->getSslKey();if($u)$B[PDO::MYSQL_ATTR_SSL_KEY]=$u;$ob=Admin::get()->getConfig()->getSslCertificate();if($ob)$B[PDO::MYSQL_ATTR_SSL_CERT]=$ob;$mb=Admin::get()->getConfig()->getSslCaCertificate();if($mb)$B[PDO::MYSQL_ATTR_SSL_CA]=$mb;$qm=Admin::get()->getConfig()->getSslTrustServerCertificate();if($qm!==null&&defined('\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT'))$B[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]=!$qm;if(!$this->dsn($ed,$U,$E,$B))return
false;$Um=@$this->pdo->getAttribute(PDO::ATTR_SERVER_VERSION);$this->flavor=str_contains($Um,"MariaDB")?"mariadb":null;return
true;}function
setCharset($rb){return(bool)$this->query("SET NAMES $rb");}function
selectDatabase($A){return(bool)$this->query("USE ".idf_escape($A));}function
query($G,$vm=false){$this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,!$vm);return
parent::query($G,$vm);}}}class
MySqlDriver
extends
Driver{protected
function
__construct(Connection$e,$_a){parent::__construct($e,$_a);$this->types=[lang(125)=>["tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21,],lang(126)=>["date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4,],lang(127)=>["char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295,],lang(128)=>["enum"=>65535,"set"=>64,],lang(129)=>["bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295,],lang(130)=>["geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0,],];$this->unsigned=["unsigned","zerofill","unsigned zerofill"];$Gg=$e->isMariaDB();if($e->isMinVersion($Gg?"10.2":"5.7"))$this->generated=["STORED","VIRTUAL"];$this->operators=["=","<",">","<=",">=","!=","LIKE","LIKE %%","NOT LIKE","IN","NOT IN","FIND_IN_SET","IS NULL","IS NOT NULL","REGEXP","NOT REGEXP","SQL",];$this->functions=["char_length","lower","upper","round","floor","ceil","date","from_unixtime","unix_timestamp","sec_to_time","time_to_sec",];$this->grouping=["sum","min","max","avg","count","count distinct","group_concat",];$this->partitionBy=["RANGE","LIST","HASH","LINEAR HASH","KEY","LINEAR KEY"];$this->insertFunctions=["char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",];$this->editFunctions=[number_type()=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",];if($e->isMinVersion($Gg?"10.2":"5.7.8"))$this->types[lang(127)]["json"]=4294967295;if($Gg&&$e->isMinVersion("10.7")){$this->types[lang(127)]["uuid"]=128;$this->insertFunctions['uuid']='uuid';}if($Gg&&$e->isMinVersion("10.5")){$this->types[lang(131)]["inet6"]=39;if($e->isMinVersion("10.10"))$this->types[lang(131)]["inet4"]=15;}if($e->isMinVersion($Gg?"11.7":"9"))$this->types[lang(125)]["vector"]=16383;$this->systemDatabases=["mysql","information_schema","performance_schema","sys"];}function
insert($P,array$Bj){return($Bj?parent::insert($P,$Bj):queries("INSERT INTO ".table($P)." ()\nVALUES ()"));}function
getUnconvertFunction(array$k){if(preg_match("~binary~",$k["type"]))return"<code class='jush-sql'>UNHEX</code>";elseif($k["type"]=="bit")return
doc_link(['sql'=>'bit-value-literals.html','mariadb'=>"reference/sql-structure/sql-language-structure/binary-literals"],"<code>b''</code>");elseif($k["type"]=="vector")return"<code class='jush-sql'>".($this->connection->isMariaDB()?"VEC_FromText":"STRING_TO_VECTOR")."</code>";elseif(preg_match("~geometry|point|linestring|polygon~",$k["type"]))return"<code class='jush-sql'>GeomFromText</code>";else
return"";}function
getTypeName(stdClass$k){$um=["decimal","tinyint","smallint","int","float","double",7=>"timestamp","bigint","mediumint","date","time","datetime","year",15=>"varchar","bit",242=>"vector",245=>"json","decimal","enum","set","tinytext","mediumtext","longtext","text","varchar","char","geometry",];$T=isset($um[$k->type])?$um[$k->type]:"";return
parent::getTypeName($k)?:($k->charsetnr==63?str_replace(["text","varchar","char"],["blob","varbinary","binary"],$T):$T);}function
quoteBinary($O){return"X".q(bin2hex($O));}function
insertUpdate($P,array$Cj,array$lj){$c=array_keys(reset($Cj));$ij="INSERT INTO ".table($P)." (".implode(", ",$c).") VALUES\n";$Y=[];foreach($c
as$u)$Y[$u]="$u = VALUES($u)";$ml="\nON DUPLICATE KEY UPDATE ".implode(", ",$Y);$Y=[];$v=0;foreach($Cj
as$Bj){$X="(".implode(", ",$Bj).")";if($Y&&(strlen($ij)+$v+strlen($X)+strlen($ml)>1e6)){if(!queries($ij.implode(",\n",$Y).$ml))return
false;$Y=[];$v=0;}$Y[]=$X;$v+=strlen($X)+2;}return
queries($ij.implode(",\n",$Y).$ml);}function
slowQuery($G,$Yl){$Gg=$this->connection->isMariaDB();if(!$this->connection->isMinVersion($Gg?"10.1.2":"5.7.8"))return
null;if($Gg)return"SET STATEMENT max_statement_time=$Yl FOR $G";elseif(preg_match('~^(SELECT\b)(.+)~is',$G,$z))return"$z[1] /*+ MAX_EXECUTION_TIME(".($Yl*1000).") */ $z[2]";else
return
null;}function
convertSearch($if,array$Z,array$k){return(preg_match('~char|text|enum|set~',$k["type"])&&!preg_match("~^utf8~",$k["collation"])&&preg_match('~[\x80-\xFF]~',$Z['val'])?"CONVERT($if USING ".charset($this->connection).")":$if);}function
warnings(){$H=$this->connection->query("SHOW WARNINGS");if($H&&$H->getRowsCount()){ob_start();print_select_result($H);return
ob_get_clean();}return
null;}function
tableHelp($A,$Gf=false){$Gg=$this->connection->isMariaDB();if(DB=="information_schema"){$A=strtolower($A);return$Gg?"reference/system-tables/information-schema/information-schema-tables/".(str_starts_with($A,"innodb_")?"information-schema-innodb-tables/":"")."information-schema-$A-table":"information-schema-".str_replace("_","-",$A)."-table.html";}if(DB=="performance_schema")return$Gg?"reference/system-tables/performance-schema/performance-schema-tables/performance-schema-$A-table":"performance-schema-".str_replace("_","-",$A)."-table.html";if(DB=="sys"){if($Gg)return"reference/system-tables/sys-schema/";return"sys-".strtolower(str_replace("_","-",preg_replace('~^x\$~','',$A))).".html";}if(DB=="mysql")return$Gg?"reference/system-tables/the-mysql-database-tables/mysql-$A".str_starts_with($A,"innodb_")?"":"-table":"system-schema.html";return
null;}function
getPartitionsInfo($P){$ve="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($P);$H=Connection::get()->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $ve ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1")->fetchRow();if(!$H)return[];$rf=["partition_by"=>$H[0],"partition"=>$H[1],"partitions"=>$H[2],];$Mi=get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $ve AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");$rf["partition_names"]=array_keys($Mi);$rf["partition_values"]=array_values($Mi);return$rf;}function
getIndexAlgorithms(array$yl){return
preg_match('~^(MEMORY|NDB)$~',$yl["Engine"])?["BTREE","HASH"]:["BTREE"];}function
hasCStyleEscapes(){static$kb;if($kb===null){$al=$this->connection->getValue("SHOW VARIABLES LIKE 'sql_mode'",1);$kb=(strpos($al,'NO_BACKSLASH_ESCAPES')===false);}return$kb;}function
engines(){$vd=[];foreach(get_rows("SHOW ENGINES")as$J){if(preg_match("~YES|DEFAULT~",$J["Support"]))$vd[]=$J["Engine"];}return$vd;}}function
create_driver(Connection$e){return
MySqlDriver::create($e,Admin::get());}function
idf_escape($if){return"`".str_replace("`","``",$if)."`";}function
table($if){return
idf_escape($if);}function
connect($lj=false,&$j=null){$e=$lj?MySqlConnection::create():MySqlConnection::createSecondary();list($M,$U,$E)=Admin::get()->getCredentials();if(!$e->openPasswordless($M,$U,$E,false)){$j=$e->getError();if(function_exists('iconv')&&!is_utf8($j)&&strlen($bk=iconv("windows-1252","utf-8//IGNORE",$j))>strlen($j))$j=$bk;return
null;}$e->setCharset(charset($e));$e->query("SET sql_quote_show_create = 1, autocommit = 1");if($lj&&$e->isMariaDB()){Drivers::setName(DRIVER,"MariaDB");save_driver_name(DRIVER,$M,"MariaDB");}return$e;}function
get_databases($je){$g=get_session("dbs");if($g===null){$G="SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";$cl=microtime(true);$g=($je?slow_query($G):get_vals($G));if(microtime(true)-$cl>0.1){restart_session();set_session("dbs",$g);stop_session();}}return$g;}function
limit($G,$Z,$w,$Mh=0,$wk=" "){return" $G$Z".($w?$wk."LIMIT $w".($Mh?" OFFSET $Mh":""):"");}function
limit1($P,$G,$Z,$wk="\n"){return
limit($G,$Z,1,0,$wk);}function
db_collation($h,array$Fb){$I=null;$ic=Connection::get()->getValue("SHOW CREATE DATABASE ".idf_escape($h),1);if(preg_match('~ COLLATE ([^ ]+)~',$ic,$z))$I=$z[1];elseif(preg_match('~ CHARACTER SET ([^ ]+)~',$ic,$z))$I=$Fb[$z[1]][-1];return$I;}function
logged_user(){return
Connection::get()->getValue("SELECT USER()");}function
tables_list(){return
get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");}function
count_tables(array$g){$I=[];foreach($g
as$h)$I[$h]=count(get_vals("SHOW TABLES IN ".idf_escape($h)));return$I;}function
table_status($A="",$Rd=false){if($Rd)$G="SELECT TABLE_NAME AS Name, ENGINE AS Engine, CREATE_OPTIONS AS Create_options,
	TABLES.TABLE_COLLATION AS Collation, TABLE_COMMENT AS Comment
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE() ".($A!=""?"AND TABLE_NAME = ".q($A):"ORDER BY Name");else$G="SHOW TABLE STATUS".($A!=""?" LIKE ".q(addcslashes($A,"%_\\")):"");$R=[];foreach(get_rows($G)as$J){if($J["Engine"]=="InnoDB")$J["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\1',$J["Comment"]);if(!isset($J["Engine"]))$J["Comment"]="";if($A!="")$J["Name"]=$A;$R[$J["Name"]]=$J;}return$R;}function
is_view(array$Q){return$Q["Engine"]===null;}function
fk_support(array$Q){return
preg_match('~InnoDB|IBMDB2I'.(Connection::get()->isMinVersion("5.6")?'|NDB':'').'~i',$Q["Engine"]);}function
fields($P){$Gg=Connection::get()->isMariaDB();$I=[];foreach(get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ".q($P)." ORDER BY ORDINAL_POSITION")as$J){$k=$J["COLUMN_NAME"];$T=preg_replace('~\s?/\*.+\*/~U',"",$J["COLUMN_TYPE"]);$Nd=$J["EXTRA"];preg_match('~^(VIRTUAL|PERSISTENT|STORED)~',$Nd,$ze);preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~',$T,$tm);$i=$Gg&&$J["COLUMN_DEFAULT"]=="NULL"?null:$J["COLUMN_DEFAULT"];if($i!==null){$Lf=preg_match('~(text|json)~',$tm[1]);if(!$Gg&&$Lf)$i=preg_replace("~^(_\w+)?('.*')$~",'\2',stripslashes($i));if($Gg||$Lf){$i=preg_replace_callback("~^'(.*)'$~",function($_){return
stripslashes(str_replace("''","'",$_[1]));},$i);}if(!$Gg&&preg_match('~binary~',$tm[1])&&preg_match('~^0x(\w*)$~',$i,$_))$i=pack("H*",$_[1]);}$Ae=$J["GENERATION_EXPRESSION"];if(!$Gg)$Ae=preg_replace("~(^|,|\()(_\w+)?('.*')($|,|\))~",'\1\3\4',stripslashes($Ae));$I[$k]=["field"=>$k,"full_type"=>$T,"type"=>$tm[1],"length"=>$tm[2],"unsigned"=>ltrim($tm[3].$tm[4]),"default"=>($ze?$Ae:$i),"null"=>($J["IS_NULLABLE"]=="YES"),"auto_increment"=>($Nd=="auto_increment"),"on_update"=>(preg_match('~\bon update (\w+)~i',$Nd,$tm)?$tm[1]:""),"collation"=>$J["COLLATION_NAME"],"privileges"=>array_flip(explode(",",$J["PRIVILEGES"]))+["where"=>1,"order"=>1],"comment"=>$J["COLUMN_COMMENT"],"primary"=>($J["COLUMN_KEY"]=="PRI"),"generated"=>($ze[1]=="PERSISTENT"?"STORED":$ze[1]),];}return$I;}function
indexes($P,$e=null){$I=[];foreach(get_rows("SHOW INDEX FROM ".table($P),$e)as$J){$A=$J["Key_name"];$I[$A]["type"]=($A=="PRIMARY"?"PRIMARY":($J["Index_type"]=="FULLTEXT"?"FULLTEXT":($J["Non_unique"]?(preg_match('~^(SPATIAL|VECTOR)$~',$J["Index_type"])?$J["Index_type"]:"INDEX"):"UNIQUE")));$I[$A]["columns"][]=$J["Column_name"];$I[$A]["lengths"][]=($J["Index_type"]=="SPATIAL"?null:$J["Sub_part"]);$I[$A]["descs"][]=($J["Collation"]=="D"?'1':null);$I[$A]["algorithm"]=$J["Index_type"];}return$I;}function
foreign_keys($P){static$Si='(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';$I=[];$kc=Connection::get()->getValue("SHOW CREATE TABLE ".table($P),1);if($kc){$Wh=implode("|",Driver::get()->getOnActions());preg_match_all("~CONSTRAINT ($Si) FOREIGN KEY ?\\(((?:$Si,? ?)+)\\) REFERENCES ($Si)(?:\\.($Si))? "."\\(((?:$Si,? ?)+)\\)(?: ON DELETE ($Wh))?(?: ON UPDATE ($Wh))?~",$kc,$_,PREG_SET_ORDER);foreach($_
as$z){preg_match_all("~$Si~",$z[2],$Uk);preg_match_all("~$Si~",$z[5],$Nl);$I[idf_unescape($z[1])]=["db"=>idf_unescape($z[4]!=""?$z[3]:$z[4]),"table"=>idf_unescape($z[4]!=""?$z[4]:$z[3]),"source"=>array_map('AdminNeo\idf_unescape',$Uk[0]),"target"=>array_map('AdminNeo\idf_unescape',$Nl[0]),"on_delete"=>($z[6]?:"RESTRICT"),"on_update"=>($z[7]?:"RESTRICT"),];}}return$I;}function
backward_keys($P){$G="SELECT CONSTRAINT_NAME AS constraint_name, TABLE_SCHEMA AS table_schema, TABLE_NAME AS table_name,
COLUMN_NAME AS column_name, REFERENCED_COLUMN_NAME AS referenced_column_name
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = ".q(Admin::get()->getDatabase())."
AND REFERENCED_TABLE_SCHEMA = ".q(Admin::get()->getDatabase())."
AND REFERENCED_TABLE_NAME = ".q($P)."
ORDER BY ORDINAL_POSITION";return
get_rows($G,null,"");}function
view($A){$L=Connection::get()->getValue("SHOW CREATE VIEW ".table($A),1);$Dg='(?:[^`\']|`[^`]*`|\'[^\']*\')*';$L=preg_replace("~^$Dg\\s+AS\\s+~isU","",$L);return["select"=>format_sql($L)];}function
collations(){$I=[];$G=Connection::get()->isMariaDB()&&Connection::get()->isMinVersion("10.10")?"SELECT CHARACTER_SET_NAME AS Charset, FULL_COLLATION_NAME AS Collation, IS_DEFAULT AS `Default` FROM information_schema.COLLATION_CHARACTER_SET_APPLICABILITY":"SHOW COLLATION";foreach(get_rows($G)as$J){if($J["Default"])$I[$J["Charset"]][-1]=$J["Collation"];else$I[$J["Charset"]][]=$J["Collation"];}ksort($I);foreach($I
as$u=>$W)sort($I[$u]);return$I;}function
information_schema($h,$jk=""){return($h=="information_schema")||(Connection::get()->isMinVersion("5.5")&&$h=="performance_schema");}function
error(){return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",Connection::get()->getError()));}function
create_database($h,$Eb){return(bool)queries("CREATE DATABASE ".idf_escape($h).($Eb?" COLLATE ".q($Eb):""));}function
drop_databases(array$g){$I=apply_queries("DROP DATABASE",$g,'AdminNeo\idf_escape');restart_session();set_session("dbs",null);return$I;}function
rename_database($A,$Eb){$I=false;if(create_database($A,$Eb)){$R=[];$Xm=[];foreach(tables_list()as$P=>$T){if($T=='VIEW')$Xm[]=$P;else$R[]=$P;}$I=(!$R&&!$Xm)||move_tables($R,$Xm,$A);drop_databases($I?[DB]:[]);}return$I;}function
auto_increment(){$Ra=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$s){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$s["columns"],true)){$Ra="";break;}if($s["type"]=="PRIMARY")$Ra=" UNIQUE";}}return" AUTO_INCREMENT$Ra";}function
alter_table($P,$A,array$l,array$le,$Ob,$ud,$Eb,$Qa,$Li){$Ia=[];foreach($l
as$k){if($k[1]){$i=$k[1][3];if(str_contains($i," GENERATED")){$k[1][3]=Connection::get()->isMariaDB()?"":$k[1][2];$k[1][2]=$i;}$Ia[]=($P!=""?($k[0]!=""?"CHANGE ".idf_escape($k[0]):"ADD"):" ")." ".implode($k[1]).($P!=""?$k[2]:"");}else$Ia[]="DROP ".idf_escape($k[0]);}$Ia=array_merge($Ia,$le);$el=($Ob!==null?" COMMENT=".q($Ob):"").($ud?" ENGINE=".q($ud):"").($Eb?" COLLATE ".q($Eb):"").($Qa!=""?" AUTO_INCREMENT=$Qa":"");if($Li){$Mi=[];if($Li["partition_by"]=='RANGE'||$Li["partition_by"]=='LIST'){foreach($Li["partition_names"]as$u=>$W){$X=$Li["partition_values"][$u];$Mi[]="\n  PARTITION ".idf_escape($W)." VALUES ".($Li["partition_by"]=='RANGE'?"LESS THAN":"IN").($X!=""?" ($X)":" MAXVALUE");}}$el
.="\nPARTITION BY {$Li["partition_by"]}({$Li["partition"]})";if($Mi)$el
.=" (".implode(",",$Mi)."\n)";elseif($Li["partitions"])$el
.=" PARTITIONS ".(int)$Li["partitions"];}elseif($Li===null)$el
.="\nREMOVE PARTITIONING";if($P=="")return(bool)queries("CREATE TABLE ".table($A)." (\n".implode(",\n",$Ia)."\n)$el");if($P!=$A)$Ia[]="RENAME TO ".table($A);if($el)$Ia[]=ltrim($el);return!$Ia||queries("ALTER TABLE ".table($P)."\n".implode(",\n",$Ia));}function
alter_indexes($P,array$Ia){$qb=[];foreach($Ia
as$u=>$W)$qb[]=($W[2]=="DROP"?"\nDROP INDEX ".idf_escape($W[1]):"\nADD $W[0] ".($W[0]=="PRIMARY"?"KEY ":"").($W[1]!=""?idf_escape($W[1])." ":"")."(".implode(", ",$W[2]).")");return(bool)queries("ALTER TABLE ".table($P).implode(",",$qb));}function
truncate_tables(array$R){return
apply_queries("TRUNCATE TABLE",$R);}function
drop_views(array$Xm){return(bool)queries("DROP VIEW ".implode(", ",array_map('AdminNeo\table',$Xm)));}function
drop_tables(array$R){return(bool)queries("DROP TABLE ".implode(", ",array_map('AdminNeo\table',$R)));}function
move_tables(array$R,array$Xm,$Nl){$Nj=[];foreach($R
as$P)$Nj[]=table($P)." TO ".idf_escape($Nl).".".table($P);if(!$Nj||queries("RENAME TABLE ".implode(", ",$Nj))){$Ec=[];foreach($Xm
as$P)$Ec[table($P)]=view($P);Connection::get()->selectDatabase($Nl);$h=idf_escape(DB);foreach($Ec
as$A=>$Vm){if(!queries("CREATE VIEW $A AS ".str_replace(" $h."," ",$Vm["select"]))||!queries("DROP VIEW $h.$A"))return
false;}return
true;}return
false;}function
copy_tables(array$R,array$Xm,$Nl){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($R
as$P){$A=($Nl==DB?table("copy_$P"):idf_escape($Nl).".".table($P));if(($_POST["overwrite"]&&!queries("\nDROP TABLE IF EXISTS $A"))||!queries("CREATE TABLE $A LIKE ".table($P))||!queries("INSERT INTO $A SELECT * FROM ".table($P)))return
false;foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($P,"%_\\")))as$J){$mm=$J["Trigger"];if(!queries("CREATE TRIGGER ".($Nl==DB?idf_escape("copy_$mm"):idf_escape($Nl).".".idf_escape($mm))." $J[Timing] $J[Event] ON $A FOR EACH ROW\n$J[Statement];"))return
false;}}foreach($Xm
as$P){$A=($Nl==DB?table("copy_$P"):idf_escape($Nl).".".table($P));$Vm=view($P);if(($_POST["overwrite"]&&!queries("DROP VIEW IF EXISTS $A"))||!queries("CREATE VIEW $A AS $Vm[select]"))return
false;}return
true;}function
trigger($A,$P){if($A=="")return[];$K=get_rows("SHOW TRIGGERS WHERE `Trigger` = ".q($A));return
reset($K);}function
triggers($P){$I=[];foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($P,"%_\\")))as$J)$I[$J["Trigger"]]=[$J["Timing"],$J["Event"]];return$I;}function
trigger_options(){return["Timing"=>["BEFORE","AFTER"],"Event"=>["INSERT","UPDATE","DELETE"],"Type"=>["FOR EACH ROW"],];}function
routine($A,$T){if($A=="")return[];$l=get_rows("SELECT
	PARAMETER_NAME field,
	DATA_TYPE type,
	REGEXP_REPLACE(DTD_IDENTIFIER, '^[^(]+\\\\(?|\\\\)$', '') length,
	REGEXP_REPLACE(DTD_IDENTIFIER, '^[^ ]+ ', '') `unsigned`,
	1 `null`,
	DTD_IDENTIFIER full_type,
	".($T=="FUNCTION"?"''":"PARAMETER_MODE")." `inout`,
	CHARACTER_SET_NAME collation
FROM information_schema.PARAMETERS
WHERE SPECIFIC_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$T' AND SPECIFIC_NAME = ".q($A)."
ORDER BY ORDINAL_POSITION");$I=Connection::get()->query("SELECT
	ROUTINE_COMMENT comment,
	CONCAT(IF(IS_DETERMINISTIC = 'YES', 'DETERMINISTIC\\n', ''), IF(SQL_DATA_ACCESS != 'CONTAINS SQL', CONCAT(SQL_DATA_ACCESS, '\\n'), ''), ROUTINE_DEFINITION) definition,
	'SQL' language
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$T' AND ROUTINE_NAME = ".q($A))->fetchAssoc();if($l&&$l[0]['field']=='')$I['returns']=array_shift($l);$I['fields']=$l;return$I;}function
routines(){return
get_rows("SELECT SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER, ROUTINE_COMMENT FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()");}function
routine_languages(){return[];}function
routine_id($A,array$J){return
idf_escape($A);}function
last_id($H){return
Connection::get()->getValue("SELECT LAST_INSERT_ID()");}function
explain(Connection$e,$G){return$e->query("EXPLAIN ".(Connection::get()->isMinVersion("5.7")?"":"PARTITIONS ").$G);}function
found_rows(array$Q,array$Z){return$Q["Engine"]=="InnoDB"&&!$Z?(int)$Q["Rows"]:null;}function
format_sql($G){$Dg='(?:[^`\']|`[^`]*`|\'[^\']*\')*';$Yf='FROM|WHERE|HAVING|GROUP\s+BY|ORDER\s+BY|(NATURAL\s+)?((LEFT|RIGHT)\s+)?((INNER|OUTER|CROSS)\s+)?JOIN';$G=preg_replace("~($Dg)\\s+(AS\\s+SELECT)~isU","$1 AS\nSELECT",$G);$G=preg_replace("~($Dg)\\s+($Yf)~isU","$1\n$2",$G);$G=preg_replace("~($Dg),~isU","$1,\n  ",$G);return$G;}function
create_sql($P,$Qa,$jl){$G=Connection::get()->getValue("SHOW CREATE TABLE ".table($P),1);if(!$Qa)$G=preg_replace('~ AUTO_INCREMENT=\d+~','',$G);return!str_contains($G,"\n")?format_sql($G):$G;}function
truncate_sql($P){return"TRUNCATE ".table($P);}function
create_database_sql($vc,$jl=""){$A=idf_escape($vc);$Mb="";if(str_contains($jl,"CREATE")&&($ic=Connection::get()->getValue("SHOW CREATE DATABASE $A",1))){set_utf8mb4($ic);if($jl=="DROP+CREATE")$Mb="DROP DATABASE IF EXISTS $A;\n";$Mb
.="$ic;\n";}return$Mb;}function
use_sql($vc,$jl=""){return"USE ".idf_escape($vc).";\n";}function
trigger_sql($P){$Xk="";foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($P,"%_\\")),null,"-- ")as$J)$Xk
.="\nCREATE TRIGGER ".idf_escape($J["Trigger"])." $J[Timing] $J[Event] ON ".table($J["Table"])." FOR EACH ROW\n$J[Statement];;\n";return$Xk;}function
show_variables(){return
get_rows("SHOW VARIABLES");}function
show_status(){return
get_rows("SHOW STATUS");}function
process_list(){return
get_rows("SHOW FULL PROCESSLIST");}function
convert_field(array$k){if(preg_match("~binary~",$k["type"]))return"HEX(".idf_escape($k["field"]).")";if($k["type"]=="bit")return"BIN(".idf_escape($k["field"])." + 0)";if($k["type"]=="vector")return(Connection::get()->isMariaDB()?"VEC_ToText":"VECTOR_TO_STRING")."(".idf_escape($k["field"]).")";if(preg_match("~geometry|point|linestring|polygon~",$k["type"]))return(Connection::get()->isMinVersion("8")?"ST_":"")."AsWKT(".idf_escape($k["field"]).")";return
null;}function
unconvert_field(array$k,$I){if(preg_match("~binary~",$k["type"]))$I="UNHEX($I)";if($k["type"]=="bit")$I="CONVERT(b$I, UNSIGNED)";if($k["type"]=="vector")$I=(Connection::get()->isMariaDB()?"VEC_FromText":"STRING_TO_VECTOR")."($I)";if(preg_match("~geometry|point|linestring|polygon~",$k["type"])){$ij=(Connection::get()->isMinVersion("8")?"ST_":"");$I=$ij."GeomFromText($I, $ij"."SRID($k[field]))";}return$I;}function
support($Sd){return
preg_match('~^(comment|columns|copy|database|drop_col|dump|event|indexes|kill|privileges|move_col|procedure|processlist|routine|sql|status|table|trigger|variables|view'.(Connection::get()->isMinVersion(Connection::get()->isMariaDB()?"10.8.1":"8")?'|descidx':'').(Connection::get()->isMinVersion(Connection::get()->isMariaDB()?"10.2.1":"8.0.16")?'|check':'').(!Connection::get()->isMariaDB()&&Connection::get()->isMinVersion("8")?'|fast_status':'').')$~',$Sd);}function
kill_process($W){return
queries("KILL ".number($W));}function
connection_id(){return"SELECT CONNECTION_ID()";}function
max_connections(){return(int)Connection::get()->getValue("SELECT @@max_connections");}}Drivers::add("mssql","MS SQL",["SQLSRV","PDO_SQLSRV","PDO_DBLIB"]);if(isset($_GET["mssql"])){define("AdminNeo\DRIVER","mssql");define("AdminNeo\DIALECT","mssql");if(extension_loaded("sqlsrv")&&$_GET["ext"]!="pdo"&&$_GET["ext"]!="dblib"){define("AdminNeo\DRIVER_EXTENSION","sqlsrv");class
MsSqlConnection
extends
Connection{private$connection;protected$multiResult;function
getDefaultServerName(){return"localhost:1433";}function
open($M,$U,$E){$Zb=["UID"=>$U,"PWD"=>$E,"CharacterSet"=>"UTF-8",];$sd=Admin::get()->getConfig()->getSslEncrypt();if($sd!==null)$Zb["Encrypt"]=$sd;$pm=Admin::get()->getConfig()->getSslTrustServerCertificate();if($pm!==null)$Zb["TrustServerCertificate"]=$pm;$h=Admin::get()->getDatabase();if($h!="")$Zb["Database"]=$h;$this->connection=@sqlsrv_connect(implode(",",host_port($M)),$Zb);if($this->connection){$rf=sqlsrv_server_info($this->connection);$this->version=$rf['SQLServerVersion'];}else$this->resolveError();return(bool)$this->connection;}private
function
resolveError(){$this->error="";foreach(sqlsrv_errors()as$j){$this->errno=$j["code"];$this->error
.="$j[message]\n";}$this->error=rtrim($this->error);}function
quote($O){return(contains_unicode($O)?"N":"")."'".str_replace("'","''",$O)."'";}function
selectDatabase($A){return(bool)$this->query(use_sql($A));}function
query($G,$vm=false){$H=sqlsrv_query($this->connection,$G);$this->error="";if(!$H){$this->resolveError();return
false;}return$this->storeResult($H);}function
multiQuery($G){$this->multiResult=sqlsrv_query($this->connection,$G);$this->error="";if(!$this->multiResult){$this->resolveError();return
false;}return
true;}function
storeResult($H=null){if(!$H){$H=$this->multiResult;if(!$H)return
false;}if(sqlsrv_field_metadata($H))return
new
MsSqlResult($H);$this->affectedRows=sqlsrv_rows_affected($H);return
true;}function
nextResult(){return$this->multiResult&&sqlsrv_next_result($this->multiResult);}}class
MsSqlResult
extends
Result{private$resource;private$fields=false;private$offset=0;function
__construct($Qj){parent::__construct(0);$this->resource=$Qj;}function
fetchAssoc(){return$this->convertRow(sqlsrv_fetch_array($this->resource,SQLSRV_FETCH_ASSOC));}function
fetchRow(){return$this->convertRow(sqlsrv_fetch_array($this->resource,SQLSRV_FETCH_NUMERIC));}private
function
convertRow($J){if(is_array($J)){foreach($J
as$u=>$W){if(is_a($W,'DateTime'))$J[$u]=$W->format("Y-m-d H:i:s");}}return$J;}function
fetchField(){if(!$this->fields){$this->fields=sqlsrv_field_metadata($this->resource);if(!$this->fields)return
false;}$k=$this->fields[$this->offset++];return(object)['name'=>$k["Name"],'type'=>($k["Type"]==1?254:15),'charsetnr'=>(in_array($k["Type"],[-2,-3,-4])?63:0),];}function
seek($Mh){for($q=0;$q<$Mh;$q++){if(!sqlsrv_fetch($this->resource))return
false;}return
true;}}function
last_id($H){return
Connection::get()->getValue("SELECT SCOPE_IDENTITY()");}function
explain(Connection$e,$G){$e->query("SET SHOWPLAN_ALL ON");$I=$e->query($G);$e->query("SET SHOWPLAN_ALL OFF");return$I;}}else{abstract
class
MsSqlPdoConnection
extends
PdoConnection{function
getDefaultServerName(){return"localhost:1433";}function
selectDatabase($A){return(bool)$this->query(use_sql($A));}function
quote($O){return(contains_unicode($O)?"N":"").parent::quote($O);}function
lastInsertId(){return$this->pdo->lastInsertId();}}if((extension_loaded("pdo_sqlsrv")&&$_GET["ext"]!="dblib")||$_GET["ext"]=="pdo"){define("AdminNeo\DRIVER_EXTENSION","PDO_SQLSRV");class
MsSqlConnection
extends
MsSqlPdoConnection{function
open($M,$U,$E){$B=[];$sd=Admin::get()->getConfig()->getSslEncrypt();if($sd!==null)$B[]="Encrypt=$sd";$pm=Admin::get()->getConfig()->getSslTrustServerCertificate();if($pm!==null)$B[]="TrustServerCertificate=$pm";$hi=$B?(";".implode(";",$B)):"";return$this->dsn("sqlsrv:Server=".implode(",",host_port($M)).$hi,$U,$E);}}}elseif(extension_loaded("pdo_dblib")){define("AdminNeo\DRIVER_EXTENSION","PDO_DBLIB");class
MsSqlConnection
extends
MsSqlPdoConnection{function
open($M,$U,$E){list($af,$cj)=host_port($M);$H=$this->dsn("dblib:charset=utf8;host=$af".($cj?(is_numeric($cj)?";port=":";unix_socket=").$cj:""),$U,$E);if($H)$this->query("SET ANSI_NULLS ON; SET ANSI_PADDING ON; SET CONCAT_NULL_YIELDS_NULL ON; SET ANSI_WARNINGS ON;");return$H;}}}function
last_id($H){$e=Connection::get();return$e->lastInsertId();}function
explain(Connection$e,$G){}}class
MsSqlDriver
extends
Driver{protected
function
__construct(Connection$e,$_a){parent::__construct($e,$_a);$this->types=[lang(125)=>["tinyint"=>3,"smallint"=>5,"int"=>10,"bigint"=>20,"bit"=>1,"decimal"=>0,"real"=>12,"float"=>53,"smallmoney"=>10,"money"=>20,],lang(126)=>["date"=>10,"smalldatetime"=>19,"datetime"=>19,"datetime2"=>19,"time"=>8,"datetimeoffset"=>10,],lang(127)=>["char"=>8000,"varchar"=>8000,"text"=>2147483647,"nchar"=>4000,"nvarchar"=>4000,"ntext"=>1073741823,],lang(129)=>["binary"=>8000,"varbinary"=>8000,"image"=>2147483647,],];$this->generated=["PERSISTED","VIRTUAL"];$this->operators=["=","<",">","<=",">=","!=","LIKE","LIKE %%","NOT LIKE","IN","NOT IN","IS NULL","IS NOT NULL",];$this->functions=["len","lower","upper","round",];$this->grouping=["sum","min","max","avg","count","count distinct",];$this->onActions=["CASCADE","SET NULL","SET DEFAULT","NO ACTION"];$this->insertFunctions=["date|time"=>"getdate"];$this->editFunctions=["int|decimal|real|float|money|datetime"=>"+/-","char|text"=>"+",];$this->systemSchemas=["INFORMATION_SCHEMA","guest","sys","db_*"];}function
insertUpdate($P,array$Cj,array$lj){$l=fields($P);$Dm=[];$Z=[];$Bj=reset($Cj);$c="c".implode(", c",range(1,count($Bj)));$jb=0;$wf=[];foreach($Bj
as$u=>$W){$jb++;$A=idf_unescape($u);if(!$l[$A]["auto_increment"])$wf[$u]="c$jb";if(isset($lj[$A]))$Z[]="$u = c$jb";else$Dm[]="$u = c$jb";}$Y=[];foreach($Cj
as$Bj)$Y[]="(".implode(", ",$Bj).")";if($Z){$gf=queries("SET IDENTITY_INSERT ".table($P)." ON");$I=queries("MERGE ".table($P)." USING (VALUES\n\t".implode(",\n\t",$Y)."\n) AS source ($c) ON ".implode(" AND ",$Z).($Dm?"\nWHEN MATCHED THEN UPDATE SET ".implode(", ",$Dm):"")."\nWHEN NOT MATCHED THEN INSERT (".implode(", ",array_keys($gf?$Bj:$wf)).") VALUES (".($gf?$c:implode(", ",$wf)).");");if($gf)queries("SET IDENTITY_INSERT ".table($P)." OFF");}else$I=queries("INSERT INTO ".table($P)." (".implode(", ",array_keys($Bj)).") VALUES\n".implode(",\n",$Y));return$I;}function
quoteBinary($O){return"0x".bin2hex($O);}function
begin(){return
queries("BEGIN TRANSACTION");}function
tableHelp($A,$Gf=false){$yg=["sys"=>"catalog-views/sys-","INFORMATION_SCHEMA"=>"information-schema-views/",];$x=$yg[get_schema()];if($x)return"relational-databases/system-$x".preg_replace('~_~','-',strtolower($A))."-transact-sql";return
null;}}function
create_driver(Connection$e){return
MsSqlDriver::create($e,Admin::get());}function
contains_unicode($O){return
strlen($O)!=strlen(utf8_decode($O));}function
idf_escape($if){return"[".str_replace("]","]]",$if)."]";}function
table($if){return($_GET["ns"]!=""?idf_escape($_GET["ns"]).".":"").idf_escape($if);}function
connect($lj=false,&$j=null){$e=$lj?MsSqlConnection::create():MsSqlConnection::createSecondary();$mc=Admin::get()->getCredentials();if($mc[0]=="")$mc[0]="localhost:1433";if(!$e->open($mc[0],$mc[1],$mc[2])){$j=$e->getError();return
null;}return$e;}function
get_databases($je){return
get_vals("SELECT name FROM sys.databases WHERE name NOT IN ('master', 'tempdb', 'model', 'msdb') ORDER BY name");}function
limit($G,$Z,$w,$Mh=0,$wk=" "){return($w?" TOP (".($w+$Mh).")":"")." $G$Z";}function
limit1($P,$G,$Z,$wk="\n"){return
limit($G,$Z,1,0,$wk);}function
db_collation($h,array$Fb){return
Connection::get()->getValue("SELECT collation_name FROM sys.databases WHERE name = ".q($h));}function
logged_user(){return
Connection::get()->getValue("SELECT SUSER_NAME()");}function
tables_list(){return
get_key_vals("SELECT name, type_desc FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ORDER BY name");}function
count_tables(array$g){$I=[];foreach($g
as$h){Connection::get()->selectDatabase($h);$I[$h]=Connection::get()->getValue("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES");}return$I;}function
table_status($A="",$Rd=false){$I=[];$Pk=[];foreach(get_rows("SELECT object_id, SUM(CASE WHEN index_id < 2 THEN row_count ELSE 0 END) AS [Rows],
SUM(CASE WHEN index_id < 2 THEN used_page_count ELSE 0 END) * 8192 AS Data_length,
SUM(CASE WHEN index_id > 1 THEN used_page_count ELSE 0 END) * 8192 AS Index_length,
SUM(reserved_page_count - used_page_count) * 8192 AS Data_free
FROM sys.dm_db_partition_stats
GROUP BY object_id",null,"")as$J){$Lh=$J["object_id"];unset($J["object_id"]);$Pk[$Lh]=$J;}foreach(get_rows("SELECT ao.object_id, ao.name AS Name, ao.type_desc AS Engine,
	(SELECT cast(value as varchar(max)) FROM fn_listextendedproperty(default, 'SCHEMA', schema_name(schema_id), 'TABLE', ao.name, null, null)) AS Comment
FROM sys.all_objects AS ao
WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ".($A!=""?"AND name = ".q($A):"ORDER BY name"))as$J){$Lh=$J["object_id"];unset($J["object_id"]);$I[$J["Name"]]=$J+(isset($Pk[$Lh])?$Pk[$Lh]:[]);}return$I;}function
is_view(array$Q){return$Q["Engine"]=="VIEW";}function
fk_support(array$Q){return
true;}function
fields($P){$Qb=get_key_vals("SELECT objname, cast(value as varchar(max))
FROM fn_listextendedproperty('MS_DESCRIPTION', 'schema', ".q(get_schema()).", 'table', ".q($P).", 'column', NULL)");$I=[];$_l=Connection::get()->getValue("SELECT object_id FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') AND name = ".q($P));foreach(get_rows("SELECT c.max_length, c.precision, c.scale, c.name, c.is_nullable, c.is_identity, c.collation_name,
	t.name type, d.definition [default], d.name default_constraint, i.is_primary_key
FROM sys.all_columns c
JOIN sys.types t ON c.user_type_id = t.user_type_id
LEFT JOIN sys.default_constraints d ON c.default_object_id = d.object_id
LEFT JOIN sys.index_columns ic ON c.object_id = ic.object_id AND c.column_id = ic.column_id
LEFT JOIN sys.indexes i ON ic.object_id = i.object_id AND ic.index_id = i.index_id
WHERE c.object_id = ".q($_l))as$J){$T=$J["type"];$v=(preg_match("~char|binary~",$T)?intval($J["max_length"])/($T[0]=='n'?2:1):($T=="decimal"?"$J[precision],$J[scale]":""));$I[$J["name"]]=["field"=>$J["name"],"full_type"=>$T.($v?"($v)":""),"type"=>$T,"length"=>$v,"default"=>(preg_match("~^\('(.*)'\)$~s",$J["default"],$z)?str_replace("''","'",$z[1]):$J["default"]),"default_constraint"=>$J["default_constraint"],"null"=>$J["is_nullable"],"auto_increment"=>$J["is_identity"],"collation"=>$J["collation_name"],"privileges"=>["insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1],"primary"=>$J["is_primary_key"],"comment"=>$Qb[$J["name"]],];}foreach(get_rows("SELECT * FROM sys.computed_columns WHERE object_id = ".q($_l))as$J){$I[$J["name"]]["generated"]=($J["is_persisted"]?"PERSISTED":"VIRTUAL");$I[$J["name"]]["default"]=$J["definition"];}return$I;}function
indexes($P,$e=null){$I=[];foreach(get_rows("SELECT i.name, key_ordinal, is_unique, is_primary_key, c.name AS column_name, is_descending_key
FROM sys.indexes i
INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id
INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
WHERE OBJECT_NAME(i.object_id) = ".q($P),$e)as$J){$A=$J["name"];$I[$A]["type"]=($J["is_primary_key"]?"PRIMARY":($J["is_unique"]?"UNIQUE":"INDEX"));$I[$A]["lengths"]=[];$I[$A]["columns"][$J["key_ordinal"]]=$J["column_name"];$I[$A]["descs"][$J["key_ordinal"]]=($J["is_descending_key"]?'1':null);}return$I;}function
view($A){return["select"=>preg_replace('~^(?:[^[]|\[[^]]*])*\s+AS\s+~isU','',Connection::get()->getValue("SELECT VIEW_DEFINITION FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_SCHEMA = SCHEMA_NAME() AND TABLE_NAME = ".q($A)))];}function
collations(){$I=[];foreach(get_vals("SELECT name FROM fn_helpcollations()")as$Eb)$I[preg_replace('~_.*~','',$Eb)][]=$Eb;return$I;}function
information_schema($h,$jk=""){return
in_array($jk!=""?$jk:get_schema(),["INFORMATION_SCHEMA","sys"]);}function
error(){return
nl2br(h(preg_replace('~^(\[[^]]*])+~m','',Connection::get()->getError())));}function
create_database($h,$Eb){return(bool)queries("CREATE DATABASE ".idf_escape($h).(preg_match('~^[a-z0-9_]+$~i',$Eb)?" COLLATE $Eb":""));}function
drop_databases(array$g){return(bool)queries("DROP DATABASE ".implode(", ",array_map('AdminNeo\idf_escape',$g)));}function
rename_database($A,$Eb){if(preg_match('~^[a-z0-9_]+$~i',$Eb))queries("ALTER DATABASE ".idf_escape(DB)." COLLATE $Eb");queries("ALTER DATABASE ".idf_escape(DB)." MODIFY NAME = ".idf_escape($A));return
true;}function
auto_increment(){return" IDENTITY".($_POST["Auto_increment"]!=""?"(".number($_POST["Auto_increment"]).",1)":"")." PRIMARY KEY";}function
alter_table($P,$A,array$l,array$le,$Ob,$ud,$Eb,$Qa,$Li){$Ia=[];$Qb=[];$ti=fields($P);foreach($l
as$k){$b=idf_escape($k[0]);$W=$k[1];if(!$W)$Ia["DROP"][]=" COLUMN $b";else{$W[1]=preg_replace("~( COLLATE )'(\\w+)'~",'\1\2',$W[1]);$Qb[$k[0]]=$W[5];unset($W[5]);if(preg_match('~ AS ~',$W[3]))unset($W[1],$W[2]);if($k[0]=="")$Ia["ADD"][]="\n  ".implode("",$W).($P==""?substr($le[$W[0]],16+strlen($W[0])):"");else{$i=$W[3];unset($W[3]);unset($W[6]);if($b!=$W[0])queries("EXEC sp_rename ".q(table($P).".$b").", ".q(idf_unescape($W[0])).", 'COLUMN'");$Ia["ALTER COLUMN ".implode("",$W)][]="";$si=$ti[$k[0]];if(default_value($si)!=$i){if($si["default"]!==null)$Ia["DROP"][]=" ".idf_escape($si["default_constraint"]);if($i)$Ia["ADD"][]="\n $i FOR $b";}}}}if($P=="")return(bool)queries("CREATE TABLE ".table($A)." (".implode(",",(array)$Ia["ADD"])."\n)");if($P!=$A)queries("EXEC sp_rename ".q(table($P)).", ".q($A));if($le)$Ia[""]=$le;foreach($Ia
as$u=>$W){if(!queries("ALTER TABLE ".table($A)." $u".implode(",",$W)))return
false;}foreach($Qb
as$u=>$W){$Ob=substr($W,9);queries("EXEC sp_dropextendedproperty @name = N'MS_Description', @level0type = N'Schema', @level0name = ".q(get_schema()).", @level1type = N'Table', @level1name = ".q($A).", @level2type = N'Column', @level2name = ".q($u));queries("EXEC sp_addextendedproperty @name = N'MS_Description', @value = ".$Ob.", @level0type = N'Schema', @level0name = ".q(get_schema()).", @level1type = N'Table', @level1name = ".q($A).", @level2type = N'Column', @level2name = ".q($u));}return
true;}function
alter_indexes($P,array$Ia){$s=[];$bd=[];foreach($Ia
as$W){if($W[2]=="DROP"){if($W[0]=="PRIMARY")$bd[]=idf_escape($W[1]);else$s[]=idf_escape($W[1])." ON ".table($P);}elseif(!queries(($W[0]!="PRIMARY"?"CREATE $W[0] ".($W[0]!="INDEX"?"INDEX ":"").idf_escape($W[1]!=""?$W[1]:uniqid($P."_"))." ON ".table($P):"ALTER TABLE ".table($P)." ADD PRIMARY KEY")." (".implode(", ",$W[2]).")"))return
false;}return(!$s||queries("DROP INDEX ".implode(", ",$s)))&&(!$bd||queries("ALTER TABLE ".table($P)." DROP ".implode(", ",$bd)));}function
found_rows(array$Q,array$Z){return
null;}function
foreign_keys($P){$I=[];$Wh=Driver::get()->getOnActions();foreach(get_rows("EXEC sp_fkeys @fktable_name = ".q($P).", @fktable_owner = ".q(get_schema()))as$J){$o=&$I[$J["FK_NAME"]];$o["db"]=$J["PKTABLE_QUALIFIER"];$o["ns"]=$J["PKTABLE_OWNER"];$o["table"]=$J["PKTABLE_NAME"];$o["on_update"]=$Wh[$J["UPDATE_RULE"]];$o["on_delete"]=$Wh[$J["DELETE_RULE"]];$o["source"][]=$J["FKCOLUMN_NAME"];$o["target"][]=$J["PKCOLUMN_NAME"];}return$I;}function
backward_keys($P){$G="SELECT fk.name AS constraint_name,
OBJECT_SCHEMA_NAME(fkc.parent_object_id) AS table_schema,
OBJECT_NAME(fkc.parent_object_id) AS table_name,
COL_NAME(fkc.parent_object_id, fkc.parent_column_id) AS column_name,
COL_NAME(fkc.referenced_object_id, fkc.referenced_column_id) AS referenced_column_name
FROM sys.foreign_key_columns fkc
JOIN sys.foreign_keys fk ON fkc.constraint_object_id = fk.object_id
WHERE OBJECT_SCHEMA_NAME(fkc.referenced_object_id) = ".q($_GET["ns"])."
AND OBJECT_NAME(fkc.referenced_object_id) = ".q($P)."
ORDER BY table_schema, table_name";return
get_rows($G,null,"");}function
truncate_tables(array$R){return
apply_queries("TRUNCATE TABLE",$R);}function
drop_views(array$Xm){return(bool)queries("DROP VIEW ".implode(", ",array_map('AdminNeo\table',$Xm)));}function
drop_tables(array$R){return(bool)queries("DROP TABLE ".implode(", ",array_map('AdminNeo\table',$R)));}function
move_tables(array$R,array$Xm,$Nl){return
apply_queries("ALTER SCHEMA ".idf_escape($Nl)." TRANSFER",array_merge($R,$Xm));}function
trigger($A,$P){if($A=="")return[];$K=get_rows("SELECT s.name [Trigger],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT'
	WHEN OBJECTPROPERTY(s.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE'
	WHEN OBJECTPROPERTY(s.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing],
c.text
FROM sysobjects s
JOIN syscomments c ON s.id = c.id
WHERE s.xtype = 'TR' AND s.name = ".q($A));$mm=reset($K);if($mm)$mm["Statement"]=preg_replace('~^.+\s+AS\s+~isU','',$mm["text"]);return$mm;}function
triggers($P){$I=[];foreach(get_rows("SELECT sys1.name,
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT'
	WHEN OBJECTPROPERTY(sys1.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE'
	WHEN OBJECTPROPERTY(sys1.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing]
FROM sysobjects sys1
JOIN sysobjects sys2 ON sys1.parent_obj = sys2.id
WHERE sys1.xtype = 'TR' AND sys2.name = ".q($P))as$J)$I[$J["name"]]=[$J["Timing"],$J["Event"]];return$I;}function
trigger_options(){return["Timing"=>["AFTER","INSTEAD OF"],"Event"=>["INSERT","UPDATE","DELETE"],"Type"=>["AS"],];}function
schemas(){return
get_vals("SELECT name FROM sys.schemas");}function
get_schema(){if($_GET["ns"]!="")return$_GET["ns"];return
Connection::get()->getValue("SELECT SCHEMA_NAME()");}function
set_schema($jk,$e=null){$_GET["ns"]=$jk;return
true;}function
create_sql($P,$Qa,$jl){if(is_view(table_status1($P))){$Vm=view($P);return"CREATE VIEW ".table($P)." AS $Vm[select]";}$l=[];$lj=false;foreach(fields($P)as$A=>$k){$W=process_field($k,$k);if($W[6])$lj=true;$l[]=implode("",$W);}foreach(indexes($P)as$A=>$s){if(!$lj||$s["type"]!="PRIMARY"){$c=[];foreach($s["columns"]as$u=>$W)$c[]=idf_escape($W).($s["descs"][$u]?" DESC":"");$A=idf_escape($A);$l[]=($s["type"]=="INDEX"?"INDEX $A":"CONSTRAINT $A ".($s["type"]=="UNIQUE"?"UNIQUE":"PRIMARY KEY"))." (".implode(", ",$c).")";}}foreach(Driver::get()->checkConstraints($P)as$A=>$tb)$l[]="CONSTRAINT ".idf_escape($A)." CHECK ($tb)";return"CREATE TABLE ".table($P)." (\n\t".implode(",\n\t",$l)."\n)";}function
foreign_keys_sql($P){$l=[];foreach(foreign_keys($P)as$le)$l[]=ltrim(format_foreign_key($le));return($l?"ALTER TABLE ".table($P)." ADD\n\t".implode(",\n\t",$l).";\n\n":"");}function
truncate_sql($P){return"TRUNCATE TABLE ".table($P);}function
create_database_sql($vc,$jl=""){return"";}function
use_sql($vc,$jl=""){return"USE ".idf_escape($vc).";\n";}function
trigger_sql($P){$Xk="";foreach(triggers($P)as$A=>$mm)$Xk
.=create_trigger(" ON ".table($P),trigger($A,$P)).";";return$Xk;}function
convert_field(array$k){return
null;}function
unconvert_field(array$k,$I){return$I;}function
support($Sd){return
preg_match('~^(check|comment|columns|database|drop_col|dump|fast_status|indexes|descidx|scheme|sql|table|trigger|view|view_trigger)$~',$Sd);}}$aj="adminneo-plugins";if(is_dir($aj)){foreach(glob("$aj/*.php")as$n)include_once$n;}function
get_translations($gg){switch($gg){case'_template':$d='+JLxZ/vZ;$rcV8d-,1I;^@;V&oCu=YF1:P"boIY4MEw0m!Ut~!Cei/<3sFF/?6Z1eL4T^cZd#rey#iVHCw1wpa|,^t7sRW>oHSyCN((YnLVGtt1MRXko&h#8#Y$xZwpuw61s.Kl:Jw_z)7x^A[axs=La=M*H5uZ27rQ7Fu8yTc
s(MR[]Jig9Kdms?c*4O~YTqzG1<A-a:z#Y=g/P+="<$h^l!BQ:"-PzN6LjOE-PT27:dZ##K_r2x-l!2{S^N&>Z(p"2oi`
0,N3N02rWAQ!$ydA)t*=ye,cH2RQqnwXCXN!*:($"U"#2R3U#,"(HXDlIR=kP-g-+mdA&/Sm#vR=GZOjNpo,BJYU3["JCwthpdWbF!)&%Sg%KGf.ZWggN[*
SP?]
}A-((8!DL39*~"X8!.T5W#.#SU,;ItsamElmUrE(<7!#?H[[[<K.1IDgLJhQFH#R-<}1#cPSnXW<7D;SNl`qI?S/KG&xa4U2/pcJaGRO5_zn%lXUV2wAhJ^@VP9(<G
$j*$CE]!#r`fp!w@xpUt*^nWRyJ2r/>0qC]$5NX!_z,fs5r!Qv7f"lRWvbP5^u1jsC:x"S%7+%c(BSSMePD.NW:BZ.JKeK]M1b;z+NG&wH,|Rn%jAu@PeP>z_XCL1}0z`uyEU|uYH"#cDe006"z%tiEc1N#xo..TRxf1
ue3hBa6*X<uw5ohn3g&[r,,g(I"RY[BT;Lr6pZ_j$
)DT`rB+1#?Sa$n/#7?KgnTeZT%x,5]E0%bnp">_1|BJ]Fkf)%uQ;JDZ58VT2$;0N`t6rNrab<.xcdJ@hc`}T~;z
nCd2+E"5q
=w4vz,F2MU5iD/cY#cS0aqQ>$,4?:_X9xGD#p&Y$A4./~ScID5t3:QP1y_<gx=22K/B)H(yIMHaH<R/UVv
x[Z/62:#b2),"sG&i,:bG`^7<mn3;CZ]T$Zn-U_]djT|q1u`*6I)fbTq:pqyrZj;bK5gpsT,6<2L(Z<`@e*.l$qUooRNK/]KO{.KqXi"0Er
3>D`]G_MR{^<@j9x$~-I:=gV/#W=<~/P*,Y~-|.nrXB+2&w+IlK_Zp`uA0;<^<?R?e0eS>1|uHTAUdaY>{5m]L>B+[(`A;YU<;5p]KjFFL1kf_xu%[xvFvh$s6P_9s4WKSyeT1t>.u^@,(2^(zQyhne]brei:TiK`f[z$(CrE^ay!8Tj)/;BOf4P]4Oj_D"N9s@AN=v(kbDMurWvh$2{DA_zJB
d@&&%$1)
Q3%]sIP?X!XCj899Z#g2%E5w)c238"L1tX';break;case'ar':$d='%c0;:bpD9,|?Yd8.2882+Vy2:/0NR@ke+lN9ZX5M%#MpS2W^Z>&da$(KHjG*@7I10_+i1q
IM]&9Q62cQA}F;<*
kw0&1*P")]Z+4@*rqA<k
`vjismgzh^/g`~[pqK,(5h=M[xx+izA:j(G/M=hdnG_d_vbx2/^l3siPvC*xD0T67.:C.?"9v%J)v@L8H8/ZJw,.Q^G-Ix?JhW=!+c%4(Cj4QLY_!~V]4OFqR^*5E;&kC9b+F15kvn]J6b:nNbRi(Dn%j~vyoX
km&-4kIt[5}IZs
P*hBS-tO^EN%L;`;mrdi34[&^T-Q@gl[RC2^"[Z_64>a,tH3abHGuoy|L]c
7`nwp<rzm~l&vZyCwg<vB=y-tU$KCaL^_X7vPZkwm2,GV(nbmNtCmxABp3uzATnRyif.#eAs$R.tE{VU9`TFkJ5APuNZe^3SZVkgZshVl2qoZA
V+pA)IH"H%6P5s|*Xgx9em5pjy[fOSy)p/?_Wj7k[E|9)FEL)<L-CSp>-mrP#=br,ycwjQs2{^u^InJnm+GM&b[Y,z#C(<,-2VbJJ!`.*V{]^qM,ZCm-f;](iPTi8UBT6(;#Up|`p9z2RF&TZwqP{40a;%qikPQQQcGSi;7Pr7+i:.N0np?VC#ef;CKIh[HU9]NLNuECYS>^X:4s%g46hfW9J0cix8*g@l[E-_BEk]>"k]6uC%f>3p)l;RiM[ev(|KDc6lu_x;Y=jXYt*KW8r9uoI0|urMAy9Uxv@Ea<"<1/@O7ox%ZZ2EJDxcG7SdXf3rhcA5LPVB2wbxH;=_XJlJ~*5?&_Z>C=hhXs-kJ^SdIB{Y.ffC~MG1|+A5s^bX@$Ws#D~td#7p`e4OB5h:.P^({Tl+>p4NxsAjluPDr*7G9R"vkNr0T*JL(aQg#F3f[IO8:Y,@ZF@0<RB^8N0&,l0.gUKu5LX_r"U)ZYw9?y2,EHLim1&.AIvw!(2#UI^g,jbi*bP3&(U$e-T;+iZIcaqT:";e`MOhE0T#}F^q:EkD+PI)%d^W4^n>7As>k;ED`ii?E9$=*x.1Dp"jZ<l<6T6R4xHN4,#2KSq>n(X;I9z17Q6g*AksbM#q8C{C]r$vqsQY`:hsP;>Vt3eqn&T6:o|pFdnq80^6V<!*JM}fR`!Y%N~evX=CrG(@;*Nv"6w
$yW`fLx^7c30;_dXM3G?.ij&nd_6xg%v/]8uP29OM#p&}M6?.vr2v0):](4.uwvCmx{^At)uiXV;odC,F]$FLCtg2[:b!-{Wdt->Nd17uO-Rw
3n2GW]2]QwK<FiS&:3<UF8&?7jmE)DV
+GE=ud6Sub`SmJ}QX?5X&DP"09MqEdUUr
<JDv3X]M7
vT`_]`%2`q^*(cr-|CecKINHtY`(IfuS|h)TvsX:hCM]RixB3sp41OCok*mYh_!M6R:Q6U+ta0(7ww1xyQoIcy0p-f*Q"c{P&i}?A2em{R4)TZ%.pwQqWkh1~+I8{sGUL-iGD4Yt->j"BRDqa,kj)2ljt"4<>M,&?vb`W2}qnv0M&iDiJf]h}YBSVLn&{S"dRPdj4&tj$mt-s=7O8WA)V?Ko,e4lPvr
`[dC~41L@h%qF3X.f+fLQ2shWsttxXBddDm0xF;g<@eW*dQ2yE6Q;KN1X)@AWJ`J&Nvh{"AWc_d8QE:-6r`JAkuO2)|=0DiE@/0R=L].%evH53GjH*hpL]a[9l]JsE6Z5-y-`hu
,9.*y+mjKBXa>eK<V7k.!<&-#9m1/lEQ>2F6SVI6T0Um<yQ%u=!+?C?HUkSW8*W-v453p
([OB4*A1-H?ywDM>zx&2O$8*24A3jZg.[PGaB=XeSNgel$o,&a+9.!i8UFN>"fQZkt}p]V,mduD8Z`*7{dHpLQ&"9&JVsWL/EHxU5=9`7]:NZ@c%*CwozuDTsjIcxh:dc3+:"4N^.?=ok>E$w6YGYG3U?
7u#2nQlAHQuE/4~T`caqWX4;p``/HL`q{^sTZ=>#StRqqj
A1+C,-vOZ<:f`Ae!aQc1_<`Dp7)l+DlbbuQ9]<Z"A"
k%]&GGI>=d)XM*03>G|Z0`A@poj``J0KZt}FVbx=iebYh;4mOo-h*5Ms,w^T5T[h~@JBc&u,2:J#5>Sr6HK?E#)uNlX#$XASAN],5vsc`v?6.W9E{9@vuBOu:JKJ`5bmr,Otfz!
4;ox:SGFRrJpt9I5N.0q/[Wm<jNO.+eI4PUKmrwp7U`4>"MA)$Z
eeK6A
1yx)=6a"kSu488srD)9V>am0
ZJfWtRf0%*-s..CclU"c;<hL-hYnS4p!dylyDR9E(*]xV5:|WX$JADS-HvE,87OyUdm(-opP_!fm*K0wFq/$hP<p5tV$&zFr:FcaFEP3S-mNBp;4s~BLc>#tEU/LVL&[#ZX}N;_>`Mcl>a"xb,xs6N/><4fEDD/<Y{&b@2]u-RTxK$Nr/<(epmDjY5Vj%@aESKkA5(I)%RX;l:?dLH^4d^qH,jr{Hca=GEe8Nc%;-p5B(B/[x-i#K!]KQXgj2bQFI2iWwpYm)+I&mNxjST(>lobYI^CQ.kQ7Bw`q({Biu.KjHK]j)V8:2Pu(;WQm/sH-]^1K=
`$XMZ/Lz7oo(xr+moLF)]o$KT1v6g]_!PEx{=rH|cmS;F/!Eqov"R<h$/$aCpvmS=q"j5oVr%t<EjXC?_=s^m#DmsljfIF*7UVg.jorOa.*?<I]cp-Ys
#S$E>lK6qT9HE]/M0r$4Xl[X)UuKd7(a&XkC^W"ALU#7XR~^!2^S>Ha;^cZJ+>~H5@`57R$tE
G,IFz%0EDm$m52}/V_Q9@S#M"(:C%$PAS,hL4N3)Wl[3f)G#!maF};Io5_0wqs`4
]]t3/1`L*GEL-T]E20sL4?0`E?OsUux@pSoGY~n3ugY`[9c8"TX36Y#F?P5@skDfI.N!8Q!meewVWW*
_}t*kZmD4)P+Ct8|4wshwSp}AA3f@{(bal!)#o%iHUyER|Y+H^D?p:a!_MeU&Clh
_.|d951mptf#5XREnMVT}
BdcZ_&3(Lr:5TU2`eV~jG6p>
-,?wAR$C:.Wu$i!zT%ivH%
&k#!B@c#l=_jHkA&*8vR!&t4XUkc=(`Ftq(4E7=D~>TpZUOwT)nTQ(`Vd1xF|WGlA&WB-4lkRI+cOaHvaibS[3JJJ]|;gr8^8UN;WqYq!"Hf$<j<KN(L1)o)Pvo&$=}"QD5PBe*edv-qMc(xpeuJx%NaeN17]gYblJa<]vo]W8Qn}VlB<"33au3NPh.^qDP)t@7aJ/on1d~F@Yo.QC/n5v*Fd`oM$HrZ?S*Y%p]L|L5B~
)aOI"Jq_2whf8c!kUNC&VOysKZRt|a
)E=gQ];P#Ee
sxmk4/6?WoaijOg7M8$xF"#9s1n0%X/]YbTA&0
T5.q1"],g.783jcB<y-SNWn:V4p_>hD)DK}?+4}Mxmh:h/Vn2/-))EK-T<NaXe-U4K(>:g&oD)8u)EFnpAsPiVaDE1^%rGp/R3>(ps,rN1<D9#Zou2fJ8jqKuQDL5Nn(7i}yY4/8?.{<N&+fUmBap@|TdoTj*SyR15#8$/88BwV[$J%[N/`%nD0ciDfrh:6s5o-!Q0PNTl&F}UIDS@CM7.sm%2?c,338oe_W92q!#?C:M5G0NSj@*Vb1cl$?O^1/G3"by<};ZXrY?OU40MPtn7lJ[5f(X4"5n&zi70t>qD:.EN}MSv{4fmUf@r)K&2{EW@DwW=g1,Vh=+LPr8UE=2h(lfy(iCiMT[A,?zBc3gmL`n:TG<FZE8qec!Y-4.]kQeAJ3?*rl,]qe5j,KwC>fhZx
gGWGy.KE!%S7o4%I{1%5oIrp$(<3>
:AO#2W5E;7jR|<$V9Ow)QUq)T>C!#?}Lx?rGdAm6)^/s^O3C7-r[gQ6@q]ln-FGq3hyB$PToMR"=oWg^wN2CIQ@&$eY-^M}=UE8J7o=eUjbY[WbtvD&ehBnrLNX]m4!FM>0T^1f8rL][1bB#`c"79%8F]+)eeD.5^<{aNCkK/!jy@_O<74ghScH(8l5GdKW!0+/y)KL.cW3h5l90hKTnW6KP2D4)Jwu<7^-H9I?QEe|dY1FM]s3j25_$s^kL~^4dR5|5_g=>L
*t_IR.3@Yfxcg=c?h;2D]@V
e/fu+s+sQ7VF(#>BV@gEcW3l+sOW3)(jG#qbgV-!?k7FWy_[J^}V>_Vaw2R[gF`-9SbXC9wmC&9JGh;(x,$)-#C*_&a27/CH~o?^iHgbF-TtNBKs!:q:`9@%Bj:wkb8SiY/Y#>0-$TOgRvH!Q)7-WBJD%UoOqbZ%<<,b@cy*HM&w$fisXnIHuEaDO_ew@E~jxt4ZdM$FxZvnnU>?ymfI6fX:%6{[g6wl_y
lQ=>^Bv=Hq3-Mh/#9rDZ-u,Sb}t1Mndts>gCo#_#aHm&U.^q;()a+%htjex]rD3)/rk@+>noWNJEmG`M6xJh-NT+a@)aq}X
Sncx/r]!2Blp&g+fk2%E[k5#bu
05GHXagm#m+sPJcP8F{:2"x[t&Kp#-0&`u?c4<S&RU[t.r
jpjK<g:KaR,t4jsi-waDiFfAMmL;yuhe=_R{P+pO:&UD<=gkQ)RY[*=w
x?G#w3[w&gM2J)&0{DsO%.zg6`PIgH*WFj3Vk3dL-bOTbUD=1;$5/
kQHb/xC:1I,ch_.L]FKmbyfnsctp5OuPD;_#~?,q;u?[&3y@6[^`#9e[D>oOa"wTY3t&K>M4q@y5WAUpi=?(]_,2m7cL8P?kJ7!4*x$+OmzlNiqw!Vt3dZ2TX&zJMKE.jg&6O]UpH;svS`rz)h,';break;case'bg':$d='-evG/6krcC#?Apu(NJC/Yi23neoY=ezQ_?CU06t$]!wU0>8N
?<esTOgL$q&yO2"2Os#gUr^DuoK9G(G`2qdSv0B`K,X2C$4!G[)Cx>3Cy?w<^%8#w|c1y~b)Fkmz4lL6ZD]tkG!:KcpS.s7uMuL7t-^iTW=!BLR@?ysJ9n*(y`n7*l2KE"/*=&kQ7uad6|^SIC1J=56Z``rc#]W{w[s~IF<M*]Y48K6C!w6TUkad
LesgJZ+F:dc5+6qTD(~?(emWc;jW7*C6-g`)grr#h)*K$ee#Tw$4cx$k.0?BJL~XoHSWhW85(kt`Z4GOGC9XIx@ozwd-TE49@ULlwy:N?bYZ&`Y(+H/t?v1c8[LbP6ud!kTZwr=K:x^v
:AG)usvU0ga.w>nxvtc|v{masscQv1v<ADuJM<y<ISLSgtcMNqqTS"=nsF7v5mda`jF-V]uyGXR9iLF]KZ+wNYN7Ze9mVUurdJ;}4>AW,zvt[TGIOf!)JcakT5T{ui&9uSiS]ND&:6U_N}FH#P)/jN^k.N6/$ic4r"
pt^ZY]CKL-KDI[KciEo^z9+;9ENe6Z*j)&<(!PJH@b:$k4<OCoWlk?)3s$Af,IRh7VZ)&fQ5Z9fWXOd)bLwG8`gO"N~R.%4e*j_*moogdonAgVTv(&bL^V/)kVm?z^cs^w}fbG^Rv8|(b!~Gl2A!-g5hzNpE@*[2>8J4jWy`AmHUUxdISCZ0]4{!U70_B?I6}M3EZeg>-pQj[7E#h,r(zH((fg[bm#-eURWs,*Zea([^Zgl=D)}o$j%-whX$lPFIZZRTR@n)NLE%IAf]@>X_NGu%,>gk=r*#.RZPoXk^^I):?:<,<C05?e-+0gh:~&JC
HQb`q+vxj/BYnmn]s
JS[-Aza>kzY$s%P@CmGuek#/20^dKP/xSaAn]<v8[Vz#SNZD7o9zi-WNZ1cT;.DY"^?vw2UQ4^A/ls6MNyW2BE,)C*F&jN,!lqFA-]Nu!W5Dl&ljCN@6$7yot>W.:9;)R+jn70qWk[*)+;<w3Q0NV6XbG`yz/n
g@CF7w^IBj)7/@g2*9WP@R9p,fMB(7Y+=MT;OW?uYEdh]S]_XNW
=.Z!"sjEB;imzl|pNs%bNO2V/.,Tp<PI@Ll+hf26z@.,rJ{+Pbc-+`"f1m-s6r#?x3oiC^xywo@1^[NTU+.ZxRL;p<QiMA(8&R:.vW^hO(CluRR?Zx|H7A{on#%-"GhPFO9*2e~i8b,4plFkVZONnTL]nW#nFlx=wa?pFk
.81-DANBd.:80d0)QC7G!}M/[,ti5_C:O6J8uNEw;}JFS`diBi;H8lWgQg=EL=3QJh6f7gDU6nYp>~c
-H0Bq//C<l,@miZU3[T[Q!SneX8!*f+=_bjg7P+M>&@wW|^q/9YN3K-oDN9LD
F4[bk.6j-,I>NGKx`$1,Gx.^CZO5:iGY*miXU(DAW|.1U[Omyls1m<&
#)*8dq+wR/&@TW!?$Bh~D=48p*qsNvOOPu:osQ_<;>Yr[w;3&zHKlV#^*bVk-ARq7"0AP"H|u,kIJ?25f}5w@.Da,_9QiCe8QIv2hty2-vRzcXSa[h`7e/b{[q+.1@F@m5gUPYE)4<BJ7_`/v&key=5Z?*-Mi=Kq`SfuQ%eMq]7bLA10.wR&NF1.<SD6Sk/x+78>O<kU9~?]c*SDcARTjo%UC,*a>Trw/3A<p$Hbtu9B^xA(K1aQC%!&yk67!D<D=JIkk$:?T[i0RLo)<,eKCOscOK!Spz,L="R5wKNyPzEpN?U:_[FEh8]yMZw!(Q%IR{=[^/5W-}3<]@j#8dH^bW1YIlLoLq$3T42TS|WI4awR:vv8:br~PbS([&M*
y,OyeT_W@=xQ{F[$mR":DRb,Jh?33k@1CqXZ{
CcV$JZSeGgLvSTE9
Sad8x#p6;Pl[yK+.sf+JAyC%vI6l5wfUTr=2:Eg*D>p0X7o&hK$`4zUtj@.
=
uMEwu&q82fioN>Cmu~;Y-
Za^VoRKb611=12G~S0d7#b;!hz5B0C[!Y`LQ_]nB^vQHZOO``p=L4r9M1r)P1j[u>d%rsy<!/0m$6@W&:-AE]]HZH3`l=/In:_6J2.W77PcuANW6u#a}lq5)>-Vd`Q=Y]=lA[-*/p6R%%^@F=v"H#=?f*m./UGbThN7%"jF
"qb~Ov1b/<Ms-gNjA]G;WY:Uc^DRPx=n12AH)?t"]1fHILy<rQ=}blT{u,6N1m=K]LW~%cMwAy){#O9q?v_)G4Yp0O/bXqY@uon:p>Vx@"$D2X]sA_bcCbf2x~BDjCAbvGq(k
R$r&D-^zRn9!cp_0P2EX"E%8.7;}(}k!",`*dVq"Q=g!+j4EU$hrw}qJwwlP(GQ/rc*((rU;$Xb}F@We@M8B_,4|YCgq-4YD<(eg
_"C6XltnnHby_BS80X74J=FU[qwrrCe-/sA,*P3$b!|2R^_sl91+e0%!!CL+7pRIl#3GUjrTF@^vyLCpk>NU}::2.r*N~AfHX+K:eR565

_])iE[mG;Df7ejIB@*#3/gQNqA`-BJ6pg12
S
B0x
8Tnt5{d3&@NN(sl~Y
.Johxcm6A&w
;%!-_*.;ZY!^N2^6)wT/Q..F:!.ISWtlD<JF2KqQLKyq?M+mswl.E]tE7>do.T$;a9%p8XKDi42j1a8/9*SeQA4+XvD#ZAk$.)Xf]PH8X<Tj@_[],<Ejh|Tu<CQ(m~d"<^LI<X
v&7tiLiqIOG=48SFZ4?$#-zEr&3[P,D9CAmCUN&Fad<+4kk+r@QI/-erT,-.ZT./1$qRU2C880l1JjmN!QI1^csUeLSB^TfaMn}$:yXvRc~?k:[-~eeaY!b=w5;f=#.a@I}EfW5gi7%
<v9Ez&Q;"v4>HoJ;/e96kP[!"OwVp0&X&J9re<HD<NW8Z?I.Q>6g:2JL0R4d-Q"Qn]f:ws>lQY8jjwCQlkS%[q;<^b4V4"##2+EbJ0zY_n.jJLrpu%mo[X$N.X9X#u2rslXEjIiRv+8gra#.0M[&W][N^FUC5GCtR:"Eo7.fTCK([*Lkk5H^/_K?X0E$8%%,Cw!CBkH7I0}rDdC<Z$iq?2c!}SBDI#|RLE]1ooR2_bj"N
.Iig"l(.<v!CXi]v3LhR2OsN?9]W_9!vCB%.3^Q02Om$lN^1M.Ap5@iuUbWGv<q2lUW)&nCAsG*t>cXT>At/!o[&]2WA{h8Yb#]f:m[60oH_~FgPVEoOu>mI$N4)7=Rfa5N$fC?=%+f3oTS"c!%dRftVIn=FML%8h_R.Ihz1MWT;,B9BM0^XqRo"D**1yue3EMe`lub#4=wI&rsE.^{DJ<gVN&_^nD}xnAedvgv]4/}Nd1gQbsc(XHj-zsoFA)Io#=667e:Iv9JB_wDJ6X>%O7J;iot%[biDej>+o<.=+d],NNvU9CV-V>S8Wiua`Nb&cp)@-rYDCZ$hZ;EOIsX.qP7N%>z/U$9c&s,1RbL:gnzcXA_L4loITQM&KM:=TORdfgI/}=bNpL[kIy0_$x6p:[=M8*u60;M!LS_I/Nz>Yx;8n9jde)X&V#kAm.s7a=An
Y9c</l#C3y_!8{H
3~apJE1jXr-on:G)V?A@U9A.ryAi*I]2#3<^C/#w(+NzDd5P]{`beEUlU0<.fX:zsb-Tlv]+.ghLuQI<l==b65!t=5:W;1rPQz6Mo0<|EU^:1A;mNa(TFiiIc+4B$
MmSexiU?9:raON)}#.(r/DTo"pSl#D&rfe,QVjmLY2B)R97z^urh)|%*806x_qv.2qmGxS={HW&ZF(mA=](ZfXDlBdT#mu=7B+M6v`skXT;es%Cq*UAQkFOzi-@~KY_dl*``G_].dg>mCV-$5sD`q+4u4h=]/7M(xfq1]pMAhl2J,ZX`N#6SLY;X@k]a.vC0:ue]_{LPt#FVKFV!,vpoX=On,BTmNOy|1;MgiH,9fT)9O*L-.IOwFW*PG^t9AD,k*uALhp!^K-X-3f0$$CQ%Qq%c4&>pyDM]BE<)kT`*iRI`@f
+W

Dr,aMM`_9&rvWVPDT`i^}Y/)HFo68Q4pw>
]/Xqv"a(*B/,s
ZU_F^lKtnAZM-Ch"`R/zf39>`4U%%J+mn^5L6xtXNL0^"8/4U&"g&~R)m:FDYOtl(mrqJq2;CRj<0P/800-%6gwcx6UTHO@F0owtS!<]Coru"@;Znk:i)&H>6p<jc?p9DG/ZUBvC56Dek|1"E>Qg*coOnlDc
_XJ0lB$ZFm/.`vDjCGXIQDGR#]KcPE>;hyLT&lv)PW[@^qIybK[Rtk:nn+srpW
v-=q!Yh`r4Q6VW$;S"4E)@V=G&u#GD!z)#UF%]^=XR"-,wPF0Z7Us"l&im_8BNBW1[42M]4&+[v@u90u7lc$#%FwR0^.,/B?RW^K_<c|-h6[<Nn/Q~f4NkMxFMZ%%atU68G]<*tw`~
][,avGjBY>N+jo1!l$}Q%=f5
Fve%"v0rKANv+GfwS@4qhl381Iybtsb}tu5vA9Js4cbL+;_Bc3GG3)+C?ZAwUWTh^"m6RzCJ1jL-4N$++H@v4:B`+6[{Sk^kPG.&`tfX?O=s*gyq"%tK$@HpslEFm]7I_$WIaBJ8j[8PbuN
HE,3y:]Q6oYP8Y8r+TSC2ViaVFTw^_vvw%BtKc3E7SKdU8*{[)Ei"{YEX]$Wc[O1I4/)VQh|#79|t&(m=M:c>IiG1"8anI:T+JL8!>1BN)S2i4mXz#ZD[{dFa{)k[+mK2#mtc64>Ys^CAOtKYswH3wtj%Jb?cvP^/(M?aTX}k,r~-ru4(Dh|E[&
>]2-Q@ag&Pn`.2WFw^45a|OnMh[Q%@c[rFppO.Hn`h]`V!ZL8?;_?,i6K;!w[Mi8l1*w>EQ:m)M{W)84dg`&1J>u%vy5[}U}n#y9$wd3&Z^lj5MTjJ-AG8&2cLqne_2q$5dnt(.d+7]e:5TsbA?#mK5DWyl37+@gC~`HA?nC(Ga%AEj3(
v%&mX{<fxUaL^;++qK`.MB=vP?sy<=Jgcldk%L<(nc,;NX7fEeZ1RF2i2^)Vb<y]13WDnjmO&Eq~
j/co-jDHop+oTbJf/"7x44Z:P8w]%T~x
Ih:=.{Xz6/D},YGKDB2MLS[Sv]h,`0Mb
DHD6kge"I42
Woq*MCuXP+4@h7VneqL5%"2H:s
b5]iUjV|ws<54~,X-q`[WqOou0C*u"0Mpb-YJ?vYXC@82L1.D6>--O6|xZEE_7J$&xHQpEePv=Ad/xl)Fr.zogjn(b5vyG""';break;case'bn':$d='.hWVkaMDG)E&.NLeS,^LKDToD*
2^BcN85e;G@CNH<>"}3MN;qq"2NZZU8d=aNiBH)3/=Rk6B6:]4#=Y0#`R/E].y/$BaT^Mo!#Ew6)+|m4B-](?G@9s1b$&(q*I%#[E6:hGyg(flL&iSxzGmmx(^LGq++U5n_QiK>oxJtv<xvGyWM,y|>3giM5W;iK)ujj]c
^Gpng>F`?G"25o5.1o:
6AsLa83o`tuN-Sfy7a&U@+TCdLsFR?Lv5^ArGL$i]6rd&y"U7"*d;LL::CP.
"-_1L/m[5s>Z!E+<r*R("3n#?<r-hm@^-.$E.PI"Slw0iQ=NyzmZB^@-M$6VtD${:C]|6^$g7S59)4BCAYsCcLY&mhtUpAobCA7`2-V+l:
}@K,gl1g>ivW|b@k
Jwz&JP^Et=p~uY6]v;AY`by~X}HBmzHCrPH*CC>oPvcZV&1-*)gHWQA>-!-(mUm,]]!EI9A&^tgqkPEv[y&DHoG}N&6Y6Ao`Pc%@U5rgth=k7AXDpCZM#s(!CsPGr}m7AwWMj#$=dy>!Fg-Fq%$OYQhHHpm}tf?|&8Wg"=>n+k#]S?m5^`_bk$pP/I,,QaWK*-7vA%[sU0<St)fvD*7ggLr4:>8y0X6>6F,ULzI;>g"Uj3Y7RQ"uSIx1dA-o;?YMmYNT5u9*7J"7!1LfJ&fjBm,f1k%_1cBPR!9g+X:rx.m9TB3pd,xD,H8<<8*K*iYFX1A.p&PoE.=Z-hdV)l%1[}"]f#t?Y{eDP-jVYIT[B+Z&i7K~+8&.$)#ST30`H-Jq*<(n!C/j_%=U2MquvbZ*h_R)>m/^D@Y}x`uwvZK./_&71Y/BX*s5,]y,7MZ>PL?,D;*"-XOE&?dFmA?qexp6:#J"]u>;@)#N-wnJPj
iSX):EYDf]uEetXJi*]s<X_K(lMY(d*C.H&8m+aQ8lZW$;e4
RXr4mX:2m-EkI3Oyv0KWvGVBIM#3p.L;
xqJ:{1bn0[>/To5n;hVX0^(=^:|W7+qnw^>4+LUXf
xfgE*1]t0Q|JE]|*p,PD$X$mUy
sUE]RO;^(Sw_TG2:Am@88luC
e7xi>hQ?5r0d~32>1b2%J97$hS>^iZ#=E^%R+4=]FdFg4_EQiZ>$r^@PzWdk,80@^Z"CN>CljpCNP^vKqa*&9$sy"]qSg5eTT(^&PUU0R`:
M2io&@M9HX}5m*PRe6aBGQ~<?*IpRn*=M_]p>5Z`CLzHG7aMuwxnN:VLdQ9Q~,+m;MQ[|k4SUm]ol/I2%<
W2Rh$yrLOvY>enE(4qweRbGAn]qo/`2j;[SmnCdK"-[N_=@w
J;f1Ge{o_g!X8iACP6).mY<yHb?h74Es`7[
ji9&$mLHliQ
C<Km{VQ!w^/K9
L9MYBd=j|aS-g=nCb"3!d#^<o]l80@h5~bf<.lNW[Wt;zjKoZ.Ri
@G^Go@h>v=`}UYR,8Y7w&d1;`Wop5
,5SoVt;arLK.X/(=c;oa`w><_{lEjyc::;SO<HXK2R"P?Xwx]M)WAgUNc,R{1F%yh|IR^.:^R;:sP=P%
uXnuv5!==;0=?3FOKi>Knkt2k6I^W?L&.A=W6sF`NiAn83ksjBVe@ifEqKi%Yitv#S_av=Ni8.|15aT:.t_$S>)!zx`4vEcZYNjaXU(F(%}eT93YE`4c<-@I>aa$bfOWNY1oGF@8|TlCj4P;j4?:}8yqW_&vT*Ye775.6s#:6nI^.QBF[a.np^-m;p
mdiQ86r~3askMr1@>P1F#i5BMDXfMr6Now)P%
vS_|2rX9sXIz_T.:)#9;cu<#t`n<%t#23QU2el4dn[M-0:a#o}
1_#Pp/m".At4,
$RA*undb;r/-(fcwJskh!mpJ%0t9K%T*h31hCGn<SiT]"`m6H/j/[oTgU9T)_-R%fAh*2-Ntk.*mFO5Y:DO[@l{U[pyn;TTi}r;1f$cZmDf0m7mmZE5423WRk8Iy=1"c}FnuksQlu:Xi]<4Ilp
LmZ1Prc-KD?1@QJeido,5J0_9~W"vI$gRzx9J/hfYl@&yV:,U&$`[w6{hfHi%lZ!X&-d(XX=CL3gE!-$O.aOlas60-IWG(1#//l7v~"ZJu%:=l`m>NWUDwR/cA+!6(l}-nm_Bj8#2sa"pm+qvb.Tx-_R??t"54>^;Q$=4ricW@UK$}),pB%29g<gemX$R{@eO|d9WlGX**i|k}E{O!Rq@%IWF$WOCk)R:-U[b1H9vXKMqD35F_%VwCfQX9amoal@%4ZJQ/t+K2XB@mA??mNriqm)kaRPB-U%jkl/*Tan91TSS,2FcTyEI.3SmUv~Z{<x>K"$FDU?,z2f4GRR9%Qb7{VT-Db!)o4Piu*lA/x_HT>b4UY<G3]3DKam$p){GD^cT
Q809..80jhX%1qf4#uVLE%tXcu-/`P;Ck`6S[=6YqvD&NC<x;(4FANdQ98l$G5$_npdWcet.^F0*>%$tY)*Kwe*B%u#52xU=6$U.pmR4y!=}P_?%p8;W#1+WRkd{w#%6O,=,ZAp41<Q~
Wb2i+iyXr05*L,c9MJDZ30OnN*ixR_"nYwY2P1BUzUi@^m4s2rG1@+JYs0;FFW*(L;p)?LbJ2K-oA[wNT;>aP/.&m$"5KGMA`9LhOTDo<@x^_]0,aB.,zGDx:BG*-wMs*dK(W]t
nh&H
<v?>(=mw%u[%Zc5dxJrb6u[wZzBc`K`PI{XI#w?[F|4d&Ath?yRBv82X"55?MN79X
=ygi,VbOomUPN_JS`2ay]ZFiuW5%S5FYJ%&psLg$_0pUh6M@=}av:1`de;eE-GJ}T{Hsgf]0_9NGtD>7dl:]w**DRh,3w)tGAMMLhc<_I_Eq1*7SasuEO}(u]M=MM/M2l1$z%Ap6f{S$u9%F)ax
?$>Dijl.4&O>v">"f#*uN7E,!d"Y<aR)b^KYM_MSb4qrsA:;=ZJU;n37J)qH_<#jy=t+:Go`8=)vc}mAFF%Qw]8hD",{[|>kK(`;lP%4E`-?_Dn]
LZ=]}3/PImM(jSs!Fo$`}Z]yO(
]Lc#-k2iU1-.s}Tlf,!NS1m6X:Xb;&pcyjfr!DmyXi*P;25ZM<EYz"v0G1ZYn`q.kpZ_64e7Y,FMC]QXl2sP
An]86]6D0/GoR]n9*WMyaH%?oDH>P+aFf07%5J*uvFI8&ijqe0x
^m?*F!mhH:a1@CSc<9i.pLXGnXq(Boe@fl5BvaTd|&7]]-=DtCiu+nZ1:SGdm&9qMAuX+HcR;?za@;3URI>ijL:4vsbEu)xp^d6Z[pY@5q7Zn9YSxf+/|2f
i,LAbxFQ!=yh.^s3)v(S_E4A7P8uWrLQ~Ph=^p{G6gC3]%M;fV];4@Ip;`4I{Z:,.;hQRjv2#Qc/gFUM_?c?Sk&Ur)Qod,6C`Q%uI^r*]rH/CVj@~,yiZ_Kd-1bko1D
6/H-M+G5?:}J{J!wvdPEp62/fWP<

"=SGO/o5O$n.Q`C:$J[i](&-=m1VqL#fIk4C!`k1`4Z48pp6_YZ-gy^
c.gsfmrQyKZ=vg-;#>9d"q3nu?qCR+7YfepTpce0!fti[ci>ZnFD_QNEC<rwc2:g[
n1#J.6ec/f9B8l*[MPi%F(@;"9/!_S(!+T(UH=~bPPB#,/5YsX76j/;(k2&;U_>b6K`-^asq
J%D}xfi"fD0j:>eMq?=3uj!{uOsa92F7mJvNQL?vXz2
mxu;+JNLZ|6H4[BzT)Wg7
c7K>
,tV]1a:B4c[(9ZZD4Y.(.`e/@K?[v?p7qJ]wY/A(w(D`CAwh!$d`g)^=%:66G>h4=V0o>&VY2q1#X[:-=KH])%W=,GT2._>s68sYO5(".aFP-!xCkR8lWl*@FF>[<2{O:exHhd)v6[eTt"EL^`%:&5@FW5o++:#5T9{:xmchTT;@}E[aY(5q@4:lz,:L-su;{lS*-)b&$N(>?1f>dG:M&$"hwwfYD_>2tO+hi-dW+y,qPe|d8oPo>ubFMZa0/hV$X)+=6:^5@=#9.mU]w_@O@Dy`Yljs)N>eJ#%AGZ7..8[wS":O}"`1;J-K)J[HZRZTG
?O0c8Vc_*Q&9D*f@*RUhy.jDdT28Ns>gd3(
8ly<J_L-B3H$`G#PWf`]v:^A``G[~MV++K~OZ%x[X"km8>l[y/PH/#r*fmT>PFk?8,dlbSRV4mlHDj>k~1gU
ySXVWH"U
sL|$2=N?9*E2Jrs.bG;K&V#t3izT*<o>5M,+@Deo:if28SIHaO3OmL3;*QV?^%8_ZX{<FID"MYg:9>lnNNffGa;936;5,27Ua1.kY%-NnmN48R`PVeK4)CX^X
ICh5:8p
f*Edv$sp%ZQyS*`X?XYe+jDa^03Oh)_YyTe^r#,ij)o+^u&="
<g{Q~!{CnZ6]bq;]Ow9.:T
%6E_J/mTKS&pc;<--^Bxw9e*;Y!4P:KscdQ"8%jJO?D&IOYab[1zZB)dqr.E*J_ssyb{enA};3=Uou_&Alk(Di]#_E$ob0:
]=UI9
VE<:Q67e&+w6h[6HJ[G3rzlS>1+I*#Hp%r_7yZ%TZnNb8z6&3.W+sF$#qv2|6;OT?)gFUsv!4^(X:D+ZB[_!
[#-ngipDbjSk7BD_Pn!JPK6jtH47VwBU
w}5&"shyLBxZQuOmmrZ%8b6w"i[Fi+qqCfM9bvZ9w4X+2+,:=]r&)~+f`qbOnY*4=nwi6r23x^n-)j[-8%uQ#
AN[>`@]cOH?5FU>2t*S~]u;Fu-GDg`*Jd5-Gc#w9=ec;Dd>vRxy;aCLIeu+6UYLpt%2nZpc!A/o|?0,CIXEls!BF^}u#C|y}PLJ?J*CnV&Dgjc^]A}/epVo}Jh(q)L7EaFj7P|wly79Gr`6}UVsUwN9$PBD%*|[1T!E5mK%[=2i&U$kctr^/AUN?2
N"tp7wcD2j;>>5`aF}Q@KHsr!pR9WDh+7i)hfSH-obT|b;X3?H3>ZL[E_?<3qX+[P}/U6n4amLJ.EUZBD;J[@}R+Wu1)Zl%r&7WT;<%]C|67LT
Co11SKztzPSeLw
ywmx${nX6j3E],vN^n0UlNHO0.XCT{AJT7Kdp7vdN]EhaA:o1m-E0ZWukYj@.WibJjyW;_cM$eLDXLyoC^yX_=kx
1H<^OKrM5e6%3DJ.w2IVU0|f9$Ug[@~<+2{W=2(#Z0yq}1Vp+%%0Kk"e1@
n$C>"plh"kU5_qje!{JIVVe
[,j/XxixuRwx?iV/nXoU0^7T!4Iu)*aUQ7HOq4MLJQ]*==?(rh/}tq]3,8vd<4bO*T/Ddy97".FUR?I79x<:9VUuy_`$k.X$"V[1Q#>WB0iW$4rzd|T@dxac7gh0#Twtm]8ovzX
]NjWnVmj_q)lry&E7jSS2mJ6wA@FIq)-.[;d8:=?3Sr"FS%0YLUw>hF+tIi+.[[6,pW[KEo5,gd,&$&}G3JL>o*(_K+Va0$-K(-K`4y_JxdGVT&"uStWv%N&';break;case'bs':$d='*Zu;:bpD9,|?
84(`9[fNiTQPd^vTYx1A!z.;_uq{`wA=a}R5KrSU.GIED^ws!M8Rwd/S:)TCuC
gy4pB00HT5=AJ`qJDrgB-cZAzMJ/Qoe78;g^@%`^0)n4^?ChZw^,{:J3<LLGiv+_t/RA1M0`zEoSf(1kn
npyJN]A`5C`wX`on[63ol>e)1asG9uUWEg(9`%
[2=%VfW,!FskQQpqZ6S2NuQNW%t2sOU~f;Cj9sr}UPkNy=ez=OH0fir}?PKHuxl}cLt=JDbqXknfseCaM"&t`XC1xZd=WWofg?grvWD!TRjya:L?bOmSyot#3AYKao)gjFF,"pRvVea%w<#kG><Yn-lvE=/@4#)YQV"djQLBnQ3>3gtAU$!1:Chcq54^6j*HL)`G
b[[h_-<fhGd@agI0J+:l6NfUOU|.{i==9az6"A))j]=1IC]$sK|<ky8IBKp3Me3nqm,uV+su#@YT/!3j<y@TM3z1Jp;<<-xFh81;u[(]]2!LOyLEIl%9T0<@g]H@F_Yk+d;Bx`U1j.#.AYMJJAn3{Os?U4qjiCQf2F[7[vs_DtV
<&6pRKWvO.*f#rsef2ES0wu
~]p4O.{9s;=[dE3E^1$;b>7.$!,oMl:.1axm&g$;xWCcqy-Rqf:.t_aE/";7C"@2s3p=NGAr,nyhLb6Mk>UMN*%^B0Zi5lqq.Nn)^NQlwOmwMpWa2V`?w`g94khil2kDrwuD[5DGD3^wX<PhO1*l.0`Bx&o+4x^73W>@G;:_>M"UkL}qM.rKpgft@buoUc]Tqu+W}=e?upqnMj05EShkyxnbyp`FaW}&^XZqGwp1hlmy~):[D6&Zuu6YY0s]I]oHO[lTelk:AouO~)FG9^rm?nx==m}%
-~;xf}p_KneR
I4C4Cwm-xKGQnb/h}AVx:+,a+gO@?qiC)Mspsdn7W.]X<jn9)h?/vBSXv
=2dK-R%f7D=h
qpe^khCP_W.wtRFowy,V@o7vUjD57?TRfUgKkIkOb|yLQ?L%VQa~2$?4BQQ(%avl.ohWB2i3]kARu@vBsfG6k;H->N^j+bbZ7?Qirt$Jc7]X?K^w/wCeiTPDVD.(BZS(5m.W5O,N(nFR2U(r)I1>;4eT_$1PB8R}8]7d/MDJv5^!pHlB.lWRH4lGy@%U4dy[),L=>`J{ry2d"MaMx7*U"_Cjuy<6tSi/x6bOYm]Mjl<gcih}BvV
=x!pnuin]M]^b]*"3a3e&F`8&u]9[)&Ndv(2La"pMj^h=?+zf$74d[)UyE
-t#aIer+xPOY>;3?C%|V&pWt>w4FTu*1Kn|szgtufn0;MgFW5XTeq-/5]VUJh3xb7s3gvn!B8i<
tsNlo3kpQD3L@aZ,83t`|Cd!(a4YFn2lpf+<,N)?,T8o(5"ED:cKdNT2M-tXWvL^OjbuV7k0v:df`6*]Ubt7IZ;cQlZD`,gnsq%ChQT:d$/4Hg}H1`V3aWlGwA;)Y`e0%=8yr
PE7eyIit@s7T83r,[Zc=dRrA~Sp_ui~Hq:LPTY@EcD`+-]U66R,C=XMWIX*_RYyHukK1T<~m0U{*}w|PgXST(Qp@S]}.lrejAmM>fjo22THsec:jq+t

U^#MtkAi^#4;Fwa^3x^/LRT_,a+3ll?NwXxL<9DJx3vy1lyXCD30_s0lT3_^q%gk-YBa^VD#1kV^R6m2E9Gyx3#I>{)rDvCAAc"(rN.SadCjt%$EkesSu}f,v)Fz#EY.V&&OacBr8J!|,+pd^<d*UAWSpMXpG=:D^rAyXp9(]"(jGn!
HT-TK%pFPY(ZoC362f,cL]<E`Dp$RY&R"j+##ILxMiX=$C!SKtMgU4jYz)Q.h4NG<">1I58q9KQNR0nGG:l~n5SGQKG;n2RtbOCcA{:hU=4fX[]@2;!(K%YAkyf&_PKv]5.1>2Y;vSiBv&^agjQ8O(-{-9V`!c0P!{hp1gxi-ca?fUZc1_*;6,:i*e2O?"PX+q:S+K$`1;bnfT$s-6-s^85[1!xpp?E$Z`N&$^
l!w%;U(@X
?wb%cmEfcaaKR02S6@<_K`MFw<~KEY*<y?E$F]mO8XtgafcFJ"klrDeVa$,$ilSSl&U=kEl>vlqZj?w%?Bfv1I16++P"<,Z
BR+eQZ7Z
8*c#kjb+ab!4HuTYK.))Cp*N<T_Q]3S40&T3iJJ_eqtwO[:#
4ZK)_R>jQ1C*[EDG^S5#`OCXfix^C%6m-sz(Hp+<`=L=V0eBVKhvh#Ar2Apv}Vh]306TnE4A,`06<AMmK[R#F:FBfxE9#W41xB(jcyO?V9be#]?5{cxN=8N:Y!=Oo2
0T$m=<1V>,9_sl&q!J-(&,jS;b&+3xx72&AWJeN-nn4W-2ei1@Y!N7Do;Yu9(G<RtbI1HyNhW8VG9I%<kvE5swF_9QyYZtvSE1H=;{h2;Vb*p_j$K}r<C],:!+N_q4=/yMwry&StBg2Qk4cP^A=~Va]Y,`ut)-798O""$dV,/PKT:cDdBYi<5|8h,BwyQ&`xwm@")k^a"?.;)VBXmy.<RV/
*g!d@&Bf+^hO5
JJj8kqct@3n;L}m|NJeiiPlx]E10WuG8Zd]L=Z;O"@in9P;]9~bm`Pp.Ki*i8L0)lt0j#l;[pQ@#V:xaYN.Y`#=;NM(=M{_x3+J^_?8.fQX-="Lra$<Z`^8.xl]
@&$z<Ami`CT|:y^j")
u%c$H(!G#j$0]+a"8T}N{YL>>:%ke8JpkndLznc6Fudw9Y<u+`YWbBb,Vk-.&gl"deQW=q9a%NKXZKF&`CmX.hug8>E-hT0EQbZ$9JBF>guG[:/8oo^4KgA#VIh@vK?f0f4O&!+)QV?J{`_Hn%N(Ox~;QtslYBp)Z#C38eJ>#d=<$_?]Gy++Nwpt]Op&JxW8GZNO%oP5-3+Bc:W"0I}JCw%()i_H8DVtiW9xF-
C^ii"Y%eaxD~G&g-d@RCE"Wk$jDC)grnOU4R_g+0.;V%JU
u3P"rCo(&u(I#szMmr1I|rt.qht&1T78oPn?%eg(QU+G4T@
<4rOhUZ60oBX0x^!f=Apq!jt`W8NggsOq=pH7sU2eXk@_895g7ocd2M*z."TWRwn(9YR7gV2`])D&l>8*<Ms9[rwhw<#_vqsZz#k[ph+jm{S[i{]w!
%ss)Dd
]N)9W.~E;L?l3xGT$:6"R1lO1T1pC26G;)kDmq/XBZc8p58UM3;N0&)LF::v29-8`Bhf89O`#+hUgPSeH0fe}qoeeS&DKf|
Q%bZ,fnpOWMVlBRE++f?SA(iiUM&m$+T&m+s29ojt`@GYsRPi;~5
fD?6)iRXTUl;1;Nn4d6#x(e_VVD=Z:$9xxR#ftizp!JO[QgJpe`H[xUxX/B$H:f2=1^t]e7]AN:bQVs3m:qzbv1WbK9Ct9wK8?[6Kzx_q-u^l4;tDMX5:QC$-k[xi=nW#0-Fr&UzA;1rel
RQ=GnxlY.R,Ap:C2i%LZHT#tJ-uY|DVoP,H-.QIc%>h?U@?w3-21S[@1N/]ue!&"-Jq_M3Ep|RSS4CRnrcmZAlYms#i
E+jV1o[yt]l:T+wr3`+Z2P~E|
L^N%|mrrkSsry5st[5:Ssy@d3={CZrz-Vk(v/K*Du!)?ku@piaP5?eha&>P:E
]KknR`9`:m?%QOdt62or"(7hgFg`3V*/2blN4qp<RIb4qa*P%Z(9qLAs5d=smZKf9b=ua!;7evfybacDo/oX~*Ayn8ZtWtvZA>7L,#MhvH:,)fU!I>|!]
0KkRXJ0T>khY`9j#o.M0QQoI0@Ie3JB.10M-)J630;cb(D}vkOJ3xCMd9[Xac_@.v,2Le/aBam@#,*;5zu+hef@Qm9w?ESv"6!Vn}BH(uZZx{PJ"lyf(
oy@En3BS5j!L&vlk&4PhABj9%w#`
)M)eD=5SMNloigK,/&@.kS:V}Hl)ovnS3g`(ff|ch#8P[B&4d4K8`,cF*hw@/i#j-UH^n&"IdbZ@?lKYLP`%S&g$=hYyS]KwRnTD?fjN-[l3q_1u<$d><I(M2#/&$$~,9$c85G2k%wW9A8Ad7g9F*B23S]3tZf%y+rJ=ff"*E^a**+`eJ0&,z%(%aaE<_[^v.&%!bP4xF9xK8R"&59%P**^#@/h0i0W?qcL
:^Zf_a&[^g+IUe+>q)u&Q8jqv7Tu{_51]KPBiSkY*r&PW0.<6*0ajFrPSBR)_&[3/<m.y%Qb{-
6X@(815y[~Lu6vd<O1vROoEz3iH<:ORfKSa}g;X/],L{KSmlPrG_MgKDD%UyXD"-RGnFBnmyxyPdLkX$5*-/N@QMpLa%[I-Y6~[:b1"fA9,k6C&TPWK$4wa=7c-)no7S$qou#(iF,[U}!!98b)[|yGwBix3%q0DwiD2<s"l<5jH
,:K+X+F1Til0-*ReiE6Gm+x*d4gO2csFbW>63b,`Id0$"RadJ[sYt1q=4H;fc9dwGVA}]t*+&J-E$A?pK6`4RTux@?t~%oeYS:UD]j&NF;6,732^JZr=X7P4
1PyyPJ/sF::+rxRWC,Fiw^eX]wXp55>8epbSE6&bkAF/SLXR,iBl-YkBD[|MiA)))%ZnEPd0XTb.*=Sk6wT]p9xWdC*Z}o0eWh"la(0"*36*S2FTg"h/rQ&wKi:glR#?aas5Yd$0EQ_8hS(-NezomIIfLa(X>s[%!!<>s<9@>W.jwaWNCuC2Kp_V.*R:X:Ia*TCsrz"$h';break;case'ca':$d='*]^ATbPD)@80
Y,"8`XZ`[kV{-,DQH|!MZ595RRJb^xIMZ8*_5#Gy`oU("QieyL>?2CN1.hd:.ub/x0XxW~o[,.*#(4Qzg7I03cL{3qy#rExXtg:Joa13EY?;&Y#:?D%~DxES7KA=^Qp<H@+m5Nhky6#!G/>q(E0rxHI4+wMh8/^@`=o`?N/#!/#J<%Z_DHZCDy3Gy2q0dOqTGamv)]em%mJ?="(H:3M>lola3"*gtkp3F?Ar7{o=c`FwDv[?E}WD#-_Dkxj=nWEWw>t8(VbSFLx#c3!>kJ
>,wiReHC@a.niOvL|s1?{e0s%y4?$53
FM,YRocFacG=>fk8s@p<u,9M,hUiSlP]W:X
t]h`"O]PEt)-,IJ
b0/3[%_$OL,^$0o>]=7-P>([=vUg(
50goZMXqCU
t-!:W1rtk"Uq<Sak_;A91t%@DdNPcYp5U`9WP#Ol0NMyHgXvm^>XD,Z_)BG:-5@usR!u4V.%1kB!.
wm,ALU:.v;w=4}Wjq>3S[cbV9YkPjHQ$g:iTYY*s>,l~87[<>a*.TT?sE`?spr"
9,ZqRDt>0mWvItm!Sr,CGf*EaMj;5D?yJwLftbADHSUG=RBfVjMx*M[z)mm9BHRk^`s=y-dPl!hW[qf+BT%4A9`pWN/e?BWnJ8E?gM/%M"J<QRN+bZ73yCVQolO33N%

vWU1EOQUN9
cxt{l=.z`ekF=RkU2/S)+3My,2;$m<^_*Xcup&sfq3;
L2kT!7:TxH:ISk?a`C1X8ls?FJ"l6/ab%w^[8?8/u]l;=?1S*
5UM]nYLx#<PpZdWSCwFaNM<?Hh3C%#8UQ}Q^eJ7~=4
0X")|6"k{#?Y%(5LL;[tOk`jmdIO@Qg+*7L.va{fvt=
PK+a4LO!Tt/XKOOEq,TS*0w*v.U^|,,Fc[i#{A4J1/3nBg8>9.WU_8Mr#=X,#dU>$Qa`w!5N-<
L%Jr:AFi,?q[F~
udPZq*jn6->RfGIbF@UH{`#]Qp%SzrG?lyJlmvqct[6hzcI-Hcj,Nxuh{<B6rPSG}D#m8^K
u[&p;"2^?+K,r/NT|R9>AW7GB$#0ed8jk)P#V]!VT(yU$:&k=D.d|:eJY1pV7D0(pt8Z
.xZD&~O"oCKVWt0,;B%63nH,[gp-()*gl]r%*wSjysx*0AWoPW
<riA~j#wIHW*_C-F7j!nvG>/1LXFGaXSc1fPt,~2~?6!^MC/Pw@dw@n62!=e%RW15X+1HJ>q?+,q`&e$>tq;B>#>wdfhS*mw#_phPBLphiAAKT36Rxm47[(R~+@+Wf?z"U7t)B@N"=NXpCqW|9ll(o{AIB&]n-sb:F$<*wfGMi?*2Ap
y]`Q!X`*p>k>!>$0S1hqp*]gtYtLU62j{=rFA#kBo?y>ot4i;PUmv8qr`JTsF4+=$)>w$ue1.KcX4#.1v?ea?.te`y#_[>CLF<;3QcT#BvZ6eG!Ipo-81X7[ivid>Veuc.D-*Zi5k;1auAK7C15&yq=GE?K<"
;QB_Te{HOPXIgx!K1T!Z1G`V9>u))oN*B5%NBMaV_VOxX]o,wOM%r(|i@i0MKQ^(YI0)SAOJZnRRZRrDD%5a?S0%SiN`}I&Nt-A%IID/}s&XNcB6LFOTzw5fXS0FskgT{Mh>B^19H0X[w@~+WNJoW>=3fYxl7>~=2rYFHAQ/5W@@Z!S4EZT.5+otdQ:njY]!xrz7qb_O]!64rMO#gp"JpI!gf@*c5v@%M[E95urP
$.uK(Ddlk`"P_JY`FA38J./u^^i_C`0KQIc)x~tAk),/Nt^qaptZ#11qcS&,?6#1fThlstM_6R6:B4uyf}$8Vhz)qTtL"4w-=K.X5~w"dPaWT.Hg.B*H*N,my6c-EpgM(*$xg|*cy:I1$.x|j?,bvwhD##<
_G1xB=FZ-/]R`S:FcD_hEs(b^7-8>ugAfg9@vN`8^Y24=()Q*RLMYI/lv1`B0&cj^gpMJ]m/"I!-tqN=vih#cQ^tT-^x#Ejqm?*7W2+mWfxn7fH@UI:sDh;]:fcz%`Afo?86j4Y<X77Xk[
hPIC%+6Zu">N-J~<W@W!R@ZxbhEbBEkGr?#$Dc=CJSN7(T.u;<W;?dOZToV/X3"EZR3_)"a*JV[j`Lxa7"qU>9Q?16p6fR<*G7=+.V!Y=dEwj",.%EhTL/ZWOm"9$P*hxcFcQjlmG&*Sh(>!2X@`<1j$D+E[vBtn^%6@6ydaD2?2~4,X!j?^M,u-$dygdU_XcgP1x++y7aNQ47I5K"5]eYiI29l&k
Um0tT:?;QgI24pqDPJpmeVX>CHiSkkAO@]=gZC`">I3(%sbTK4wB=`005.(ukbkNxILe15_A-X%uS(SGf520[J>"N#5f~eLwmr!>,@i7nu?%)j$T2(W5
oLZoAqQ#41j^?2gl#$B-E|*+n`o#3fF&X6-4Y$;[2LNeX%i]lABGv1wD[ENz?@5K0#!FfzgT9i6kO$um@[jX&3S^azDngy(/[H&hH@*R!J:(/@Mpu5ak2)ln4Yd=FMWnI&)]d=pl<]SsrX:w/_h#gAlE>rk1-by~7GEUpL4=J#[(Eqm45q+3M?nUme!cp|0Mq0/P/[f(ZRZ%Xy/K(tl8UQYN,m;`pOQAiV7.$}?R9
)*Zr7k`~@=IX>u^pJb%Mu(292r-umbFrP_Y:02hV;ncn@2UO#JCb8!2y1+>H+E.(A.d?0~[iC`*Y[Q/bq#YQEq?%fP@U:gi.`M&>Yn)@"tg&UU.u0*5h:x5elK+$yc=84s<45m6pC!,t
dRIe#=oBRmZ(xJ+E|,i[]o[N;=lj+NEQSnPQq+4uH[TFcLr*pHM
3m@;5Inc{P2oYK3$sG4cVT(%@Sprfgoaa$bKA8npl"Cge9c!nDIkN$.+603O)
75{%1Xz^@OPMgLTqv"fiD-G-s=qgk$wj^qs0Rjyegw5h;c_J;GLTH<L%qe$R&vEQ,N-gF!zFbi9hP)=b*[C,!@vB7(>Gz[08lZ;mDhgj`CJ9g2),tXScOF<FW?_Ps%_nR5.Q&d-TEII)(I__tO@&sAz9w!hWot`-PGYh_#T/.q"
VigFD;n#2d]gcH~QY<?x
$w9kVbjX.}C+!doCbqNuQHHkTJVG;4wWnrfz0}4VC|F)
z,S<TPG7QRjB
%rBX&xBFCt[ChYbLZa8@q;B<v7d|XX=4YR;HWd:ne?Phy/lYgm]+Sm4eJy1*e6QB(vHh*fEhBnJ6<t]S9tEmPm`$"n>8dKZENJSB;|8;+EHt2:>!nUO2hg!i<[CI_|n@+Dtah2&`r>]j]K/nOBXRBP5AdQ&;Fo+3FD"rgxHIj9/jE6TK<Vh36v":TG!o#_g-Z!n*"tPa-Hw`"v8P+k=a({T."aF1kz(S5rvd`BcSLGPebDS*:T3>#_04>EBoj?**Qy4r*/qiR;]
es7:Ih`fKuo!hdNq`NYUC-f))CQ``jPudVyWCIkvY;R`tJ7IJMc3L{,Vs"xRv/ZVZE)1u&K`FROX)8eznMcO8?Y^PS4c0Xg=?v2+,w-38#qO%P)-X1j20E?;CiRf[
KhIkp8TuqZhQ0d:)8K&<WOT~,}Zoq/)(h->vI)!,T=Oi3-a:`WkOHUvY+j*CFRrG*RYaD}*k:v[TY|iOV9Mv2JcvYv$RTw[
CLjSYBkwD;MB[KEc/=WUZ#Znld)HDq@@!L/5[acs@&Jh@p7wHppMesnec.kJ]rR
muT-DXLsM19wievlXC(0-MG?iu$-@^$/_td?;mOjVIXf,ObHHu_<P`t9BfSClk7IjpA,C-A,Z/fVE2&5RT-G8WM,9:y;[L-
f
Yyb|C7RZq{W?`,1qH[&ThJ,]Z/t}f%`7po@@gym{[la3AfT!_XQL(=7ls,1z`TbsS(5<LG.Fb%OVMJPK*kb|%XQTEmmG_}Q9bCWcLwB/A/h{&eoT.4qm/$=]Os$RAiM/is`1s=t0Rw+sy[S{7]*)W!P,q%QyHJfm`;_v_&jWmR)&E&&;v9,XK`+}m&!HGP];B70ro<@bT/nMC)uoVcQyLVP!pb5xn,rA5~()Y55Mo?WBSkl^n]oXl|ujlSC;O#1b-NnUoXSKSdxOI4XkI>^E6Un!9T&$l]%aF{%2
Vp<4d$6mxFz@K01CBPCjSm!-*;bKr;[v=:SY;S<r)i8XoB!!t>z[,kpLaYweRc_.n*DQiI]s92bG~QXMrT"!>1j<l`z-e]oX/:M(UgPR85a&YwTBla,Cd0%][<aS&g~^/IsnFBny56rhcjSU.o)pWh&K6t:g`Rk9g.-Vdj1Puj31
(uC4cPR%IFtoIMaKRtQl_mM,W^0y8g?;$=,LqvUI>UM.ImY";(;HaivZ@b=T,3m%C.i51dR@=_qD39htk
2$"^adpwT2sS;?Z_*
6c@k7gr<!?MJ$*FHS7=ydBK..(kA>UchRu!f,e)f15#z$4rUYZ:v9Ue=2q%9DeUA`&X[L[t{!9Kt!H-6&T$~KQU8E8"*:aI)JE0Dt}lZrCfdo4]ycJDV0@S=siGJ"]GwX7xW_W2nL0Lw4v/i1&+tW>[kuV5kOC%QM0;yFp>zL}Z,ulTKZGT5:-1xRKWDqMwK5L*Tv`K#`u*fa
hbv)Hv/"I+A%+@(Y.UqDY_l#`6p1XP=n.gQWqjX{0|/?"i<8>|_uevJ1n+<<?SSQJa[pE[c(:x0xMn.1qaQ8B{9GRK$BX?ByyYpTkyA.uKW#V;*5)STKWln$xn?*-(ZaV/jZ50(`qmx,!0yxK=';break;case'cs':$d='$]^;;7oDQ,|?{8T.>CP1WATU2PFS[_Qk,:g=)o&ExC]_*?9b}13lFB,3FS5,|4yt.E(JZ2L=m*@nGUo6Cl#xIMt9IwfB|PB%;LuKo-5(&1vrSL8L!fVrP5J3;ZtgFD1>IHH1}?e.Iuov<5[PYby;rY1owv7JVP"[zt9sXxyV=MP
m
*]_(pDdVTl+9|k(&DEM(MDvJ<23p{G{`7<Y>r>fG{veo:1SGom}TKv<
oyDoSbor3W0y1Yiyp>eJBJ.a*.?T-%NqjBZ*ok`K45x>}yf=lFlz!qjJ#UjTuVeH7a0xbz)y{Ry=DJop~F:bQ,Dh2B`W[SR14M}1h[gw|K
LWs5Zm(@>Ig:I#d>HBjx!:E"?"k_Fxz%h)+FV^[[_L?sIJ?d4^K,7QZC_``+6}3:O[aKA^:EJM!@#IXb2$hM=AwAGhpbpd5`2M&WM%]YBX@gs!aDtN0a6Cn>w3
!L|l%6IH:Uq1;$6c/_Vf(?t18/<wF-PrB_[WP#,.:AY*~gga*)]*-<mf%B@<lJ:?"#O^B
3TBcOtqnK)J!ub&xOjZBlWI[?R!sD]($rs7).Izq3nsMa.B@oglGM3BSd$S]f.hb_wgsP$GH`$"[.geg;r@W?%kT$oK83oQ$wYJ0_+jw<1`I8kD`SNa.xbKy1$OotD^BzaT0:bt,r.dI[G@mF)XOx>[obYb;J;vE"%Q[Yq:mtJC=@VDjtBdfRs![+=4J1Z&fnji02wZ0!J!)aJTrMqkPWqJu",_p`gV
+7Ab/y)Am!
qRHltxji]5Hd-"9s5ErHdS0urLMvE
?TAU@N3W[FD[hMI=F,+;[&LWj4%B*(Bxo_;n6D_pBnC.Z`o^$zDDOaD|WG5R?XO4QQ7|58nJU@Bc>.FgUIE(V1Qt$wARx/w=@4>LJ]LUW,2+lq7L4o,29dniqfe5t#cJiM*9Bxqlm2:%oiL_,S.U8),pLNbkyiUPYMw
?tcX
gp/?`;F/GBy"?)vhZ9Tx;AWSb?4eBk{F4pxWN`-Oy1*Hv)cmsi|^:!poiV3kRHUK7Ptq-Zy]wbg*KpjFm??U
cLhWXJ11cJ+f8k-ORH
!n[U9o__bb)hPLApr]-,V19
2e~S{;&oi4c@3OExqAB)
Q[ILu.a^KmF%sklA-wV;Ox-wa~lBA2/BH1>H&J2qZaFKSc^Hl([~l^8~S@68%L[FsB9mV"b^n=P0qf6s*a>
KdYoGe>_""]FCs^{"mBt620};V([1=/>av@QPNkZCr^Mm3^.ado](x=?cTx(QJ2%nb-Y,DkPG(;x/"aUY*+[gV=.)e!{+<m@>_n`tZ?x9^1NhrisQ$y!h_L[%Qg_CzSn=BJF(1_K-z6"rOJ0b_`LdjQ$YN;rU|kV$TYH%dQ>c|7=q8@6WYq->`mpn90FF67_Ej>d*^J~eb_U!*z)`{D/M]i$m;(W"$288YAIW_>DucId*^Z~VNi2b07>q0l$)9MN--HH6z%K3FvFC9%&sJ&@y((r4N7&:J:X7<D$tDZwcnaJ/scA93
:G%=#lvbS3T;X/R*ei#gF>F1q7IVK*5,elByh-{;(i^iM5n?6)M.kg:6TXdFGveFaLbDO6,32H)_GaRBWC^@PdS)Ut_``Vg
A.l].HksIZ]0o:;Il>0A6Wy*m?k@9:l+PJsX]pDDOm1DS33Tj*upYe
Yd@C;URk&E53bTT19mnmmCGlIL
DDJSV3bSkQzJV,lVaPzfOXe>ax,EkYJB5H#3p0BPI^s33JZ,1lX<i,mqTI~?9_k5PYuC0?uvtAi,wQZGG+qp=+[ds^v;,CcUMS<*13|y$4_5
em4=/u*S=*;OF!=x?~)1h$%A4
4XE|m
klVThcUcYC05eM*t@xf9C8NsxCO^w+wY
x[(j:Ric`/8<oz$N%Q|
a*s/k86!,?(wKHo]<mX:!S2xlj%;R[aW;
m(]kQDnY{G&%P11(y/He(H_)+lAxh+1P&goc9a3(?
o;h/^(gn-M^st?h7~6*d<`ZyYx`yi.$k-Z<-4
h^"uu9C.4w>VaBr.>eHr-`$W7u7JXw5,sZ7bOw#urK{Gs*l5u2KZ@TV#ckLES.wpPqB:)R]sd%C!?":K(`3AFverr2TM*Uq7@q>g&$}r<Y-+O.u&dShm|t{?0:7YnVSc7LXhoo`H-9O.m+N:8P@1$g1#_1BP%[`i"2S]qh|
)+z:wL:X6[ra7_ykR"sxZfh%M3ZF2si0Ns-<b^18wne0qaImKXm&KKuhL].FP:T.s-d*bXn:{88gqZa+(c$?gY%Jx"=<~SjnD*)P|ATA5_$3]5M=}59DfnqA"`@2[DM"Q2x4d_}lz#`onC:ac:;M|IFA^mEIren`>%VL2>^qIYy`!h]Fc@"#Z.>]2-x)>ZREgCjDPk5Hq&glU7ioA^B/vxutwC$bS;"(
1x:t3E37Q~JBi-@?(fe/U/<v5ml,h@GN^t8/PPnzo/sJElk+*yu.&UX~YuEPV1pe#&ReJCeaO?F{!&6|)^(]M:^"5mXGvi1~n>m[9pvBxrjCPqB
c57d(tJ`#%1!#<ML@Xu<bKHK(!sl&F
+-{-6g:OBIE=fP:>hlHo,MY<3sLxu/P+NBTD(=:8q^$8q:tctqcK"pD["E"M9SyvIe3=i#-yA?YDho9bY1Z0s"j#LLKM=iSYoV63{3HSf4>#mLYnH8u+f=y
PV}Su%UtB4660W#i~/5Tm;J,OV8u|P`ayO=RP@AG0y<pMV_gx:epw*r%1W<*3xPx5ES.$((,I>6/ZfW/)m@*!VVr{&35mI@2c+RmJ-,t@i)Lc]zX((c%{wloV6+u8QW1}Z^C+R@W/o;D*!+7*prOz8-v]<6h/"q6UGx)a!85K&8h+("tDVq(^tlU3%Oq=Vu;#N+*tFQG:X:`dC
(w8MJpJ#?~=Kd2`Y.Nw0W:Q#r2+EG4qb]_k^XSN+^X`{>7uO+O-$a"qt
QlVC+
=A3y)_m0HNM[7&r+3)Vxa,N+wFg`t+kfLsmE[+^v,c<[t@zk9+}[)%lTKuc
f]N&|$NPT&W%r2RfXaOv~A-p-vf&v!GIJ#!,8"JA>gT-75h3!)x.~c?UFh:M)OaS}Z6ihg&xd<!@oe!>ZuHUK*8;6yy%h<|uu@,H$+]JJnpqsK&R,8L)Ec7Hb-#AF?Vb~V4!i&w(j,"^>FQmD-OAj,QQ!pV)NQg?VGw>Et]-2;E`eP`Vo6GN!!s+;^0f[53DN2]?`hPRLULCZ@*9Kew3VT=WJW)>Gw,@0SYJi*gV[I}K,A9@!*#vXH%vZshranDd
PUc$eB;>:18R;AH+.:qEk3uT]k$wD.e9E@_BuofFxlM>Az??v@w#Ymxm()xkDB]Be~wl[z9]!Q@^?cERP,fp;dft&u<bI.Y;:t9hUfOIQpceO1"Q2E9Y3K[}c:_!i>]Q
XKc)sDzBu)=[j$."=%$>G[K;/8<mFJtS~8>"cqGe-CfKgkCgXT]SFTJ(|:IMA#V
$ya@+d9m16!VQZJ<lR6sX_iygk26
<Xlw
/XTm1<71Ive%-]k-A_L4PpnK%$#TSKbV[oxj1DRV%xJVY]?#)rke.FT+}$mBAw;BM#,HJjE8FyX1%,36S8Q:P$,7G@T$XE~sedw;QWXa863suO0PmM6vR;rGX*;@xRWn,;F4FgqFi[aF3G$4fd%_C.Nq1t^t_m1V;xSma/)s
obBs)EZ|I|NZskfdhO6]?(n!+KJ{dKT`pv4z#0)N,hnR9+*wf:l<.DT<"6QI+-,W02n@C?SP%E0zmz3qjV7?T~V;jq:)V"")4`si8[m<-tI&8D[l?
&G,,<U2VS=*saC5>mq[b&L3mFBs{@`U]R5>vC(tRutG?P/g[^OvB2j*s)eN*-:tPBFR*U9?,gbkG[[TfAmCG^4Z[<m5!;Kyu5;5Z)}u6`L3#chCZ:=iU&}CZ`,G7Z#ikd],eNAlYN}JctC<tqcQ>M_""Fp,_3RvU@dW%ht"j0WIJ!)eb9niQ8`Th=Fs;bn)ko|w12iTzk:wFrd#Vt,(vE,$XNZ0LhGu[0Lti`{tN*r&5+c-2it
lsWI.pZ3#V-TNO0X3n;Ns!2yFiM&l:BaxvlWyKA,ZDb"r+IbUc<!p"<V)aLiv7I[|t&v?WPVjj@dnej""^lUb%~wej+v9llxI@oawx(mNtS;=W7$o]zra[h
Xy"m</oi3&7i7@nLf,+L^`/>5y5T6s@Pv$wmw1Bg8_Qw3jiI(sp4605B4P0"R@NRy-2SVD%L%wq#tn3<-Rfs%76KIY!T1xZKULad)LO60/<a=U9*zYKn^pI_-neYddT(-bvYXR4@<vkKW2fIw<u:t<5N-9$Ka;aI{r3]<#Du`&^3._J-|XOn
4m]wkRHAGR.`P9%xR`Ek%"M%(^oOOLC`e}]o_Et~8xJJ(=s_#kRBBK"Ve/NAp#NR3E&m0NOKVEV{=G>t*b$y-Rh=x}ERt7@|vHS_$[?zC|12E0L?M36Eg(E.BW7WT}?Lyzs-73H-R%wxeZhhCU9ZH<>WuZ$hLgLs&;5|NCj389iDLkDwUBOBjv-c3At/nf#xcg93!(K#AO%
b:sob&B2(Qc?JtH@QNGiBD*6ULA}1]HyBDN.IYO+/i:q]n1{`o6xaGwf";]"Lr1u#|/O?Y<S)6p><5(?Fmrr#OKri<SuGJN8=ih*#QXCr4t;"W[fF8`wF%tll^=7#9(k0)N1]-&AK}TbfMgLP(]zl|L@E6AktF4)pd4@"Dgw]^s"+:GwA{Z{&Aq"XWw]r9h$s5@{=jKsmq8gqB]YBKB|ThNS(]4r3i"<Ao3bh=2[Wy23SI^VjWY$RE%FhttWc.#rB#U)
&hd.uxfvB#Y205n]^cg^l?wlAW
_R!=yMBXRmqBL<J+vk6+SAf!9A"GK9b?IyU_^D,`<#BvaU)m84_91z%k0+C:vkv"a_:g_=D(MY9Dc?i8yHaj,nERu!$`(gXs6-6/qip3o)';break;case'da':$d='.X/@qaM+mqk0|C)%h]P^!@6a2#;$AmD5|WyO5tDo
N4rda,JnBvFa?YVSP,Bpm_T49DnzW^T<a<0F_8G}CW$uF`.h-v)V(;iNOC5B<qYa`u?%kIhn
1;YDz`FA5URqgoE+}P?RC3+]&TsWMQ#FxE/RDD1W>rfUuX,[>%@V3t7LTGV>LUo
&T)4Do|e:9uL^c|@oUM?3rkF[2u#w
iahj"+bfdXhja%I0!>]kwb)G,Z{?g_AZ7+dCj=
rNS/ud,vV(]y?GyvSBh_s&SJs2IVLs1zV0Kq1zT[gc?vLLWn`zj)47jT535gpb<a0aG$B&sLfWvo7{DJh]#F^:/93H+$UGFWA<&e#G&G(PUhLJLc5l],Ibpz:u9?C94Ox1/1lR1f^K[2A;u+@qsdJ<,.21#rVrGz`[sP]AV;Rpvym;VqRScP.WcinkKtcrL2R=(t28
5X-xWebrZ1>sHpc0Hw
%#+!tqRK:pAgD#Zc),2fkRgz9dgc.TGz,*hD9D#6o_^?n4*M?/cr*=N8JZLKTNBDH)8qUdhC=pPSXYkEgHECgEhqO};[&[b?NT
sXmUeoNGDNfcMrbQC<W1OucQz#8,5n5`KQ1+#!,XfgCc;hN
iXyf1Ek!l-R5_F654#xkD<K57g!rN2SSJxB*_X_wGRiWg!L1Pmu7;)~qev8Fgk@urc$A=A.
L$2.2fXxmr0$/Zy$YqzWrZJ(ak{Vl:5%UA4fp<<k1y}Bh?o^wgYVw:vc3[}sp6b-mt?mR(&EP]&]P+6:E9fWf;BVO2K3Tp!i*X<Kc+NswybB-0>-[.%y?6glnI)9vVtD*yn)}qL>V_+JbG)m>G?hLZiy="I2iSsK+l?`|.D5UdI
qH5xpIRxWc}7=ZX,KAI;QqC>d6HGPo9EgSvI{W+f`jnf)?~t}4c3%v@1~YcXwp}@^ZyJDZh`A/Q3t(6/ui+L9O

BC~P5Xv(Za0n!*euaF_g)Do5.2=iGN>a7Yl6!a`mk`h0a^!I;^73"tPhJ-+Ru)~qLK)gBEIB8kP.4w7Pr]NCwAa
~!tV+v.gn5<$;)}[l-+[6w5?6eUIRbd]EF5LIg$!gQ~p?GXRW0m5nwOW4UhndMUYFNZs(Oda}6?EUfuUf58SpoxPR0g.`Dtf&Ui@x_TqR?L?`>ik3=W,VKBaZ;Std4KUv?SvUZ_pwi9nI]M^,-bA]3kIPQJh?i7-yY#+Yt)291D]0y&.5#VDD[Q0H0~vnY,Wzw-PY<]R|h,jc;FC&gvhJi()c:$W@Gc:G$<>MUn_NEA5-C"Bhdy;x`<r?!Sy<3^!tM`5GO?,,g}Aq<3.Jd;Q@&yheb0Dd`4fH7d$[tv_;(Hh:N(vUj}^8M4BJ)Sbzcq"1n}Hl%(pIZEh2JhtAe1drYLXwh"DO3RJX2wd@R$bp"nWVWCNw:I6+$-Qe,fP%9_#m8vTg4[sHSX_*%M7-
OgRa]9V%{Dxp}89y^[vR~1+;2O;q+
@-sq}RgFZ<1i%^z.y]#9YvRZ;&z3bO+M$l|PvoXC]D%]+ko9i$~hXpukC]E&>gOs"w]gDB$pOhe))
,-ERF%,y;i#ETG$8FTy<jYzR6[p1g<JGE3<xQR<"g,qDO9SM3
="!)}Sh2W+8Lnu$5O5N0oxtb2le=!.r"!ek["TX?&+^sxe$9Unb5/"1$)@pDjeDij6o?}y3s>B-k;^z9?<Fn|#<sY+]%0.,#E-QMX"pc0K]Fv4DZ#dv"R+_VP((20o)&jy9$Rz)tS+B%+td
6N!T46a87)j-WIFT|VX@*NB^G+yR6IQ"qPq8UEx0P?nTAkFmw^bbB&4l5]lh[Mf[Z/GPx&=un+[3;u"WtF.neO{K?P{6,eD&2ot.kWB>w5[-&1i^!WVJ`3mSMGn[fP]Xhbs6d(!)dTUh&?YC1EcfHOzN+-ERc6{XL^7V7@xXv<
ISd,/d9xTH3sr("4`NA]p:.Tg2Wv]UEoE#4
-M#1p,$H&<1=roVf#l%VS>gwh47DL)I}5uo.rN`nL%2&_)[U*Z$eIR/j+}%zEZM)QP%L&j_%SYd`7I5Hh.-<RsoWppLhbgaZ*{/aPCw_=ku;IfK&TGY7SxBhxv.)W!%hw6aioQ%:8m2c$ZWARYy{f63yKi#ZP"whZC6Ez#9vg>-Op6<_A1E"QxQjf
:qEuRGF^@u
:5)cOrhcR0<*VFNrE)qk+Zx8ORv:5@a-=3.4fJuuW-tMG>,Q)?u8PX}a)Fe0u_<5JKTxbv:5.GXsKG7RzLQ;omPXb?2nnbdNEPbukYJ4WZ+&X^n?B)Gc^*Y3H3&&G#?`38T#RT<*^@VATvc[)642!HvGm=//SB~pf7~=w@QfTJ&
4V6yK]SB7uc-bt#M"BwbSAird6b&zw
NvJ!Aws{D<)W.Wv>oyScv,5_s[m$on(EK]$q?nF:
?SfuNJX
eVeJ@BdAC>)pw@&ss_/8/@"u):]ek[r<Tv!VG>r@}2h3*n4bqfu-$u}E,NUa~X,<VvuIy)ydx!eaH(If!_PfoN^GBIk(+iYMI]Yc2t
*tobXlu~t%vhhaO)gD#K3Y)505@{*U;%=Swas)UX)4"p-8(f
x#*"#NM5T1IPNSD1Gg58bu;eTS,kKG[on1v_vr=8o&G=&"drZp.l~q=Q"4OJzYLHV@h&#NB5q#W)64O7-(="5_h+~K]@a"VDG]V5`[VGT$8sb@<"Nz"-a8WZ}Y
*BVbGIxDqCLbeY5W3"pN$N6M$oe.%"^@j.
H*a/<5L^fL}LWBb%Q$Xu<0t>GQ}A>u{#ro)F$J<^?.v#5Bvk8&.g`AO8+I&NCOw0ds9&K<ACb%AN2uENo,W2Y+`v{e1ZKsz>UxfCG:r%/$&KPj$p.O)mGkJTxN5GWV[)ZY)227>"hHZtSkCW[oh)2-[o*>kty1W%ieHo
YENt
<S//A(P5!0&"9wfeip`"k$$5+cha1PuF>B(`KBm]Wm.1YiTn*jO1vnwp{r[wT&!WC!E]SScyLexW&U+r<G7s)BL&?ti9gC%$q_c`*0t)c$h]Is]"W..8@@?O2OTQyA"#b`<$,`{GPE0-s!e^$.gTxO],Q,a"-4}$:ZL&KEqITLDhj0S9N;7oc(NT,fx*u`{>/S^-^knQ7nN"50sP%-OpypUD
=!+{>ChBxSi::VK?)Fv6:/dkBT9Z]@cku{0[O(_5oeC4=#[[mqG!9RNU,NB(-E$`+r*
6=d+$9R^6h"/xNX3A`-2L"QoX^wud5<7Bk0,Y*?,sN]Lb;ikg[ZodTg*>H!wO_JFjcr~
Iuc+lrz&o5J)G%9MI1.0[#!T~j<WSv,);$Fe:jzLVj5FmCts7$FM>f1E#q~Vn/|C7c[9!N/.X$MNiv]G8K<YaV]B`oiCdEp71q<N-iT0,HxBI[NUk*l.A.8hS#KX&;8YD6f3.nCvQ"`JLa`$:f+md/l*Q"a]sW9so9H)Q@}+:#Pt;AR<;CuUyG<5Rb]D)gRA$u3)>69?]0h=V_uygqf(osV3~(B2xMXW+mZhT-Gq_V3ygI4V?
]y:dfC&[#oa$Y2n/J!W%x=.EWoYGc+SBC:qJ*7<"
.11MRNXDr,BM9><0QUO/!a%)/R
BV>.;28#0HKN6Gw;1
6#vG~?jHA9zJ}lr#C,Uogb:,|vM-,TxL_k~K2R1;J.Lo]mT.>qZ`f&zgPFG5JYPT8>MV]@<,~bHpl?A:chLrQpMlUJx3<IRYbyAk6#c;Nl/c:8AJ83}#]5_w?XO/o4.3W"HZ2Xy^G%tXo9~d#<b6|[s6[RybXe,h7gA35W4k6auef9:dE]o/Y+{">s
7>pm9:ONe?:=f6#$HK:%y>SB5-7+TWV)3mh|32t&*S.Pkvnz)!@SX<sD.;,TxPm-tysV1WP01(&}%7v)GPrM!@W6;A`!6kn{.[gSt&/I_y?dsJQHi@`IgtBy.N$F??waKKEXdiv9&`I?Pbj;gXX[b;B|@o.^[-me`Xcx!@](18bB,M0;WBK^.cBy4zyRQ
9Qh#,$@XJ0F&_yux:;whA42DrP-*bcyyqD%mY.Z;m`g&lVb6f-I}[sy=WI^E*LMX=58je`=NXaGxmz&qj6T~nH5CHt
g1=4,
HB0m"1/AcrY57J!#a^>93Dz;,]&ZP%ee5@(r5!DwGYRsuo}p{>LgA2]-6Itv)9qsLsrXF,y
;15W{au1*If>SWf#(y;;$.l=A<,I
t,Q$;I1_m/m#@X06jfn~K(,QeU6"at?&AJ_J@RWbp<s8n(QGFb1]MHB|gb/&T|#Po&0mSUS#DvP,;?<o&?o++9e&%uu3o{)`1d^`N&v5ug>7
v+FY]HC7OT6`uakm;/-LQ:y&;x@OHLX.s#MLOs]$!HcNW%-3%lI/k5}]hW#?OYX^BoMIWXo(/;K*,DX
OeU^.:UPGRxVS:<I

>@(o(r1N&';break;case'de':$d='.]^@qbP.!/e0
Y+$lG,/vgP:@d,I
a
D-<&1?GiJZ4!lxM#I~WgQ62;2z(olXguOT_*xf<gKoHJ;#y1
GlcQC@<WBJQxTxY@&rgB@^%Bsio+dPZpwNt
]?Yr6?yg$+mt44$iDt4py:"0OeBHsOOX2a(A2-xu!?]Z0X>V&_+*D11=)NPLr@~cST$"vj|EUnYm>o#VD0WXXb7kGRS^%SB7|]DGnt%G$U>Owh<sK%$>Mw^)DIIWVKIu7P*ax;is1?-^SpvFb(kh`i(QWT<i
1+3";$?7V*,BXqh`t8x`TXr<R=7xfgEVL*E`s4wPmj@&rj5DtB):g1`72@,Mxv^PXEXW`I)kk
rmw2x0cQbG*L<bOL+)<XvIY|1n8r@IfT=h>yW+hfvFOoSk*e[zv?n)nWGCb[1z+8uM3faeajU&/D,zwg9Z>
>cVIFbs&Nz3bhrB,]^vEUAip+4LEFu`!V:5$MaSi
pZ$1jm.)})<a86:#6-OH6Z:!
_J%(42qd>8Digwu&UFJqqO1K-IbM%^U/d1<+H[
7/n)".)Xd:@?AG@$%o{W`uq]@?bM/jP;nP/w!D(
|<94Q"9D05^=/k3oMgxR5ms.:
Km=`e_AQXUbjd%bS+dkhjIB<aAw?4sGeJB7hxeSss=[7KO17Abh^725CSJu:"r,e-Or]x*!l5ABLY!Zc
w]XL1D^S(AN~nYB#]IOQvvy}(=/GJ()>s68CRXhtXE=oZ(q>$3uX>$TNcpqWe,iF$?.@63Kt0Uu]*KwCqImW8"A:VBV
)*6x&s_7w-YB9gr$3H#)rIv+%`@7K|1=.7h;>Pv<N/TI/TyKDAmI&duz-F_:mRUa
uK97{u5bhyzmhL(Iv!tqi.G]+WT(|Z|II(-xMpj,gp{;B,giGkP)pf&bIPTwX$z&Gy&-H7Ph4!.XHl!E*pv6=/nQ0s~jCRdDL[%aZyIKSxS3[1:a{H
6W#4-ls7$uflB,<qE7Y~n?V:^O!Ad+;:[BUGr=T!#W,04EkZ+1L#bS:ZV~[D+5[$ou3+U9Sm3xB6"c6)JL36/E3"l}+C!Hg9;cM(6R[$G%P)grVhs"n!ZM,zoUqub*dJ^
S(kQ@F:S+3ud[[5!qt5a`JFu;?/JsG4tv~f}-Fn;:X9Nf52gns>i[e>VU:1TX/SQ"oT1Ov<x0k&[a>WFJdO7@v"lp1^3d_K7qthudwGimox2pai0.`NT.Q]}y"wkLOO$OZ+q=tmc4+Q/ywxxKAZ7@Pbo7_l!)ui*S:fi6dX+mZL4x^K1PG#rXHEyqCD`)WJP1|qB=c<qmrjo+S0ywSv)vE+8)X*ZD6<(QGq^[YMonh^Aj!q:ea>s9}+5k$HTB<w(_FkYBNs^)/g$l?)..qqCosA5r83G2(oMV"boM"p}Ze%UX<Kh6:[85cv`+6Y=Hwjs>lo>ZDxS6`)S:D:~%>E#bSE}a9bB2M*uLlYUGZ;+JCnPlam<^rr]7mL.IV$-rPL.H%fTS+2"ttXgVk+oB<*sxr>RCV?mRm)?-H%J*iyJ9-B~&q"O=9si4~#7dkE^:$2f_X4o8TWuYLgx77/Wf^3+j@`nCZ=*hY0;_=BtZ]6;-<)x`0<2B$[tmNETD1H2QC]Q#FqUygKFl%)bJXlrO17-y^YFe
?&$1gxpw.w.Xbb%h`D[PUj)2CoQf,Y=.E#`Vdt!:X_2MG-@.AdIr1iLJq}j~p5NV("#
&dAO&n&aGa8GKT#.7%+8Qfqw49=ib.)V/8l#MG5C$kXa.N4~)e.mf|#2V_Hdn~HTO2j,VDeUYI$*Li
xRr<V.11H62/i0]?)tX/.;g/UTk&^(`V)%{"#
RI)f(Gt-!vx,g@tjV"Hm2w:adthVsEebN7lq!]:LL
>CkXEXYN%nKc?9*b$LC/~va`-jECFjQBQ?&`zKVX.BjNb)Q![<_)!14@vFHT<!XR8u>luRAHX=bc#V6aJWgQ,Q0-GU+]:J9Jz[=fsL~5Bv>
<s1pzZesoJF
Ux<]&NY,U,e+5:X2|vLT(%/*)!~RKUaye/#eKWYfGaHfT:19("}81"1"]+}h7
}i9FQaa1)o<F6x.:KQO&Zy[;;/vZ,#i#02<^K&z2z@XSs$+MHIxP03LEk?m
[jEn?kV#i(18Q7sczFcuFl@,gg>LAvRMnyMU/Qo^k=elz#,>X%PJ%.f[h%NZ/`41/bYR6*+RAsVlD:1KM(^V3[VN,fpX,La"C60Fk@oOE$j0.-i/yWU%z""M)F/-[HoPTM"KtgjQ31N0G
7FCj5!g*^86eqDu_##I@PEv?#%`O:n8HE=XJ1U=hJK/SrPU:bSWkYDUA}#UwZD=@K/MF=;5$u@78.<59gY]5aV_o%m:yW3Md%I~k`CIw&@_jX9*awYF2*ST%p)-=%R;`QAq`}_F0b(;BU2rCW
O4#@7P+]!2j`?TJ`p;.`XYhQ<8z&L9qjjU|]LESY%Emaze>FF1q,y7c5#aN#9=.pJZ3)A<L-H/Nw/G.2EvufK]Wpy4pg3Bt@^2V)JEWtwXY?HP@V/C8(&5[&+wl0?rN_LsA$J;PCOaCWGTbm5e8q%Au!Yd)_i)N#W$Q7K)4a5>[-sR:BF-HM!Bs&1mtx|r,%*,CK-kTc.^RI3M!@`tlfx173P[T_tIFoanGq7eIy%cT#&MMU7E`PBJUlP,~m&=)<Ir&EOCJac3$QDWI5,V?&3Re%,`g0)W8P"HMGa=q<Olo[#1!7V6FHIcErxBlsIL~X7h,I|]W*m9=q3h!xa8/2&`37&uJ3Ts8hTM|LzO/.K=+75bR
<Qk;T<:8g-fI6h>_e+B$v:e3Efn43l-r[w)Pm@sT7sRrH4yeNv2>MRn2h@.3C_Q[e4wT~Aaka:8S^:}Z<J{2lYS[BU(=V22DkPCE!w6;*-&uE(kWrK)dEYVTDZf4tjE)hmT8:3Oi[AY$KjC+`Rg(1!GQvc/g;wvK{m$(p.PG1TI#dO},a-(Z#7ER-V+P-=/&$D+Y>QH!Wp~80+~]6v"8`hBH5H|&z@Y8?l`jD"Xby$xZSi2kk"<3;/pv0G!:iF~k{k>PpR&GSpdYM`#YTn@iaE?&aGpmN6a
m
R4!p<ZKctV;>@ShNm3{qHf?AR8S$:<N#nE9`jsO%s5M:&<2dYFjL8C!
Lj~QTTZ^7(R[J^<#&!&6_U]TcU=G-e;BLnV(@-=[m=;"@4ikAK?ifQMg%%{T$DC3}YcSS1.*4Jw!BELqw*R)LB#_7cXMTZ0N947!M8~"1Vpk,,pgEse=1PIVcl6uk1&,Gs$H[y_lBE-7/?pz%b,U!v%lDy0&i!U]ZJn:RhTOOfAgzqQt$j=xKu6FVy?mj>9]vXiaBb-EiJ?3AF,R}7oUWx,d>cyFQeB^G`_Hckuxt@].@*zn<f2<^!w@^uDk/QC8
>"+Nb/L(@wNJGW2-)3jXb$;m(GL{3Vb8?xU$8FIp99Uwe3ULr[mQcOUy(~9#vWv[%c?tu<rNMc<9.},Six]$f%;USlsGT|yInKjF>W]mE;sJT=Bx@O6V@@-R*up3LM3l)}!4LC,_[tcpe&kA36ID:
2I;5*-K:&el3-v!HquoE:)dAx
V^0*q;GJM9?3JAw5[q
h-e4:RPUCmB1IleS]1l?)=|(++)[?u39cr7;EU.Yz[Xo8&bFtL,q@
q(hhy<%!Of%Q<l]x(ccpoQfDsG_jrXGcAc}MFbUnd?
8=VFdrZ*G85~4gbunI*OynZ9_}^W4u`6dFmCK+`kx^5f@yv[YTe@fL=ROBik:3Z.lIDu`2t>9B1hD~s~3nnV+IMvw%;%,{0"K{nOAOGAfLlBTpMi!XfC^6vo`2/*01a
bUP%Ru7>=$d8NG7L6XxU2`!zODS<^fuYQ!)qTH&@l50<"lEuH8nq2o=2?!w"N:7e!/o]vW8KsR,Qd@Has@8v`N3i/%=ODVoNy<a?iD#^s_N*Jn$!B&;eB".{sq[c#nSO2+:DW8/A
>fTVB/,Jy"5qLchSIIwA^^|v3N-v|*xrf(e6HuZM%^
mcxI4,hs^dPkZ>f]$#y;fjjED:`E65q|e.nz"#Wr^e/Prdirv[5&"(M~Tw8O3p?g#|(t*(HDJ,46B/^O5nN-KlInfDndLjC3yBd[ig=PcrM9wx2i;JPLq"cLw!O2baGvcb]6cYlBH[LPwtq#$cyE7{VN"%^mu1yY.fa=0L
Vx<E%&};,KOY_N$k}^D(@s~xF8[n<E|6|8/%S`FOlMVnd[rGr#2M6g>2Zk{Fi>w4QV}J^l2<6aVr`?feoG8C|BihP
Lo%X=+`fP2##aPTbuj+.#+d[k-&!*PTR<@q5-Lz(2!Bn`pkypKrn,I.h[B#L2a
?z]ijEDaw:IQ`H_xAj[]x1NPjem7[`#Jra@QO~HRJs==[Yrzl8I^nGR)L|x_q},&eJVV559ho8P~R*BujK3-`e@!&8&-UmLk$@X30ikM
wWo8S.YLsKW,ya`@^N:>|$/+:2h]o>nY502/ACz5T^(I{pmZ#eiZ!M$VLy3qxWoelKQmb?CGryY:Ev+8D!HH0iV*3])W8bn"h$W8yG|S<!QM~c,5@IFtbRa1uGwLPi/t%:_bM<[>WBu_zSC;|JSH!1RKD@vu:b6nv?_1Ba2)!)v&G`^TXG-!FbklmG54?Y!-5<R6[BK*4D&q["5I,sG:k
CxwAeeK*RN5m6](IgdKCR]oB])ju9UWb??43mZysH*Oj(6pOs"{K.ngU`(u3V0YcuM:pTUq;e%7L<@ucD%L*Ns]tHJ(p&
*<kM[.kWMhm@O?8;%mCwtw]r#oK^mdznU^k3y[pyzv}N&';break;case'el':$d='.h_GfcsD),|?z""*grm;,gM6U<T"=Ix"AO#kDJAlSulGeNIR^d2<4=uNBVb9IpNt0`X._f^"frJBKf
HfI^@3pK"@uzr7n-41l|[!7S<{8/)G.9M<bUTnnTbxHE^DJPq(a~Rwd]*SO%XYx{H=IJmzRB,|q}O0`)MtJ(a)*MiU!c*}MF<.SVx#3Ci7a2$Sm2=][BgKnY4WQ9W<y&/,LhPhW;7%2P7}&/Lkpcck20,um}S%HF7<tT_HtlHPr1=9yTPx+}x1l-3Q.ndQifu~=b$j=NK`,zyihpDAe0t~bScHu:Zq``8:nGf03yuhl#6}-_6U#$SB%<Rm=""ETX+TF0a5E?Q41fKystg5w_`q6}B1+Tu
9`9*:,9$AS``5t+
_IjI69_Zb[#)g9qZ46!<@,8acl8#%aUwfgI$M:=6bKGE`jn}H!^<%UGzRqHph9W0l)l;rDaV)DO|L>bHS2^N$)MD,dI&?/iBt>&U<O(D7VR6=qK<+aB[;.r2fM>t>lph,Vl%[kuOAfMAOl,Gnp/pDuaho"^+Z;;JxbO|>|V[[vt99K9FpTx2tV-Jf~ffVZDBF"TlSV1f+*dm%Sy{PHI[20Wa2?4jnd&y*(8xEq^"O1uw=9FSP,;nQwwG#xcL[k5
o"_Vq_L!Mk)u2*m(`t>?s&"4t;5i<CdY;)l0@wk9)HuO71[4Teb*_>J*q*_<h_WC=YT92aTMLg3Y8~uQbiZR`F
B?3hpv,kxt2`E_MnJ#0C3hp&ip?=MSgoUhXK9;mH@3>?m,}s0/flM)7+qolGL4JPKa$rV50kdXDj?__<:#b
sP5FW5S[Sa|:,w$okRL3[3.LAO.;Qvfe`E9?D"n[4YrD{.|ytlmcdJfFUT<l=)9;!:n[bq5+@nQLs$=KJ_/z)Q0$EYR2#u#3Jg
Yyq7CzlX>zWaPDwtO9XEsYk?&J^.D5uTYLtYctdQwu<8WgOgG")6OQ`Y@J)yjK`JM)C$M_[NEIAv@w_pHB+d^9wec_AG?[tlcEp_*Bxt1t=Zq)h~>J?b0eR^MFt<gVi`EMf$L,NG#@[QmR5jVyZadhLsNxG+8Soa.~=%0$+UDJyIO[hJ7y_C9#]gD4uUsh<ZxELFo<[",B=Q*}>ET4[!O^B)!]#tFdp>&>*xqBl1[c1f?MT2PQyR8;nU^u,`s$b*$r_($UQqx#h5u)fdqX-jN%Du3nxYnwH"B6sR`dNyN@=9@m3hn]EK_(DTl6q!Mb
;;pe~K-8.p%`d@<p~Fl>[t+6%2b`[,537&<lD*OS35Mj^ppLl`R&75haen,ByDMe_Odpn5]*_4H_=t.2enJfR
g,=*@;_5Gc{2kd<Wo
|b|;1B#-
C*2u,2sFR"<~Mn537rfD,KN|;_p-EQN!Rz([9{9QkNV]fu<^@Zi*ocL>rUSrnb`bvMP{:v]uSsJ(i&9:Yvo-W=k~JY<k1oN%RHEgfA$eWw)Glhg_r!1>$SK;.7GkfB^5Ye"hI{sHM;`MV45(hv!/f,6J*R8y)MhNN+_90Ox%"1+%,+x!a)]pSQd[7qPik3sedJ,g
).h6cmMq-R#E>mI)+#<d{Z;({;c?`NVSqjT`f[VK/D-y;PkGy<}RgRUTug?s/rDZ[mxIn_vj<s&u*D7fj]&L_G2ZHcKQvN8H/S[%;Q%iM>oOx%
:OT.i~=ZC=5w(rY{R@5/>RbKmkm94E[x&5=YeN?aVI&

~quwDhWAA,q1Iy;#fe__GQ!R!9JVI5i4$M>SPcgW]gZ"1TT*Rm&(NHB/i(!/Ngy?5:FRAo2->AqJIET7GEi`bACZw.gmvr<.u_rsx_sqfsaKz0JRFUASk(h1e2)>A)AmoW6gM``^Bsu5ls|&ix~Z`yQb~
4%CVinjncivibu:i.NU^@</B>xFtpqaX-iC4{OBnMteyF]$VxyW9IZKw&*fi-Qlrp:5`f!vh/?[7FQCv9eAgQ]0a}o#Y@"H-5c%(4plYeG]D)Q}:`<T*`+u+@HX-9A#8k"^9b@^V9S*NNY$C&x#Zg+N@_wQg&k6<9Hf0fc5p{)(s-V_u8s="AT|.FaV^,7/SS9ML?/a%3R}/yr.xS2|VzI{n~NRour0_2$]]A@]pEmuTb73w#c!70"sb.id(
4aPWTD!MZ$OD)}DvT;/O=3,|p//0vrovxt?7y7[<B!m"bWLeNSCOwCuD*e<4?t;Ar?x%e?-q)/c#tHjT
Qgt.7[Oht@Qe(vG:}d]h48/dJkSV~DYab`7WugUMU8#Q7
Gi>%3H&V|#g?L4:BY5;*^!IHYH>%4EfNZDxCJ4YDq:z2f0.$[w!lahkg=7BWu@VV4O}2mAB"Igsak2;%BKB#af8QxU-6yog:>e0LJjivRy*b-$i=J#&$|6|M*DmId43?no:Gj6-"W3jTZv`Ye4S3yeEq++kDq?r,@<JH5lw!$mz/uACBt"srgIYW3j[<]:z/8$=UM3Gc)@$aH#P?9qV%B)Vwh:W%6PJD|rxI8KzZP]Mq;ZRb-*8@CV>hJt[!NZLFz2d34`#Bf6bp);jao>WO3SefDS}?JE5*c6a"::FUs"d
]_^^lo[@W/te[FPmkDueFjEU:"w2lO4d
Q]@M!VV>34NDV+CNfd;W6f5s_e:P+Y$og(h!sH7aiM(f/$L:@su(vaxdNN4B^y"fO!JZ"_kzQ;oBfW1ul:&w#ixRdmK}1i"isr>)$%mW++k^m"&#3kG%w+Z-`dNPU!c{/D<;Ye]JQP4Y6n$09=V@3eBBZvWLMKC2PJ&aOyu1)Vi0q@o+kb#iQhOUt0>I-%vf6I3=3HPU9w^v`$BrCqUrO@WE/nC2:C0VAWU^.I51@Fp8xZtxc0Ptn`ed?J&xj:={Ign]dovCOBkdtZNuYYFnF]k
YWs|)|q9@rC{A<s]8nxTu}w!Lef1`m9>R
/E2L?
_SC0uhUt.l2F({uIexJMEkrZ;mh
g~9_cUUg$_<e`2R*k"XJDzFuFhiBCCOCovBcEQwJL"n49u<)^/!U$rt154K5m3Jz3p$Hp|hVwZ7nn|MHj^X}BzU#/f@D)+V>Dt^VKP=}PymS+Bo=gR:Ce(7sk{UHMlWt58al/pNB"`4?_4)*QERkJlpKgLE4/:DY4[F5po?=+kaG^GO]Ql-lii+AF{L~3]Bq)fK
o0rtE9!=Ovl;fHC<WPXetQluK[KHMM1p;B
?]E$0>#"C1I*>B_fDfz=:#[/5
0@%_!TD
V.6E;a=u5jZ$dR=t7YUJ:SA<(,#0;NAHH`xN~G|QNaJE_*^&^k13Q+$9V3,^}oYle#kiYj1D54N[HW"=^8tT{lNNGgx:|5>Tkz(e#a(Etebr&u4?
ri?A`xHt,t^N:.*RXFw:#K5YyOV}ZHLX]dL6jp$?b/OekHS8mqt&k&PEbh`auahlwl4$@,G.W"a=a,UU
C#pVyj~
P,COy=dgA(*4dH7
_mokM"zfWCebC
0-w-#knS;[:UFu|]blwV2=,JA,4d|7k2l%Y65Zet[1cyHd,B2fNe`ZI]
;`f{PX9ph&cwi<*yZ$Ac$?_nmIkta>coRU:jEArbWYD?h=[B9W2xi,"@4{]$QO8m,DGOX.:ud=8:UfSVk%8&%bcE=d>1AvtB$Ttlhn%;d{xpExLz"(D*E74&H^I0&FapmM[16mQ*djN<J
IFk"+cPx55RfPL*H4E
:]+,W.8F;m=r^A]gAok/)kVE`:$`i:qKs#(.LKuC82lJ:<_52ePiJ]1:0+.eFjl#;Zp!{qGoGHcaiX"#0=i%oG!yNXyhKaOc`!n_T#*S!XgJ(gW
I"O/f$-r}=I1]HBE|K
n##*e@6$UjjNaI?^:o:w8Eb{]fSg%RVmNxM}kl1&(DU@WDpVfoEp5d,8PG@t1
U#H!;#PAX=Eu&/@QuEgKh4/n"6WLfVHD3iHjmt;wq9GvnP0|;a+|t%.ubXJ:IX`2q;0a7:V:o?[SlJ*#6OqcnW16uZls%;wU$zx&tNQl"K$T+,trlq?.0K.CB$_:4FI/,:nL%<yn
Drx2pUET&gF0k$jh/VaDX!`p/gX$0n(JRM,4zyjZ!wMDXl*H8-[PdQ<CGp.4bXZ6B0tKNvwg0+YQ:F&kE`LWoNqHJ]i<TIIl^Ii9O)b5C0fea2+4P4Wqj@vU<Oss#])aF9_v%Y[#~Dr&FSBC,G!bOX0HP;Ojli|*pEKL.A__<N$aLAE0n5E1R!Jca,m5yhlUca5:D6o:2-7Ym4f/,F6kG0M*hV?(^w~XH:J<*9RJu_P`1;gdp(NX89@xFB7,A&iw+d_0"y4"u@wUYGO#v==4waUW3Q8@b
~hl?RQ0O9(&xA!yUM1S9*BjEzG^^USlW#tdN7N6i(
;wt`mPob=.M,)<TjSe
:QPDkt:*;|E~9^-UE?^zUoIEw,V(33;wxnfR4R>;/L>7?w_B+?eXP^H|L0`J&-i<oo=-RNP]5)HYYd
H`UAKm!0V)5JXdU]<-cFbB2`/nFp#=@k-XrlVFW>z[A5Zp7m~T*oyX`k(_]yx&]sSs`2-y*aT)@?[=yoiJ45>l/kCqU/Av6x)=%G%)_7G[btED&6F6)TRZ9YVf"I+3:tcEFv|P0N6-.^N-u/uwoVPi@cS8[K&I4;ab(^:q+-jUEZ%h1"6dy=qyR]!pN:ZFcy~y=jv1QJ_Vss<w[4rww$v"sKYt1;7Vpy2k%p!#?*vg8[~xr<I?/_dHKrnfK
;1
vfE_ucb{)]S&L}6rjt
yS@Ftel457RM&Ib6/pQaYvL!/g|^;0JAt_sKaKd
+3o03g[O+X;DHUv!TdYi
Jo:;`Y>6,`x-3(HyPn](sZxP?Sp{"OahjICQJ7>R?>JS5g+XwTjpZTcRv6LD@N`{lZ5/D%Q85J@Sge0>uWO4cX*9`zDo
"A1cZcYX%r|02Al_Z
~/E`graXY@S/S"w8g8vmQ1,=3Jot=?*ql5HFWm%0oO55PxQ7Rn3pxBtF;<|7g+o.d%r<sUZB;/)%tq&%ExXTHs2-rPhd4:nU9&VP~spV<;LXC?wjY!yjruj5>DR4Ue5mfhWwu:-Pbd{8xXR>WM.MmL)q,a]/Z?=2$<ac<saQ0(P)O3MYzj@QPe&AqCzr~qnjA7pkT`/fXk<FGNa52fIY1VZ@.($:rd<-wp;Y[*FPE/zYW_hpD?=K_#oBLZTRHF2f-R*
1v$>ahU>,y:rx8p+pe9$5!i-U$v&E<QnyZSY|L$LWnC<8<u$Kx;Sa,d4[l<hTh94sGM8xcp#&]H;-?wB9H;v)]-qms2@Sq]sN!BojEbn+2lo{HJ3I*i*SI{%?^#ww
TrQv{p^YfXKQ<o!hvkl8[IA2_C:%4Dqe_E?7BwG.B;hYaG
Ue]MBZ-7[.`!)jN95z*!$|+8Z>j)gTLWp&d:cD7IC60w(>@09{o}jC!gSrtFj|oGuV^)CpU}l6I43NuMh%gssR_zi`;wiw-l&>i3$Y>od1R?.=@KN2qhPE"#(guj;`W*9X=*Qo$J3rxrd4DR&8R!N.2`v(1XCD*YBMUASeo?@xar,7<@!f-yUp8K)b)XQU
KyRsUnqx9?/wkj4
i9^f:0~m8AK>*)-?^^=#c;5vc"n[:8O?fuSI~g)9r6:`5f)e5i0X#Vh^G?AD;ls?C93H1UVMnv4XVU@L3JcAAUc/k*"s:*"U7NdNnGsOhrDZ21XLlgdVz*<qhNOmyB]kP@zY?[h_6olNS1+=W._,,8~jrf__ee9)is3PbrJlK:o$}Ta&F%*w.V>rY
;L)8
!2yFF}7Q0lIta#;6bG^Ve[8.B5t|#oxSR9aJk33zsW
>4rEzp<szdIJMfVLzJx^$z%3ch?,l1~QSl0!IH9EI0.tkc<AZ>&YEcncjgl5Dx31hn}a.N&';break;case'en':$d='!X/+JaMAp,z0
Y+"&I/g5m.fI,3%v3SjRAY<Y*A"F^t[zl|3FK3ITT_`+CwcieIAvpVN}ELmtOK14S8C;@aW<UE%^x#5<`DHu0YxoNCjY,OLWTZIUX3S(KQGmlzncGVJ=a`D?kfPPG
Wb4Eh
e%,_5^rp^w]f`Y[WM9x.4n
c$dh#+ouPn`U51Z+U!~5pF}G_RYGCS*pt:+J{J/?gPa]hIT">=6/~x4n3Uvlsz%5F0%Chm[uY7xR,cpQCm;K5gGI&,Kn%k$mqQ~mZYTutKRa;USj
dv&kpQfA]k7C&[t
N?B5?ogy@}RQrf^o^21Ol6P
e$X[9/B/X)oZ]klU4`/BuS9}?bG/@{phbBl(-M]ZU]Pcx$FTtEQC<kj`+_#`xPu~QXg%r,C)$OfA?UQyp&?:]+Lh<sa]^hlkAeJ:L]K$J"[dIxv/)SmcP1`?X5
D"UA8!$7B`>QELz`7in^XVLHT3-OhulA+BZ<V;MrtoAgnhq5!,"G^YBiI"n0)Gq`FR`suJ"LOXo*}AVP969Bk8hXyyF-_t8S|b{4Prm5{:EpeEu4z[XCl>vJGm5:|GKV|?~twM(bWFPv&Hi+xcI2cA05aTqA:/6W`S0bVgLA)q.s0s;W3$6V]7v*dk99Q:ZH:w+)>7i/>DCy%;u]FqI:Fj?(duTF&Kjg7AJiz<t%fVvP%n&8}dBEhcMIiT<`_kILWw36yX0Mv#A%HcQ:qi|,FJ+J}TLl,t/<f?;b%@(
)XId{KS@o[X=0go%v`1_#&%W?W+e*>a"2yaOb=V^1^`Jt_J<KV@c1x36sCsy
i]U$OeB
FA8JHSA,4}@3KJ0>D8NC-la"hI`x^Ch4?XtwJvA+V#sZeB:Br_g7*l2Xr9Ifv;D9LRe7LLw@Y#h+SP?/l7c#&=8Jy,!4mL.cRjZH"D,^I,
m@V?NZx)r+=:/!b)L+M6;^`%!(>Pyf-Z
&tcQ]=a:<n"inN$nSxuSqeo0K=$w^8)tmKk3Lfe"#ourIlc>byqUF<Dw]zp<g=GuNpN!#B&.Jc*]C{nC47n*vY?
]f6z@?/@j6
[_c6[?&y_)m56)!.Tn2N-Yru`BG=b={/f:Df`4<_[N&k:uArNUIq@>4s,Lj
#/Q+Qsc"G]{^"gasfE4ofV}Nq-)Fi?Yww5!p7hd-=EBw>t6tWpA:ay|qFtOufgQ#mQ8Lw[mM)#+<yW^yHa,,a.~Jud1SA"?q
_IMBt_l%beK-@;c!U,57jWH5&O,N<tYjY*ZeHa>~!,_|={foWs12oikcJ6:&z)X3MI$pk`E{Kq%RsZIai_1/48n6^kgjO."sp4>|`0=}"C$,mhd@c,1D`;I&@Oi"3i>%de0{&JIO=475^n[)uonmcbd73Hf]6eE@LtOJr"ZrVjL|:=t[pMvbW_n^9yKcx?fNo@Pfv8QE[tpWO67^PRu4tTTPUSC3Oda2+-JH$]<R=EgiLWBNHTTmEdG@E7fQVgOlCC[O6|0sh~d+f_L#KYnrV1f/+gDTr;@?b)A#2EL|0Vihks5F/2gMv<>5u[orjGb58fdFb3uo(5ic^@O
j&:t%8.t!egE9]75cq9E$`g<xdp1g/@2aS7.WfGkodFW
*g>GGlQ+h-AOYy/u},ad|l}=?d2I9N)dn5l16s)%v]uhyNr-l
uY"B=Y%V0hRb>G!8q"1Cb#ii$jrP!`t%h9!hvZV=j_rgLsj.h#`WAGrg0
;ns6/drWFn$$Qw<O|bea1$Y$fvA=L4/>SM7Gig
3+s&;;LGw3sv.9gF,pZ6!R]<*G&E5sFh5%]xbx>|mxRTqJkr
&CQ!Rbtf_9r[|lqx
_:MXsNS,+y6(20VM)B(5^9M}xL>VT|6J!tPO=9YwR-D~a(%-p&RA>=B/bj0][~[f<zby<9"b"a*eU:m:K9A,HKZX%}o8b-KvDnHUZ&8h!]U2#LeXn*4Pv+gu`WJZ:quEdM$vHPXo7g?86x(Z]{*R[$$p?e@K<u?Q"=Wu,"8|A(Cs#~o/xQG#,?;.x{,;Vqd?Cn_C&v]"CqeZXgt/+?wfLN/.U~w;N
xU7K,{#Om5vO1`AzGz86F=<h`-+.1.?4J>7abefE(Z<|Gs3/;/?:!9`E%;-
1w5!aj]<&o5U
dlavnHw<f<@-`KC"SK(1`Q;#nA![2aQuKSY6TRZS!x`4o0~WiYj3=
d9GJY6q,m#&_.kE&XtTtq=wDpXbI=-xh1H^eq@Z5c
Xt8<CqH3^J-yaqUCfPe8kIH+r34To^K:}YQB&RCmBYiBSF_cJkw$)E?bU).BB5RwVI:fYy:uR>s
:/DLTO%l-RBVv2C
Tf[E[+.M@=wZ>fxL"i!t|/}H_T-.Cnz5
peg5pR1X1YGsBKdaHO1fRm:d6pizU-V8nPFYN[5jenoy95xx+__Dw(3r
5Os!U[OAckL@#i*8;
pmih!hOk8lCwUGO6a)2$@g4a.LpQ!eE3T-%fJuM%o.ZqKc!/fjB1yM-@?f<EJul!-s9s?c$<TPjfi@1d=7*FTpSr:aQ,T_WB3No89.R9MR}77IG%AJM*]V,dkEgQkdiZ&^#%F",^~JGnRF%Ip_1lbPu?w7!97!5B8O"6mM@5LF1^snfU^s_d|MKj|T0L?G&%[mHk"9*dM
9mI#}M5lDIJX;0$[)p~<HU%aRYyyOb
?|i4pmO*&53{F[7/a[Psb*
`s((R?"&s"7I:tCMb7P7i1;ToY{1IXM%UFL/UWGQWcb;/Ci(z>[:+OD_7,nN1,kV>l[P4v#jDAiE
^dDZ#;,&ou5$%!qsGhXr7x=Q7*CI6;gQ7/w>HoPUw.#
EbF>wK7;pPiLP^wdJvj}q
cP[ng}Ue(gkDe=8Rqa1l:>vQFLpe)LBL>sy<:S+l/
GDTVTRr[^U/mSPN[nzR;"V?sstSJNhc{V*=#f"#`Q&Hx]uG@7R52vBEp
B+jEz&)UJr_:YM3K|O~/%98)siLiRsOy1<EZ:w<-~B$a%uiQu>)alhGDqSQ
^5Y$:H}W$sAFzE9QFI{Bt
2wYP?t|oK?g8r&-5&;8=w,A!Pn-viTdF]/?wM=?h5w212aO#4jB@4vza7;)6`G;TPU%SAg/[WUUn5SclnGzIf[~q*w~U:$U-?UHfpMixwTnLrQ?v2K~5pi;=oIEJD)e3C/Tt8R"q-cPSjv6
HB?egxX49Rgs/`Q4g/9cRE>hphQ$H^;a
Kf0ko7%*EL0[uTc]S3@]ur7(v6$fs2<8/q$.Uhrz2BQd"w]CX;Xlw@%k';break;case'es':$d='&`G@qbPDI*70mN)${e]2qTv&W`WT$^?^vmT9zs}A/uVDQG]bFiU<M_/Pn#$-Dxe1o,^dk__h$%=HFDe/S
tmIS`#N-c2(w/^B^+macZIotgEKd`6e`U@nUCm=UC`fV(^Pv{+`K]Yr="vqsbV#UPCOK*M_uE/AQ#p6vH+;+<0Tw*fHUDjObW`HFd[7sj`(;X
u>p4cw0W(wrYHs/D}_l4F=|`2>Z[VUR5Ao&yaM<!~AWr%TTM6dzf,3Bvhn-
D73jWjSlrt=C))rV:2Unp<%]k
Ix?bP&{jgX1,wV5z%MkHr7dRYL;++xv](mbLOJESRdRLIH_o~&;n50ctMsdcq=K@<vQ:#[),.tBgX91`P6$.Vmh8qQu3/6pE~2M:7iL1|hjJDMJ/o[Bt)Ur1Jp%wZq:]8:/Rjh}/!D$VtWh(|P+]7!w@kcMk+Q{;-[Lcwu@`><^_(KNIV?f4DRe5b,zxVgk0%5`Kp4"I^O<:QRXwgCoh0Vtt<Q03d1hfr"0`chq,+;8UXFvl&,~9dOV9pZrJ#eYp_)1P>rUTT<`UTW?06?$HJXysh/?D?m]2Puw1OH*si1u?-O/J6sPiN@<0G-;gcVP#]82i~T&(YQ,<,>Jj|`x>}1`:FF9
xA^QNg9>kVd"xhH1bav,Rl""HE98pv!1Bm2vEd~lY`VTKlNo,ik[hiPTv@]jsO$
5ku5
46ha>Ae2HQ#t%|!<$;8;/d;y`E1LD7FlrZBus5JydcuLNDkBny6vV.[~wZ]F?6Cy$ZI-;i(F7#pl5jUwU|`C4OQ5<
1
.q&9:s?RNKB!R0WY]3EPEruVjf^g6)=Y=@UDizB?!uvI9A:O@Hjiy(Xv.Et@<G^cx#O7,53_C,SHdFo
ikacS/w=yyU6pq3oFn:cFFaUER
=m`LN(V[RsmH?m"rbMa!razOBAG$kW_<@c)!0iC/2*C*.29G0-s>
m]=FoLJ$<WQ1WO+Eq5$$")B)_>(WiGjzbaYM1+ght>=|Ysg>3"p$.Gou$Tf/XwadJWa(lhTT1WcPECC,1zG3a~wJ@kiSU47xef,0_w!dhqmnJs
2)lWJUtLxgbRME:1ED
iSlj0|@Sh8Wz
:EwMKYlRvUtc+Z|K;&N$IQJE.z#Q*.xDUj9=R>7A#Z4Q^Osf4Ywd
5YL{K]1|y-Sh@c/OB=rj@?kRWrUD_.xnfJOUeROh(e${V)MH@3JG_`I`=-fpW`akd>fu:hBvK|&zD);?cmVX
cdze#w3LU4CcP>$X*D@q5;prRpx?hVz[6`TRD&Wh0+htz,fMj1[^f@IS$AE<SV<Q3E2xBx;qT8Mp22~q@Cr(:6)JWODJc*cdF!f)-9c;Xci>9ZUB(M]6tZ<7M[s%p"cH-.PlzP::/):.h5`;KrWsN-aj2.d!Aic/D-;$D5<o%puC!y_2udVkD&p"q/1,?[~q-cwWJo6p8@g,(Se4}`kR[6%l%ogcT^.,qwzq(U42nclqe?^&*p"BLwYX,S<3Xj?-ax]wVfBz#5+(xSS@]q-ndM7Fu>sV=22@]O[8O&GB4U[9-;>Kr6ZS=EYLAY
*c`n]5,Hg:>Bu$QzZHc,M
0MH;l@f/1Gt#RVK;UXa[t=$M%=F&h>oG.hoRCRX+V#U6g~1}LZ_VQq7%
nlS_HcuIBXV<D6Bum6gP~cvX
qE!=cYFq,M&BX%=f3v04#SAI)_
7uS]|*5pA5^,;AG8zU6E95R"5p@dmN0,}pIu6]fyoNmb/:w,/2kg]`Rbog>ENQ6;7gk.Y+?"SpM3k-8,Hh((td
GDh+2Z"SAwfMpQY@C7NGO>hgrnQ_)rvJH$0ma+whKom17l!"/4&>YaO{2[wl`uHb:Z&DgU,ocT8#p";u+{DW"8F2XW.;V*t,=
=-T5-i"Shh,eyGv@fyA)sM^;78BLZ#BD0Z?Tc{u{N?Y0%Sd)u66?t.!x:bn^SUw{gS(E`{o?M;$2]8G<b.FYg;=cjFHVL85a+a<yt>OnU~8C?"N&
LjmYWhcStF1!JOccW86UyC2FKZ5SAuzIs(OOy:wRrr4RkJOHG4Br=ur2Mr9TjV;rH!*9J)iOF^VOW1fAByk%&&$1+*(u;c5&xci_l0|,vfc+A;w0GA05h*SmH8G19SYSaPM3"?~EmPlcx
|(mUjKzn}d*fv$S7>w@D(5LN78s/8]h($dIfesp
3-0yD
4^~_r6bYa`vV!O>,XW5/AjrHVq`Tm8D>4Z%QlG{#>H3&Irn?%d?,jL/YZO%Z-DSY/3g=?8~I-o&2MjBtGV@69#nca`0;:]~P^Zs;O7z/RAu`(SZ>CIgs?!h<dXO+G5)a=E5<h0CC#0<Q3;;+SFmxq+rhxHUG(GC[@nYBaL[2kk67=o^>QUI8?_0SnxBQM*&&nj>^9+8e!:dNu`nR<Q.jIf-4U;]luWE6sb<3Qm6i`DkIi%4+
f!0E@suB=-EJ_b1NOS+%i|q@vKb092U{&OF|=L..i`lC=:tZoTYT*Ji.oqR$cT@(r=6;9wH;qY_8vpm;EHJN]Op(U(-t7im5^1qq"C"e:ie:
.A}bi=P%iM2G<.$`8k9Nh:YQVGOJJ<{CyDDVmCRa&$|44N:t}U_a~+Dy-#kCT?p>jaV<I
eyvP2!gA%811EhNSx2OkB[Yg+i:o"rb8cV3cD`I]x:U#S(|`+m&QZlhAnd%6O)O^{!_VT*X!C"/9?u_o]PbDd>(i#DE>iqlwSI}HI5]bGv<?/e^@&0xwXJQu@=ayN;#@jj-kmy*KV)piE)[6>_K7K_R%VNowiPm#6Z.n0Pyc+0c_PM;&sFgUyp
>=bBI]"3%)@v({cw)F&XrYoQ0).F4[a@"LBPaGT:`==uR#O!Z/h;A]y4;_debOKc
3qD;8Jzid(WeAy{&BQZZ-:jdq;-9zY[RNJWP,P{2O
tC8SQNAE+w9@"us
^TSsHxol#*4?sJ{c]MuT9A3+;je#6r{/e>vSre)
k=2-+=7TRgY;s,3oE82di[y)?WWYrMY
ARM;,5EY=v][/=z!8TN#!4))Fkm8&.K5;2@(}?u5]QX;:-uZL8T&Sc;*Rv@JY`m;Q2zdS;Q)L=S_HvU9!aWL1_@=ll*h&cZjWno2oV;m*@=WA,iB)XbZOYFV(7yYrNdE6ju,g(`1=x)Fc8|f:E;D94{pHmIK~a$sO.v?&1i4L*UqnEXbRd#I|;a=uw@kln@(FKE0#K+(b596QA5;,8fITX(7)^49Gd6panzG.MJQLB1YbH#9Ow=V{!^*hvV2ZuTI"mFk*1&2"8^oi:jf}F0:+sl/5T|,LLpr$bj8ml*uuqug-Z-P[lb0@);7t02._*>
{+2+}qa_Q>Q8)Z4.l`XC]K?%Eq~0S@Dd8P&tWkh)6(Ijl+cm*BP9Hg}]l-6BnHtk_OsD.8^Fmc^:WZOjl(No
a/S<"`/,b7=Ks*j*_qPe#{,2/gO2rwkGC!b`viOy,VU$R|^N@[".6/CP-r`0U_c}i!=nMV:_#k.@7|H,DANlCOQ&,PvM9&7-A/]aF}0:LuVNtnS`uG+*"I
+2Q;Uf`)<.g1&6#__Q<f01z5P_1U._Uf>NwM+ytn;[nOb^7)(pgT3kGNJRe(:[=+gVOpbjAqKwHu]-H&W5_

@5/"N#hX9l!`6A+sI;PA!YVSj^1`aV9Y85[!9kQWW)@~Ta(1=k3^3!wwI?[pNo"=#L+k%GLXqrxF":a2oA2
d2$)fM58PBo
&kSeeiK#:AL)Ss+,a~gRLaui5Vlg+5en`~IB3t;>l-NB6m^jyz;;j7:d6"V/j$I$996lO-19"zelRdSV0.OS*<GP=JH2/u;]1xhvK)(nLY:}u;M^m5C;3"Sg%PSVOx!JecAoSjftJi^_XQxr(+,0Pyjtmz1~LkT6f0d%SWr^dPo|E[1v*Op=C0kd%{;;`M
*A<u*Hb73q.*5HL9|"1l#iMmW>tw>DK5QwO27cu+g
Z.#,`v8a1y2,/#&NY+}K]6h#^VX6okmw.Em1lI^a2$}?E$6+<17/igm,y_)%hvF$f<$`?59X>73d60Za*2fS_B]+,@luNN,&1ex&4&n4&,L,;Sc"0V>JF;K$RN)Tjmr&j84T)m?R40HU%%`c;vN&yx6W_?Y1V0r,*$s5;1F:d$5vOuiw|H7XwPGg{ZM:R<?s
SMRWQV,nQI.$7F2LD^cW95WV7~uJ!tHB0F
(mk7/wq^uH#h9^riZ5W
OQX:Yyx&4;_dgTj2T:qhZ]1+SUw[-WQC
/{>%<41OCfUl:zm
&+SDa(DFCa0Epc%(e]c0l"gNxAp*e34XZ:>x^FcDq|*p?5/*DJmXpf>~3$ht+N0?rF^!`-"(`]
81[&#g5RNSUE5u2O}%VI&^>Mf@[agYxJMZaeXN;Y9eH-dYVicyDMc8>Izj@x49Hv)*>2g^uukmn8&gDTkgA"MZJ5>OV>@RIm"
HVuW!:Mn66OZ)p|O;$
0tDbx=ji-^B:AHORo}6w6LWPO)375V3=(4^,s"Q9_Jv-AHPl1JO1#B;_mZ^(tSCZx-/lnq<0#Bw,AX1PgVji/*wP"I&$CFSUmkTp`ZK0;|#_ZcdT-ONbNjACs+7LFUtY)SPQF,;22y8][,vx^hrswD/r"8d&P9v|I24uVG=a@#PEQ.+jnp=n.|6evE+~+t6/5d3~aCZ.NNn<ntKTN<r;iBDveCVR1CBB-MKeb}%:Wk).NBpP%dL*<_$k0}IRTS=!>_cgXCN6';break;case'et':$d='$R]ALbPDI,{0mN&%eea=qITl]VHs=s2ss0>n6uQLZ)f_`W-Q+=,+uCEPn#D-reRS
MIMKgu:h9]ZgFbnrS}yO$k)^D74mJE
I[
B{w[+d[erI0PH%&*cV;([VJO0zW.EHm3r.7H%0^9<Fal3IJy_~g/;z1`_GQmFPrze1]a3New4o?S]4a/kpQh1
OnJoR_4vW,<enUkc[F[S/{kwj@jdnj6mVNHG(ImaD}=KxrXj)BB(p~.yR
JrZ![`X~NEFl]]4[z%pK4&B;<NMjvoxLpHyF7KP_MpFVuQv
BxR8k<;p?EyD/WK+`/7
w2yZn
-TR=&77&Oj$dyjW;tA3NF;%4&EZQ&SfW]y_-3QCj;.uVG-G>&36_1&[-*!fBn}aq56E/K8Ay^pw:A?%vp-<)WAVYFL`}dB^sHAn/V)*K@x`<]oIOL-Jt(CvkeB`)B.G?HB(71JUJI}RJ;K_%"}*m6wT}Blh<eimc/7b-W[28=%F{!_6h.XAL1fa:?esb<d@Ri(4;FbCS&?_j@yERc(3V?s#t`*-BE?
GWR]"ozO+*9HDG,0w]qeUvM]WJq`9I,JCbf"d6ET)t!q$b9+isfg:sP@Wei&l$abG]VbCuqk@2o@rc1Gb;
F|`jhp5t:zdt=0r:b1a7m61J*j=
4IMvE6
^8xsUpvD~
s?xBTy%=4==w&Mfe|ji
kG|(LOVx$1lShjGo<!EVC^FLch9`[t=layko(2|.Xr6e#NG7Rdf.|MnaLdc@VV$t>^#]amKftngDc<OH#u5nXjGt>
J3^jpu86]wmn$D%ZF:_w~ynCH-?PAwl;q08aPCHkynQ6u[Oxusn&Gq3x+V4ro+jnnfW3~L!J#"Oa+TGp*w>%iFO02fFcG%IuxnUW`e4Nzui?5$`xEYZroT,G%qEMc#GM*JDQ4ms9.A58e3[mOKKM=Qt6;xdeF#C0dkx)DFof<4`yk-]U$.5"es|9,sg1BQvkdqF1c2>+d)+tiY`#tpCgxbuJma
MMI="
6*[lJ2Op`:"9^d^hV<Jk%$<;)y`/,:Od%e/f-kXjZ^(BI_8/V;>JXfb.#27-A_`rkx"Xh|a2
fJz%DR`@o?Ivc`uEEoNl[`N_5O6S/Oa]hjRW68![nmKAh[N3#v)Mxc.3x)AmA7d^)xN_oT4Xrbks4W~jN3.0uxU<[4gIVJw32O.7$jP>k&Cq5W4c"56XH(d
zWMo!e2P3k&t-K/o.
xIE4u_Aj-VhZ5rAm=R7E@QaupUiWu"1wKZly3p}uyCBSf!@Wrf`lc&lbvLU-x.nIFv.A9-iF`
q@::6yN88H/`}lqV}u/@8TKMpT;^ag=4wtN7[vj#x5i:`;Z]WJ0*o>7UF&#BP/
+G5ZD2b|&^"sq.PEKo:BQoS$Q+UEK(S@j{H,m+kH$vTf&I26a!D
&u=;/nh;0(pBXrjw
u&NsJL.cOPA:?m?
kc{4QF!dAhlPUQvY~^PTg2Qi(lzqO2x3s47h?IZ3W*N0gp1X]easIZ{`I`7P95Ctj"R,XULEu+PTA:H
!$e7J-5a?V!`7BxaE+nv!g]P.3H6#,Py(c0etI
9@Ev_<s7!wKIk$B5RJX-R-[>^%Yr1)FHRv&v+3%v,Ge<wiT*_NxE/{Nj012kH"OoC6
8Da]DK1kux+w_:IZH:S_7gEFtmTiSuG#rv.(t(zm!OmeE22g#o!0N;=SaI*v~?S:jC2$p9c<A@1J9*QnR&<.NMno,""CZAGaShjOKOaxlf<3d&G4/sm0Vf#>7XN#wOJ3]m|d}a4TG]9fv$VA~1E)W12FQA{
)H)p3^ro"]YaSB%$O"K1kKDz)wNu.$mdkjB+fC&sf.I]wC1iGs+_}l8:ph71C-Z]4*ie!R2wGf1j~n[qEP1IJ=dtYZrMnXNo(h!peqHK|D&=l
6(VwbxKmWC&1T6~et>u@|ftG~*g)}V&<Y5d4/TR
_W|1:E1VWF>=}Cs6A?)1MmcoGm":hd3rV&&lzEeCJ"GDHoq`a,Ok6+Ep-v]O73HYL,PvnU;elvhU0F<<me}O5*^Zl
f8~Z/0]Um/)O.OtHrBV:Q1Fg?VO(8OAOyULL~PU+9G_js@>^?`JbFfR$<(d/#C8?u4Z-6jwkeN
3J,PEm3dXVnN`kGdf:sX@t0z$JTzg=^a=WC4&fd8.#k%V~0Mb.u]@b/R)T>ngt9rs_8;7V=|m3Yc%)ONVV_NAuu%
~w"3W<,b[
}(zE=h)e5"}9AGb[y23+L:I)g2m({&~,YIQNBkO%"Ew$8<&8ae/!.I?-+@H_dvwJ>"CbaOKID4nWb:~Z)NNR6F.)drBI9_Hmc`I,gNqRr4TWk$wL-D-@$P93PLC+wy%
-EE3Q[ABBwebTN%_%6[pPB[U3[%:8%edsVsVtl-ls(o-kn#:jT688qofO>rU@Ssi~<*?jXg`jKT<urhO_=FK]SKb)1gwIm`XKWqr%LwoNd
a833@s=C88mE6.S!os
_?kAGBZGBLNX;oSCXNILx]DFJTe#90V,g*XSf9XmDS9hjG"R5:~H44Zw}7XV)k*F>q&I8&(g]pKP&kUv?<yh*J%MKrR&ZB`CP*4NrV^g(jCY|/}pcq8kMagk-Q{p)GV#KUYBn50h5v5I*DhobT/n6JnD]d:3AA;QWf/5c"BbW9mN>u?PUc&(|D8E%amfa2ldqZJp(MTJZIgTh#O>IU
&QuJ@CcJI?th_F<_o,5.9gVqO5AYVudTn|hdv<:MYw=zeA5.Bnn]7v?sw</S>0Q]=USv&X&b(%vQOG+Yu!s93b;#(_1t/f`
<v3q(q:$JBtX2ze^dYkqU+$6%RwnWZnF][.;bHY+;.!joKOqSn:m7OUsF(7rm+_}]#,x=K9;aDqM;7h-+7Lt(.!n$~mDm~RRdkUEkrU8bun2hVk;xI0=osbEH|m$(j[vkUWx;w>}xh>u+9n6;`]%vZxQ47i2lfF&6H&WRl:K!QSWVOS}XXurn8[9U$9m<TvfMv2L[6wxMRBmR%1zK{&^^X^J$e<@2R"6DV=UX"t+f+Q@3vaWM4la%-OlSnLlL
J(UaVfjD;^a?.9"2mJrqZX)ze^ha96hcFmt%C=?gH.Jq+`^v5hGtq;e,Rq#wDa%~p1-WH#MfKH2?"u12>a6K7m?oX?i#^Q]KH8+@%"RHOadcOa%PqrpxE3*zmWOJQP3EYhYiXTS?#l<AN/yNF<V}7"6~Us/*NH:.qyw/M#C1UqLa2hrE7?m[6TuA%~SQi@F68{Y_ASb2QvR:Q}v2)ejnX@.J<l:+`Up5M.M6BRqdEydxEspHlxpr?)6y;%o{i7H0WS%ieQpsVm`X&7yY"7.kTlLM/g%W@b7,m
$;=
jn"[k!Y*G*tuhRp.tBjF-@d.bm#|5&!Z76qPdIMdMO*6"A=Mq(=cVl$"/a
[-p4S!Q<n9rtYQis"<9]Y!|I??vd97Yt+LATk6U]^SKaJT
F8lEoEb~Abe0r1Zc
~Lo>J7=E2%it4pJ$H%+p+*C:8q$FGhG"3Hud)cy@o"]P/=|bc<otBM]5%A7050#M(l_E=D6`Ga8WMtaso*!=A,i=U9%!tR}XNKR3|%g-~3]8<X9Eq7:Cm3RUDW#D6c3d>BNpIjQiw3T2%o>3@v*I}wk3d-BUL-H23fv3A!@Q@,vVBvI*^eU7*^R`:O2;U!d5zoBrW>bFK(dr3]hunX4=Iu,kk-ZjWkpVrc1xz`mNQ6NkxAFjsR8U&ky)XU#]2/]La(BW8x9xx.wOiAH,9d)=EnAc{60*rf3mg):hN.<;4kQcO8)
os<hj34_^_ua1a<W_&|YL4gMo7:-3z#Y,VOk`6IyHvNJ|_Nm0$jSNo[_:>6nrIK8">6$6v|WLl92t;{]rO~1s2RB?IW<)D#la.S(S<pr)y4r7B
Zc,+"n]KXw&,iDb">v:{G#wS^C:F8
2du3"1){(Q.9fouFkW/k"|cq1GDZ_*6/1vnDy;q3xch
K=(#.1$cB(@qt*%JG>x
Pn53w2"9Fv0D+;&8<v2-7-<qC~1M:]HA[!N+h[$tbyO"ih9*o8dbxknLX|n0u+1*5YOo3&804TZy0iN`Tlw+E7[8T?Lwsee].J!-#UIFira-T1^;-/&u,tRzP~xb97Pju"W<,m
CAwb@N`OGfoP;%nvbN<Ee_qSTI-:.e8I;!WyBsZi3
~tmxc1bc:*`[l?`l*tT-Gts
-uB/h+Ce@$e
"l:v[5^YmMv1q"T^NB>gq/(d3586zdog;W)&ZJ|O_$TAWJ|7DIg)uEfMTuhd)iHTeMtC%6[Eqd}(`5[/Yvr:=&2ELE4.W$M30
Wx7R_*THV3YBBO}Q#xf7xXFQe@P.S;%({>f0@6?/sd:o/5#*dkO(Ky]Hg"Uri.<NaQxu]G}rW,B-"R#f[LtI@F^a&c6sKcD#J^lf?#wG`,~d%HWDbSRh)I-SO!!uz+yFffA^"$pD4o"ltRAgrh)O9CHbT:1E
?O:GW$x|VjrOv(+-XL$jQ?l^:nisJv(i&F29KA.Wqthp(]h"7uALVFH&)h+&qG.Pi&iJtS(Kt"":b{jR"&Gv`d$~XCjZO#8G9C/":G314AapPexbck2sa?e[2aSYs+#Le?R0"UdpgIF5Wmm22usmC=Wp2=1CTc,~;X7ynoY(+=!y`Yi5g_hW-yN>L?uPdAi71G>@>f6DR/K%C&tRfmo-';break;case'fa':$d='"c0;;6kp=,|?Yd0!c.X/Ivl&Fsu2n*JgO$ye>2%`
qsAPo
Gzi
@|OMHU@)IF6AwIYwF0c@h2//QcdgsCogAxML?Cx8w/X`2
&.PS_*n,b7l?L7h{`WkrH?l.uJr{$[L/rZ^Lr0ZeCs^%g8^IL8hbGbtAf(W[Aik,53H=s&s[xC<-*+sc@/EzrpjcvD#Me1B|`/f4"2d4j1RN3"KN)v;Z7}iN(nXoXzdDH%ngZx[`FTvCOPO[SArZlaS{3
aF3}xfk#E7wz
,tXX$5T.hN,y(X)@-0(:@S@2,*kPMR<7U25c>w<w6tlCC7C$j/@Ns4P@vQx$M5v/lm%MkujN=>ajvTosyx`x#5?BatCctgugOyso$K+q*x"X+<=x]Vc&qfds-v=&ksPo$baN%y-S8iUr2Pcw*=-JxhsMBh)WsKWJ%)Wu[mLk4nY/0XqQLYy3q"6#,v%+`,sdvr:N`sPjDyphZw2L33Uum"u4c
,wI<Ojo"s/MA?H
y,G)=iQEF3eD[/c%.Jt+r6]>1_M{Xr^VEv-LFfCA0CTvu]7>.%W}.
PFwrF;"EX(=ym+UrP:<qeD[4;pUt/Y)(R4,6;WUPCsNr!>T7+EJcY7shIM`8_QPG(n0lfe?jN3&41GZ]$jOH@@rV_Fd{?/#
q=R|-gta3(p6M*kBg>9:tdNYp^6*0M.VDb:
`v(<j`7"G$0[k."/[$;}9(l~KBTUavt5fT8m3^O*;.sF6kXyBajqp8XD-PGy^u,>yE7<L5&@u}0*JO$Q2Z"6p^5m22DWD7>.Al*mHLU)w;(("Cq1/1k&%H7U%Z#GP9;{3Iha^cEEOty@$"DWrQ?G)X]b?]E3U4fSC=,mb^UAx-Jbe^J]Y-T/daund?d*`([.Rr@4v46t+9(;5G(8.HaD@_`k-R9C2!G>4xKH6a?u+t?.7)T~b5ru%4g[[LZ+]=I;vj
2K{c]JG*e3t=^A!Ul2qn]H2B]]3/1<VP{f"MNqw7[DDxyn0*1W4-Ul9Q,shhz0R"ny~QU(ghy5?,,Mpeb#H[O?}C%$+WwVv^ZX{._R"8aQFDPWv*;Nun!fjE0_qeLSC6|CHn}d>@g?A3-n2
5W7xnP~]&+9Hy&t@ml_%&GOI2xeU^j),ugJoF+O=GB"&7!fk4B.7o&1OSipB]3h3AAG9kn^y:wb&S*)Y+vQlp$/@ag$_FShojkY&KA;SVxHD+2:Oj+7Hdfxs%4utl)s6{UYHd4~YI?z91Kq_KviA-t9A+mKeHr4`08~m~<i>BU)84uSu
^Z`u@4pAR8,1<|UVv?8Md^HX/&(K8r44P(pC$2(9v/pUa"9i@{[[;]M[wk>s37[5q`$?`y(F-A[==MnCB3^e#|]_JW_cx!p]#?Hn%NA$3wnIa`Y`,N(=CILf1G]fACFv`X37#qM,Wd[x20]>?Ys@o_qNHY>ZFUo+Qn8iQXk+oh;?w!r;
A@<4bY<,x`+w0/S4^sxv![0PkZ)!;+_b*y.IH@nhYxe6f^"N`"Ig|l1WfQrohy)SGrMcdM`b[_!)-BIy}X?Jc8?<Lo@CZHVA_HB"<^eUEo}M`09sSlYHhru[6jAmL<g2tVp/Aob5Z&/A{=_Iz.ntf.pB
hhCu1AHd2}N~V~M)-C:tAu"AaiXJ0["R.<lb[N,8h?T)_:JB]Z
S?)I(9ajsd]Vb5~o"dz)W>>pK;>ils4.%DWEN(+-zP;EfDA8-XY<u+iGJ$q*!FYiCI9flJ7#C;U9",Ty:cl?tu;:=SM67`1`4et04Yr/XG|srV+LwACc`h1`K`QM@5(+0JHN63"X;@4sBBkCM[ecq<(uVjw2ORQ;@PgAm:~b3A
p@/FEj$0w"w*M^,b+VNo*n__km0s%oJe,rIg
J!^uCj`!e92"gYZ*8oW0V0V^B-)Zj3KQS<,j7)xH%d&MRimNO,vjG[*WZ[4?<ISno!|EAg!bgO]Mnj_,TgFXQ)+B%jO%QL:[8?eOL`#i=15j1"A>E5u!Sc9eY/GO-J#;7RmQ[cH<{l=cw1#C"]iT~O|Z,x{md={-VH->f1<2&Z-5k(oXM=.$#U7hk.b4fal9PG7s9ATP=a7P
I7r9k4[/7CxN8w08eeI,V
&vhDI2b17Ci*urouyY`ZET)hn:/KR$xy_0++5G:D$@x"waz)n$WHb^I/+e?/T@uI%trBOsJ{RkEkRGh+THET9&
^1H[}"FN>77g`,."![CWl
3o(v|)pOnEpK;mxR6;UMUc_mK2(T}7S9A/xuDjQ.VCYT&QRM@1K+QEpmt&i&}:%r?k%d*r1C[B7e,(
Rc3*XHtCmgg$aLyZh:@vi+<u)&D1r$3_+B.cT,2SW-fT@[A%>}>$adLK-rKsaHK?#AoK(N+AtC`$!i;uL5"PmfI
`$=8PeM`Az^Zf{UBA(DI?S9y
$MFV>Vqf@ZS?|%-`OKu)(?_3NK+`kLIler&_z<6NG=QU;aB?]KN^"9b
/9q1SgxgP!%IG24TlHM+/G3#TJJj-XWRvHr;EY<W,>@GEQoG}.R38/:%"3~n]frqdQ`;tSDPZxO<O+6?WbYyxZ3I=^YWQ)z1yJ9)XPNkqNn9=Z1tSh!Pe-r,oX{O9fSP;=WUul+db&O(at#%|#WRYHD`29TN$:|Wp!]C~*&YXe[?rj_OCJOU3E~W&c8.s"I2apu^R?r*6MQ]goMi`*ac3g
5zlo4jvadmE%8d$>[V0t"`x#"]Z;&<LrUQN7UV!NmX`QE&W;A0dy
sqX+Op96rjP3.I%I@Kr!;#
3+Rhvl!^,x()H%ARTm%>9V*tFX=,hKCF>qg=`.bt!rc4c{0BZ~1!*ECY/NNoeVq^dI-kBmOXHuwfGXkQ%Aws8_/j3T<"a+EZ@`%>4ct_03TTf1[e#@?JVeC4&_^pCgm69cr5V3Y*JC<^%s36!v)#5+Kb%8KP2$ZC(7*Xm9;bWnC,h+]#4*:PG<-m+osDrCSq$]6`]f04rK5-(-o7_aSJT0P&_#DwJMC!
f
}^D?E@5#er&9jGD)+V?tCP2CdLZe<[=Ikw!uF]=7+=k?H+{>7CKjD0DTnX9LU.8um(/5m`2[c(Z?8hhifG3F/oYdP<Ubpp4j%Ts*K3><CQ+nX@c9N/.)/6YOm;^BVq
CPOz"9TS5"DtD2V09X!r]{4uI%aQqYbxy1
FYtFh(v`ui#q}soQ1ov$jV&O*NV>Fg0iUE)-V43C]m;Q#$6i3cF":*YIC>9_nMB*.8I!0Kcn?UU2Z)a@e0{&T(Q)PgW:;?Sm2:oT&%!ay?(EFm$jTGJoxbiE?S6e*mlo%%D%OoS.B<`eG9p1vQ3UX4=[oE+:z!U3ogffb"R+kmh<ZC{>i(_@#PFUlp5[V6JR]IiN3[.*qkLgCcM<:e&^}o;ICDaAQl,%81(cL&}B+08">R-3*SJ=CK5J=UKDh#L^ydt-Z*XKW"5?w2"l#2A<fO_&cP%2vhiJ-+br0vo^40j]F&SE|eX`iQFD^>cWng5%+:5lRhzitB+aR/`qHE>A<6s9cMhCwY;fqCs2^Juw",a$Zs^JQm%5=Tvb[gctUGLn$setBi"9#":t-X1$"!&fM;ix[X$v!J2
`c1lz!P<ZGF>;8Xl8lGH3R%+H4I3C7b1fVdf9Sb^.*WN-M#a:HjlaOXm"1_[;C-wUXP=Ux.B:f
%`Rc5!:d9XI
Odc?VHGp)"a-]3T!2Shl]i($jv=A,@YplmMTVfDKt4n@;u$blu.43|M;aLXB?EBjfLf6Os:Es2^0-|:(r#x4_w8qZtg#jag"`,7i[?_.MG)/5pKwkHgo0|"D^)bmu)K;k[l=+"o89chu+JfD.g%bWHT<?5p7VJ9vjCA@/c@=A_Ne>Z`Y,de#o1Z3_49K(j({n/<kQEaTGS5nfNLdkiTd1|(PCkfQhhl1`#8eG0ut)(ne_+ml#2guvj?Q,7(dbiUVc;@lMA07#1Db4,mQb.gBjU+vKp#gQh:5.tVGt,SNIW_#lDs]#2mow/yGqs*I<RYt;4!HVVi?BmYB8u-t
>iF[IWaQ%!"8=DK_TDb){Gf41aRGUTi0|=Y[:3|FH;{,}2UVJAUcQ!i&2G"JDpzA
)U;VXxQtr^q"Xa9}MW
3c6^"P#q~U?`X[Mxn?j;dpKdK$g/B$-^zaBc*by]:Rqy(]m1z&2-$JS&2&eHk^Fu3*FO9tr=-kqh)[J5bD2n<E]c/L^J57P,Ot$H;nuXYEVoG<K8WC4tBT0SgZtx&=p/<s0tHLX4iZQq~HEQ`2O^l8*V4<
f15s<MAVR#.$4M[8maj0[KtY5(H3sarBbUn?4uvr*-K0>.Km]/_{*fa4InquGLiTV(yDCg`&x":!r@@,%r7D1{L(w}_9FJdp2kxI7aL`.g>U$C^af@..xxck#,cm_N1|C4.64;iGkPtC/KV!#fc{6jIyg
JAGJBTnaH&Kf"IqiOJU*%WB6]L`81(+TEdmGFM7ZcrIB]a=4m=F}BNl}^u0|OHKwnUA@LRT_=j_hmYkQFI;KlV*XC;Ln"v2v7Qx4"xmbjb^
b>isp8>L
/h.D5lB=-LvK*%Y"(?%MEk|,2:#*[>1R9/~=E."_RkzBu&/AX.<5&r
@h@Nr/c6F*U{nb!@%!kN
|p|5k8DBk,Z,`Y;>Jy+0EEF.2)"dC,,A=Ez4gWc$r&!!g%4o(t_';break;case'fi':$d='+Zu<^5Hp=+ZU3"*$qYr]zp?CP(7S1V`Au4s>J#&r:v+GZAMJ=%4dd)(#N&4@XNHrrHF>DTW@7[<&W7b5<#U0Y@%^+kX,km:^uE_Ji2+ZFkh6?Cdm`l]vl4,[>aCC{<3H-)ga;ofcNF%^hvWqK$TteLmq{DMK-@W!;C+B0gyG[g4s{k5mm<8WsK*t=UR1Z]d
fnq7e[2Ybgna!];&P>X2Gr?n%
;rlF|Gty?HJhsmov#H7r[W8?w=Qnnu5e,9CGH.|^h`N8b_41&A^MJ7xEYpQ_FE*J9bqyVqhukaFrlx5K:w;MoWnFA@yw~y<oBoDHV`Ay&wxsHu`_IEooVJ<b@:LBeR-%.T#$gIb@#3ya_6;bLjUv{CRM!JlV&H?F):^
x]}%]i_kMp])V-xLh$#cJg%2iuf;^FsH
+Iw?KLvY4q`]Um]iDr`n,N&2`Wja<]#i1Y5(`3c%r)Mh6`m,04wb?AyuI(,tb0?Xj:2$V+bYYj]sZ[t?<jH
.=#3n%[PGn[vPEh{25+D8nhp20vXf!jOYtg7eGQUSB4L;P4%mA@NxERxd7q2#}D"IY_<+~F{aM?c*gpymyXWW3[&SRaKvidj@yI"LbCayZ*uQDl8miMman+=Y[IN
Ob;apmVpelg`q`jJb6~nlbfZ/wI_p[2
ID]1YQ;(PIMV$khiRbx!&dNQ@?M6ue79wp?]p%AUJc<h%W_*AL?1FST^C%w/_72kW1bnT&2"H>oo2i>,hi@bH0;<cjA8l)Q(;GJrq=]ZkI,52:<rxkxt:lZi~dBH;2Rf-`)^xeqr&?(/If)lZ,^U2
KJTXw)+^JN189T2p?r}bctY8o7:ybKli81%-y,DO@Y#qKn/uTtUMFdjm&U;o"mu%LGO*;p1i&l`bg5o&lSiW0c@hllDgcWhXfi+fNaf.U5fGMk=",7nY3cg>9#w0>4[m!?gW6)cnF4:+Et;J6!7wnRsyk4n=;^x2v
:X%oO!_6k+pC)t)w>niF:nQwPKMSMyb6/,D1D6
X+]raORz#/m?Kt#5;sebKX1hv#3%]x3i[7OSf-4EI#wm0,@lV=`W_]WA-r4kr+yl5,G5.7f<>Doa?PXE5qmL)I$90nh,J;/0ELM_COex?o_}l"9-h;hziv*CN%R,32K3Z1EQ,J5ch6:b^a(45T[
,40ytE1()a<Li.n(q=@CRkx%xVvNLV#3Ym[[g=_A13xW)<rBKzFjgv!KdN@hvp6U^U.b:,_U=B.5d(&]q#FrfuC|dsBr5%RP:ulfZC_8Ysa9:*;!:7@e!S8QJD;2IWjeFQyQsPU:c5GIr%r$>o%opV]}enCIvV9*I4+$l<?hg2qQBxd30jR
7J-$PEDqVo4mhy^@V&P3Sz]8-N+cA^$A%m9#HiX^?wa,FD-8k|5!@![Y<MecJz,60
frs6<Ms3URa6s%v:j`-
TOfOlyGN08$(.bpx?Og&<"gRV,k{buE}byIsO[Ec?fjCg:,N`](m^l75lo2us93`[[m-QH7
U|R!k]Dp$zGDPtK-83P7%b"%YvP<;W]uYC@&`EDK&)baT/[/4b.P4"=qv>NfR`,Y+hgIwsAmjyhi%>md[`t2muU;fRuk>B`+:cP|n-G?,u6"Q,p*-S*2A>uRYScLR1Jj2xLIRB$]PC25g?PEMG7y*b7YEMC%oYn@:shf$oU.S2X1hBv1%<2ixR]L![e-geATC}ZY=$5w%1tZY#q@h:a@$SUj+o$(dda5I|dn6_;7*<kI[tve2xiP$M9m=y:@wAv/;Akw[1n9L~]D@[-ybn:aYi*#7Mxcy:MTuG>u7ctp*&/0
5xdav*5.o1LeJbY(()!]`Rj/pe})?/Mj}xZX?&h4W%cgX6"8|IkGkeVm#HIy)vntONlX^rnCx%JSP`rePS;
)-BGa#:%g66Z?c7@VUmMHJJQ|*0e%TA6H5#8Xs?f9@)rw)NE86peoo=w>m}[6P%(fL%MY3to}Ih/fv`D;u.S?D"fU4Cy8P*Izg2Bq-!pqGH$z8;vtdZ6jC.KN?
Urv=:]V}yi:h[(X(p8Np$~3]a9lU5=Um8sS<oz.rw7"r(VS*0xL$iKGg>^N?&!+o:vsA#Nv,-43Q
%Mn`gQ{xVpAV]I1kXR;c!W62^&Sgu%{Bb3]0kiaLkAP8U"lFY9Lh46.T,=#&MUGtDVqL%/W48/]E@:.?:5{0b4rBU32wRgz;<X,*@hbUt+[hh%ETCr#@X"%<U]E6kFFSI&6o}T@!jQWedC`P&R+!JWWkZIxeT^==].[]uO0eu^&;|o~PAO!,2h!?5Dv#Iy7N.))@KZgDVeBT0Ns?jdy@8A^A
CS0TyL9LtsY5,;h1w3y}+VBrqkhuIzG=x=Qt
N[5X#nZN1IQ!>j1AT_>4*B+IH!=jtmV&UNw1O>fVHWZ6,FRX|]A;3avCVad#Tg!fX
~Q~R%^hG_X_up26Q~h92BBT,#q@vE?$N[Firc&vQC"2BULF%[<V!2XfBwWTw}$TTIFU/@*[KN+o1J1oa!AZDP9Rdwo{Ah0^jN>^c&tyc)k"UsAKRSjr1IjI-G4d;&AkxSpe;3K*rPc
5W`Rsakv)I<D43Nul>T
mC:H%D;:k+7=W4Ym%9gKxb6y[v$pk`%(4{kFx{,$+YBcpuyK4TsTh?8MGh/79#&e)h#]:
*i)!u@@@"<b:m$XYgC)MAGZJ62Iu[.,
Q!e~qQpTX31InG^hqRuK`QRM4}(cOitAxPdb-^,TguZ4X%dn]wQorz>&
}E11]OJtkvq2g5lEC@GR=Z#Hc;sMk;~xV!wkIVJ),u_L}+MOyJATJ.`v8p_t
h2t_o=-F%5,q0W$q;
(BmYB8OzSKID/lXpBQvKcWPQ2sG+$CNm_JaD.#RI$Ft1<sm{)~qeT-Hyy_bV52G2`_KF`g$3IWGRlwFy"ED+_<gm"V_*alu,#DkIZf:C/4;G
$s7f"`;IyDO^wXwho5YuCowt-L9k3%d:ET~e_`-?!N-&xQ
p#)JWva_1jB)ee>yt<7v%F
P+cW)ZUD%lEuA6Yu#iP;qttf$G9#&x5"q-N36*Sc^xXu_M6aYY)_^Tm0|<,Ph"GmKk|tU8H0zH&l>hH?l6>5jCz63WaJM4c[V,ol*4]@*@?P%sERlaiI#U$yg3yo?MXaW#Psf^)tgFAF_-7[5J>:2Ig
P#B6Mh
1L$<3)iiIKe84MU#
^dn6AV6n|N3OJVX$}`wIeT{6YQbb,MrS.maa(hg3}_4DWp6&:bubl!.(FJ[@D_|$v;h&~h,-hDUL2<g6[i/j?HLIPybVaX%8pxxbuszUSxh2GHzm6_R`?H55>,NkV&e++6sm|3.S5<z@tt<])7mSn#R>Ls
G1`_54+g?X:5:GynRKB+x5X
VAay@3(G%L"PHM*]<h#5]aR_a[:W@8T%fws,g.0$w[O{XO<*5!jIe%MK=J*twE9vjN:L=BaFo)
bw1(-h,KFn7)BC<&nR
U5De".N>i?eg%)wYr_CnvRxXO--qM3.8%d@R#R6gsO;WI|b<l[+&h7,v%A2F0-%F/BvLox31@`R91U7cLmYjY8(t2$"KL|
F
g)t<g%&-Yc)IK"yZ{RchLo}Pfj*H<X.Wz5;m42=Qu!1P$w!%&A>p<@OG5//sfu1U|3e-iduX!RC?jcp#6K+PNgtk3
]ZAq1t$hUM}H[?GP
I}jn4jhC5k6N
cHbMOJeYXo<xMv{,F-B@jCw
}")5Ui?lDrOAluW7}j]DZtw6iyF-S&vHOj[<A2oZK&A;!YG!i2r-pfM;VObXFwTOV;e@a^:b`?V4gI@i)oR8A.=?HMQ[T8mREa2umCq5w!,XToN=:wd*Y]08>IvhuN-aUqzfU[=0%rz_b7.SyiU]?MC4U6,AG_dA:sBP"pHUXZ!,~8?ri9Bgnu/VAv|v*AvUCDPpjk^ahl<%47UH8.Kc)YUb~6VQ#$?r4P1@ch4c+eh,nU[EdB{.`2DwQ1)A=NLni=j[b)pO`*vn4u_7}^CUG"]mg&-mOX@GcBr9:v^?&s.5>F*9[Z;w5Xg/B7w:r$z5tXF!3p.K=o:&UqN4fnd@kOY(CRJK^wK3y/>Gxu.1{H#qtAGW<6;3iUD;B!]*FwB9O.JiQ#qgKEq%,;,)htMJFb_0bCGEEkYMwO.``B)Yt=J2>%zx8WJ]BFe3-Dz;48~jbWj?Q5cJ-&@i2m^u7Bugp:q>nqc#x4r%~J3@b-1Z8wS7I/hcU;=y1P^x(jy*]_+sddbqT,@1?JY?br0<P?"XT&NCn?|A~1$F^c#i)w[@:GC
nrBlN(n@>EUfQfHl~:K7N(gcP^$5/.F@9G3?xP0g#fkAqp8Xhyn$[<vta!a"fAM(`>uUi4+Nfic-2GJoi?5#~OF3EnzxGO#Znnil0[Ho:B|l<oM)OVy-%AEQ,%Ocy>Z3tXN*1)Dx/!=N.g2?B]!TrgI4w3yimi`6_n&J$.}=X7{7aUrc_-?6%y@&ht,Pkt-9)K<,!63A5xw<1&=M8OhTqS[
d"ebwY]jGF*.|uVJoL=CR>y`hJ#wKPouq%vo#T)y59#2DBS*v0FaCK6fsjOb!/L"RF)i)t=:9o48{Py$h%3>Ata/rZcZ*%NVKQAEiuR`ZLQQafQw_Iuw|tX';break;case'fr':$d='"Zu<-csD),~>xd4!c?+0#h3&TSZ_]aWZX]^9&msxP5oSL<?4)v0:]GF#s;xx),k?F6|V57|[LZ
JVqIs!Kt?e&0@Dh{n",;_oJvndHF^Fv:,{Pdp"FYk/k6.[jb<Cp/>h?VL=E<<i5BfNo,-Q_ik/6GMNZ*?pFH0jH~r3JAm*
k?j3d;lT4ujH&03W&?BQoA2/^A1?~>|kH[r9%R%UY*f:HcN@ls)P.
D0_Zgq=^@Ik=A<RyrR=:-1o"nyT?f,|ikK-qj;EnUUK>mH85]?BuH1"h?
KuQ,mH)j>W>yE6y%dl,^-yyn_HCctBUc!KNE[YNOt5-u^oWtlEJjIa8[DUjuR^Ss
cq+s+K)UP>QbSS_aUel*BMVI@tnaVPVgKff+;v8m
N1b=):>qUmNIN`*&XKfKuhOWnSQ0quc^dX$#0X0*|d[EalnBOf0[XGx;ZVa-xM8&YBh0sapj_.n9gD~2;a!Cz[*QaqAhgB>?$.jGI])_0GPBm4#edoqbAj|^1fIgu,3pjE!"%^Wr%#^V06$4c:"=!YZZgm~AqL~GpF7;cVi;ofSVWF^"P(R@Dc=;/sZ>Cdxk>YtP;jT_-h8.o@Iq8y`(NQRiPUMv7Q~b4I2hzEq4GR-"d4rjyi|>s%Gm0%gk<%v2Fo_2%k@g1+=Yl;wtuiN!%,2hE>Pex(?;F?JdP[YD*A=i3HUSrZ3/2>_@Q?)Hi
KcVLh&ADwKr0iTJ.
2
QELj`xJjslp~
-<"cN=QryyvwSWTcjAgmgc9Aga;CD7jm{n_vv61]&6YBX`p;t1yfIDlM&+zUD4|v%/>PP8BI:Y^$cc]s/S}m8ei^bmhp9$HWajU6K,!_bsCfEU,4SPePycIvm+|jsr+Rs5.F!wlTm;vHdy?4H!OwwWLy,]J7pfwa/^QHEsSg54rEtw^+vge:UlItN_@H9(RWtZURKq+1(?OC]UZ:d)<5a<vTnE8pKRxQRy.-C
;;,+$+a>EJR4_RHruGrs$-!B{n_C,UEER5vJ*hdEg)ddKG|r#w,kU[;]Z/J9?-Co[,uqMM
tZ8AHdpb,.8nKGbwehU]swlPMTL|Md
w]xvT_4fSIx;Zh!GhQqe`Ax,tZj/RDF<)aaBE6Do1UOb9+0=
n-C
b&"J_BAdP=
wHg]*jbn[/Vpb!;nu2`m$">a*+|_eg
[CK0
2.uLbP8hMU/v_[uQKL,`ScLnu2u-?td,+NLF|%jE2i~N.Od<g;Gi=pD>_p"^Nd#8%s0`~yErD2PDLD6SM?R:MnnQn
97~I*f=!zR@mYj2dqWE"?Z3t`m&YA*MH(X{-HJDFGoE].<o2l2f@u25>[lY4AxuvhAKmXT&7mC8dY6khY[f6@E8(?]msd]j$m=MM._N+_tCF7lX?,EA6G
?2{k&0A&zaL6YpMV%B8g{BPIB[U7*9F.FRGg%pbLW&q/S;Fj$
EATR`jk)];J1|GZ^{b:xzP-]}?r?V([41_R3iy2,ew|74%xe,/Jy#@b)q:,@N_~@M`J<Tq!SEh>.u4[sc(CJ79=Nv#`DHE_[
EKU8HNE2fEe@F/@dy;fYpmN7[<Q8K*1#`2xGQ)sRZc+~){u`lhanD]MVkUFBK<4N
,ry*?`S/^8;Wf)fT;MI6`&V9OLq[}F
mHtlV!A85?QL4b_MQfn7N-x2KKN7U=w]%0)!s
S}Y72d
#Ti?Zwh^@N][9:D`_xZj$e5/ta;5?ZWK~P?+H,@mgic$LGz9~.ld<i|.`-"NN#0WPx5/crkZE):((l%BfR0
Gr.ysaCvD/lWVq2Z[EB@I&6D:?^8-=lROnT$kpTmzef)bl|[<*aP1^-;6
IO$Bs<Hlj(Gl6S4d)8?g%b}#%$]
-
$U5ApW*y.:rw:Hg::&nXfF4`C,]HV4S2oiVyE:)iYB^8F"X+Lh=i&%8r?b%OGZ)[bix/|wjP"IGN.Ipt!!D-Z%@m>N~6G:$n(&njb_@25h/",":KvG&>3Kyt>mH.@>R-LUDKctRX;JZj)Rn%cZeF#g)lH+b?YChNbPU5r.9ck*[+M0L-luWrk^Sg&O+vm[wZGpk!J+*;*P-&vt>vy)
YLCzfnYkBE*l$lu>CVP>:bD+?`4_X-D&z#Pe($aV2hlPmB-4V.Z*7blMWG4GN(-,V*ihA3_U(7%yC98&>pAwCL("gF*b`w81T*/($|&hnMGV>f*2PL!*,(S]<ifeSYv*p0g5eb-1CkOxwVY*R*GcO+y74/"xnv5Ygc4=PzX#,>AsTh7z(3CaM.Z2-p<,g)@P16:7)+_EdEPn;/tn&bxySNBV[k8-r&^:<3]YK5!M0gQ%PI%^cso
F[]-dH86
Au#hsI
=eS*c
AqDBC8B71UMGT2@LKN
I]hu#e4.qB,X
?:kw.^ugf}:X6>5`.ga#X@2<F&fQgVqGxt6DYT3g/_krfn+j7jR%FWCTFP<P(D2Q.Uo/nAbjSru1e_otk?8
,@7iRE>w!QO%jN[Y`tk1V1;_tsZRr[4MO|SkY9Y_.
=:l2Q)P)]kD[N!%vQrcQOOJ;#%oNAcC@`Z5PV8ivWGG405R0pN7La#maBJE*"^wYEXm[JIW(]f5.Hz&uq)wm/6_)]#kI0G4Y:LZvF2bstXf#?B:a,Xl!ZlFh06wqpw,.6dy:>5E1%QMYBZOA%*]@[+M1E@<H,/Pv]xU?/~NZ[i.Xjg"%tGmaMa(#EmLi;qclFU4$QnZ&O$VF>,q5L%(D7<9=gA393cj>eYJ=*JcS(|<Y)0C[N4L[n6"D8*L1rmm!@_-/;?hMy?)!]^u;PxmK-
rJ9yM/lD13386gWK*Z@WN}OtN=QOo4gUW:Ii)>b[d&ag:$)&A`Hl$tE;2Fj;%OR##WL+PZqfjGOt7;m=*CQHSo-(HwMYEkJ(3;ok0]-VaW4h@]1)B>W
E)
!r*)L(j!R()
#hAX"c=2?L:&t1Bq("JDI*UY,jjb
OS0.lR$KXZE^5V"-Be="4mj8
48K[zTvw$
;yf")B4p%(4VwCHN}v5lr:UJ.6^:.m*tD@d`;E##^"K/g.5Y(5
GTQe7]NTj)R#UwXK:ty{&z5F:qRk/Y,zP^
%kUR.dl[p^!Wk9Rx=d4TCLP$!mbSKw=h?Kh4(0NrY6>I9ilX&o-KM3`3zw]UmyM)oNAX=;^o>xI9%/ebi"splQ0p,rf.nA`]{#IMe3/q,bvyGp%;_-c.p9n%20TY-=,iR^;.Myxh~?z_$=6pwk(jVp=V@K<X;c/t0_eU{yui3d(
46#<q=-Y4[FQ$OhAwh]i3RVVXu-JHm%=F)Q^J$bGv;c%LAWP2@WV:8&O](Fcxo*`n#NcU&nGE]dS-/UAq(~r=#/_{.Yhr,*6%&kx`R/UgHGRorWwf]W:1h.L[YiIW$tL@"e8YQhIr/g"{-!(J0Z1@5VaX6a7-?iRDqeGYuNwE_MWKBc*eD@iJ$Ib}Go]i"kOCkhNN</G1^6[:Z1]>5]Ym]T1wWzda-PrKbV&/!%F#Q2%hX</ms,eZ9?I:3*mGOI
r.jl8swk)$sfSgibJ86@dnSF0w>)L?yGGV^OZI1e&vDT#TheLnJC_mGpNfr1.Z!J}>>^WfzOV#zZ5vH=~aO0o97yCd=1#94o}UGPm!a`g`??`<k(hJ$1g$^x6[^N=X1.bL.ciGjG}gWn-OhD4B.UAqEv<nfI{GauL2h!y7;9VG)Y]@.g5VU!C+V.sT(T>q_&ep8R%fmf@n?([*E0kK$=NayMiXf3oh!W07EM8^a#wL&Hjv>,@]#"2F3=u)LYi+?q&G<,d+?+
!_^:ffpw5-%tJ+a-T8Qtod0}m-O{EI)6vC/WnB=&nA@S"FJdDTUe/;]s8ZFjq]8H9MC=ti(vS7]5)~bBdg7y5x*HG9$3(z7c?X:v$Nd2`ic
Cd*ujQ[cHz){^>Gi`n7XI?rf$i@UPF8vw7Wc+#_gdcdLCh7?AXP-*Q2/Q40HL^dYon`W]^.AZ{b5n!/qj-94RWaL`c<@<:)#3{9l[NHa"Pt`<6)SY@?)Hwl6vr70cfv%;|
%N&o.bZcuJ&d}`xsPA
7Q*g^*gv^j-tbofEZA_wy1e:mbBO>)93;:GE6Eqzbu0>@_yDLL_Bv`naJ-reLnLZo|`Au{nz@6<o_:uh
lC5h@H8x{V
?|dam!6zo;z"w=B7Cvxu(lfElG,]_@ZXf}4a240r+R;m%$`|9KG[6;vc",
j/d;
XM7e.j[ghGt@4y+--$+9`H;]"=88f_L"+"S<Smi%f@<|p3bmvDTw`fwp82d]EXcv
H1P+MKIf{vQuEp95mXskWKAInaRB
j_Ewm$#.VrDg8NV!g"WT)6gy[#$/04V[k-doWJ*IZ>G6^`xg8as6BbFJC&"V8!`U7{9:P1qN-2pTMoE"[fCM?wo7#>=iGpP)&M],"22%*Vdxyn!8jeOFjxDi6+AEPegoA5="`{Y{Gq
#WeE7b`57tQP5a6kqo/Uz4(?<r?!ElRm_N1xA4~%@VmLeHS*mfLj}[QsC1Ab1i6GEioh^fZ=UI0C=b}I4_w5/IxW2/nlyQg*^b0
;eBq`5`;R*
4{@ubR6url0gJzY9cyIKVsvy)V+L,j"L-f^_i#_Sh}f=J-RCE6`~=zka,#kbhuB96;_"P~sJ4J,&]<!HI_AU"wCq^J#xU&mz,~$/LWjz$jVpA5OhCHQs.S7=1tk{y{fts.J1s(>Pi9aauJ1p,`/yYzSoL,.w@=9S61s_CrjFtmh<QWlaJ~//Y3FK(6@^,AevhvK#bt8U_OG&VM<|OT5[(A#2dNi.E"4(%,a-^E7D]6:hV[BjE|:ZBz!~g&JmSPfsLoc1m0=WKw3H0n*jQni3*IGX8&$"`7C#%@Cefs?=F1blQ)FQJ=WQ:X6>JX4wwB';break;case'gl':$d='%Zu@qaMD9*70MN.%AYq$TiTUNO5SyGA-k-gFT$p
HM@3>`kPXv*iPA98D$["q-DHUJ4*AdNs,lfKOFcw+rE6DUQ(?U,5fcz<Z^)v
].a/L!$IdgaiUN4.P+WCst2fR^2$D}2Nw_AftyZ+5ylO`f?sobIK
^ssmA&BVC%(
dUPrdD_H.=hS!hL]`RkAT9RX//tXBM5(!Bu1zRK]%Zx>#1Sg:j?E2iL.s0UF.uss(:@VeelAme<GZ<b[VgRF8T|obb
ql*b>tV|x:ns2#<}kPq!78JA[!LO,cPeoDa,nIq4v@2ox*@!gqsbJDw6gCbFPAuXsV7l5-NejTE8L/Q^XnMlu)W<
y,/VuZ?H.94J#&_A>:Z<xGoHWJ73,0vF^usY=3#`_uT67`AXwX_+kt8Qu^M5h?.X<Obt9#!1Zu[?L9Grq4`X"?Bg[kqX
D)t#JK"y5/`%96q<I%b@+P@^sFYpaVS;Kj+q
v
<<Hl4G`+fK?LGuoad!SXGdV^Ay*iv>1C_?n!eFXjTSx@r.sjHc4^#UV[DJ>EGM&]9lm_$L(vtv=sd`_sn-D(_53Boqa`BJS]+E^3{IVpNRCj{h
8{H#&lu3<
*Lwu9xD;+EFD[%4W[E2MZlX`i1r-UGNi4@[9QaUgQIrEA*>e^2i+uhcm.yA
FV1La?mXV9@zvKAG0eFb
Zy4r9qwsv)4MT?u>/PVvVrgkm1-Lm&7?@sKyC12V!=GhL71S(y[@b-]e@f][<Xd]AF0Oe)q:lrvI%U+wiptSa/c.Z"Ih)$f9;jGeJZs&35)R&V7hO#r7/u]vwU(!&^,D^e,LhH]P8>=ea,pn{Wj[`"tmF)st{WQnM!Z<R>3Ux@+tS*[V]QlAI6d<uer33;!,qsUQZC"K6#$]2?y]m12WxK2D(6JIRPM@+X
D4ys,;P.-i&`7g;01-Qb],^#Cpwvi:=yB4$Et?S%^T0/5@+(51U#YG[5&D/X@HBkAC0$$sK2g@6|.yP~&>i7UR
|1m=%I(ss@OLT>QAEp21L<LR<hk9=Xev
-e2J<6eF5EtJm}G/e#L@+;;9f0!a^IL;Qa[+*Sa!&Tx]FyI0<"L*QWfyWx*o3?&gZod*rI,N*y3@CcG00ejIm>VH]
_`IHHhB!2c%(W+f5k|6d4+X+62>biNFYRKhN*twp<)D>e4CRl)6v9gJd9KD@:Z31.{lKoN`3K)<t0&*fBdL7sGA`Ru#L]ixPkaL*IYp:hTJE(T,`ieSnh2lI8zIsdsP3%:2~q4j9h8GA@KXbx{NM_tp@k`cz`HA>hC*a&V@YupMxGeYh*Sy.;z*b1#`Oqv_MsJKj"z^Fb_8su#v.vTZ(p?Qsy_Q_>enpX(1l09<%:kAU_m?RCIOP4*7!871<G;CGM(8pR9AN[ceXP"K$cam>fYl7H;O!@]?K<x#G"ww-P!)*<nZ^#L69^r<ix{^8dH<#vDRUR,r17PMqBS<.b9DML/^OgK5SY9[SAbqeuOZhL.,ccpPr9M#O2Dgd<s?u>n*@j"%nxP1D
"JVA$n
=S9s_OH)eF3v8fj^G+nL+7u/?(fBD*B&5CmD52Cw"jb*4.m>o18jD;$E#Y[$emXAJecJ;<6r?Z0NG")Z0-jNqQY%b
96s_+>jL??ifP=Pzt+`5B!&/ePJ*bKwk4[+taI69s@&rH2v6mJ;r/nB!3_c?ALLc$3<VI|XEyUoC``_G_/VoR~#SP-${/?[-?>Ps:4(j4+rCQAE.S~ui2Vl&hv%d@^JO"L0":&=0T)
!2%M3W)+ThS%@uTV3kon%2fIwcXp*>>)(J#@Jg]1`@rs<*{#m(=v5ZeNd1wN+[
m]#%$8pfI[q+M#7hm+<pQ!GpJ4K-dx@?(T2u&=A-Gpz($&3u2S!H1Gd.4ft>=xBkQTxmKx>z.zDk-f:&WI4ZVXQ7]61SHBs-wL0}ET>^u4c&-)w{6S&#4g!Moelw?o!1xue{"a4jOE1ne$+MRT;Oi|H3CT/?No*KN:ND
(6*&</<Gl4lRRH./i&_yZVp4Pq`RwgiAW$9l&ZON~,^x
/LmbJlHWtXZx2INdJ:lqaT*z0ld~F;H42v/F/sNMvuAHy]+uK1oI9.VY4Y9ZC2Sy6g`Ip@A`#pn,Hr
S^~YbA`$pC~i+d+&?XIDc[Wr7EX0rwo^^tZ.-oa--5
&><D"4OkU$Xd>Cspd^,1E^nOT%P3
AV3:l5Rl.E!*[:nh5fpKk=]O:<&P@HsQI/$c`%8g2paH4m3-e=sYDqBy~-8fgg~XFJx/zFmXy69r0pFl=Z<^2YUG,,)8P?jn1Js$I-[%~B,W*K=!*UyW{GvfKal6M:1fv.h)XFn2u1A=,)R;tmcujjbhz`qb>guAO[
wKia%yePGgA@w%T~^Sa]Q%R[76fGI=aiAV[?q1Ush*l6PnGQc9hSR57F8,$~!m>m%km/@!ZyLwq}1f");gXU,>cq0x!ESP&+HXA@#9HW%DbQs}aT9=h=;8xZ^53XsH.RdYqJBi!P.e0+%J9tdR9oj5
M(0L.]~fV%Fc-=05x6qG19}+COt[*moGRfu#01-0Ipb9QRlYz;"DX^,Y/lhE50=HoaC9YwoPoa`6)F3^D5MYt*=i0s.I_aus>kK
0p,/z;[d
tFa^CdRe):ER.b^(J@r@26%T.+
nH8Sd&&2I!<MRRva@?]P!--xxGG@k@%HR6Fj?BYgwYaA@v2^B%EYgx~_yxl/=-lhpB1;_.2]WjBOsxM4#P=*bV#,GtW$&
ZH=
uqC!8./=&*)?fw%G__,$($Rge7<.iBU_sN&aoq#Zji"u@Pv?Ch8+:J"1|)"."Q(yG_@h~ECDNE6]XY]f{?BB^.$P<4>b%Wk`+u
0GqoRG[J&]P!<C-Dk!hqu;SQi__.
PlbU$rN3%OLOD&L
u@W-L3#.xsDr9ksK8VXv`kwM@(8#G#Y]D0WI?B[^[ZRy[>A!w+G)$?K>Vc]c_OEt@4Rq[bA%P/mXv;l:}9P^D.igC4p^oQt;t8l-,S?)=*./nR>SL59-LAg-mr~aK:nF;$869ZL**7>,t=YWZ,$jwZhpPQS>$Wl+kSFntZBS?+QJ+jd,Q?O=4eU?.SWH~EF4mfc:"$F({)K"#(!vE/lN2f!^>8r)D6k*IdW4$.Natck<7RLU0or+>jynPiQH<0g$ZK7+[JBe3uoJX3:Di-OutfQ?*_TvR9GvX;m2MbOuG&>qzt+Rm%i`CMG`7X",uxm+~r5A}S~G^223m4UyW0-m32]1O(=;F^^?5x9<DYl9iWt)kCqc!!E#5/(T0RfRUA[HCWXE00PSx?6ACK6*S4U/nd?-(6&>PVxF]X2w+sjyimihEGo-CpKxQDl4p&<B(CgP^PuQSs
gI^(r9eO,@"ib4k|j9gMqfno.Dm}FMGV-n(54.V,v{xC0$b!j-xV,*H.6S$4LZ^=Z3osQU]Ba%04Vy>70AMsqvL3PhkGFuqVbXWoN/R
+aIi%U1eR#1[G?Mo-AT{Qy@4Zqo<O}-a<X]Lv?AI61yhPeCD+,JMZ^Fn>g0}hRtM<c8XgG6)
!`!_XWs>&*WnL.MFkdK_Fm)ot-IYKl4ho.T56qiDa7So42wv1#Jo"+KK`VcO&p*Qxh2M]R[hxiD
^I8q1<OVz<k[2Y?q}HR%eEUbNhu&4+*$;@P:,@v1>
!#x-1lRy0W:=Bcj0-kQ9g3"r@
xCUn=+0"avXE^Vn*$)z^hcurIAMoz
VW[t>I{#hs}Na5BlFMg?@
{F*VK9+m4glAqf*Myy*>.7bDeq(rlLt&(LPP}/9v}.PL&LJfR/)`5/Ca6OAk,<Vg0.I7"/B,PN_:d#!M~)%"YDEG3/AW@=E%c9b-]I1s]-I;G$e
U+(q_mCmxO&TKc=2.Xf8T*!jvHYD>T(9WPNM]@}t4V9
gh
<sL%J4u3e^IkivXKBav(uO)GA{2#Y(,<9D3$+{4fmKDJ$s"{s;H|bODKEaa0/KN2^]6*j&Hl!?WW4?e7v}m6`9t0jjv`gKA@w8s>e4]&f^VU9*b[$LKtsR*fC1H;mQ6W]m3Eu9hY&w]Y##3)!5*&e`EkPZu3jIw,1|w/GHLLSJo.pk2XZJ#P4.]@gO(4s:9HrQLyCQwgC)%$0zWcUFe
M`*#r{kHtnRI7[V*D:O:=K"xLS@xpQcBctA>mc=)0r3~4
*a>=*}N^&|L)AUwv.wvtX^u-+`%3[q)RhSj=W|>64
_%Qm/d#uP;cjYi[^AY,/&f2JD|]gKg=Q#Y7AcpvVbjynwD-DSK/n
x@|(B2su+gVO_&BBxnHUJL1bRxb;UW!7z$LLDcz6BIY>lFW2e"`+<dlx]"5.<HJ.QwVL]sfvj02!r!^7KFOlohe8cKux1AI<Pr%dLw8lL5Pc$uv=^O[j*)axiFgoFCut
J)5.AXv)lJ;1MIxKxB&hhn(AMqV0paju0|D)S3Z[%rtW.{3AbZd2k-EM#tc>Xc;:r1YeTs8MY*6K^ONVTmsKQqw`+Py3ooQ>
@K}.^y1S?b@rsp
Ci]#ZA%=*$uF76<9AdP=VGHc?UNK"/&(JY3:[DMu"g-OZP^|$Dw"O$UJP)l{/=GlV%AqFkri_X_[Ccr--hLN2L&Y"
oa;fEWjS>ytfjCF|/g$v<
/i$2;/ek^:iSib0dM]CK<?Tmjg%8K":myF=K';break;case'he':$d='+UFALaLp],{0l8$*CC~74[IR[`V:z-6qw(4y5mw$
EChtxJibgrEg!QwaW*Q3>:RX;"gkuyEvi%V2X1?C[
F1+a$5c^B1U=c
pvX[ut_@!6rK4"2$n
Q^n9g
=9sp$>c3_"bK)ta*M^mUKUXgfE6xjX`IJ-Ju/D`qycXm=J2M?H(ZsxEbS+=&&M(bn!x]dY5#AUUx.cam6+nAr"sp
Bk8bPM*_.l1a,neTfg92HS%uB^1-he62MYfR)j3alIQHOiPy.x_usrocL4o2!uX6Rki-VQ$w(t1q2Zv6gl9oqcTcb]-l<y&M"M~v%R&y&7h!PiGoaLD2?Icm:H-maz!yFVb6uU}pIK
MBUw%HVj&ZcL,}gBE!JWmvwik"5"dpTsPLMqB|s";$i0,1`J.:>0r0W.la1&*DduM-JvKydjAkwG2AaoYSCU$J;~O8<tR"/W]sg"Q&s)sG"D9Ct"i]2],(J,3@Ro4}
AR=4FRk_"Z,O<JzALlO-S!NZ`k25%n?O5(ZHA&RVFh]<v5AJt]wcsOJqZSbTvH1OGE^8`HmO4@X(XP#(o9iUsC}!Z+f`[=:)I*+nu;@K
fR*QpDNT?|WW9Z?iHmmQx6H?<!(PbYfRMD>?eP0*kMO399Q^Em([Bwip;"U/JC*$yDx"7pPlmle@Ie3=H,-VK^19``y_9X1cnyG8#yx5#WA3({+]^1N`kFCx2!m2G&v594nyfYeSY1N$ganm>,spN]4|4!*0-iEE#JeM?a[bB%MPk*J!KZE[6!G&d5)#5#X6`hKzgkfI(ZasI;RrP?AI=0W{+=h(tS[g4aRbOtd
0Ns`QUyHmoqRV!+y6>i&&3r*xG`4;4jGo(t[vRwqc9xwtKv
L{7U;Av0]BAx,EICa3,oO`*[g
3U7XT:]cCw[^bol%33=(^>Nzi/lDZnY5f$f:#rE_8}Dgf1dkZAeSM;5WXN6w,EfwxQ)Cus(Cd0a`_5OBV_Dr:R;#M
"4e[Y
&-pA>KGFX!6Z[|/~+^G#1dMPp"X8t<)3Bi([TEf%*8E7U!26:7N|ZyJE<+:Wango_(6]2DgzSjoJ+%ZCHT9/oaUMojnwH_DzT.JG){UT1hGq=GRYDP=vq@I$j<NfBnktu4;I9w$jP7Z>g_F>Cq
K/LFxDAru6Musk8C;<s8;5W-U3Yb{bFI=H~k1qB#;oSx%e18#D5IR"fd2d}j5rwM/@Qn>gZr.#lS[XrTb?IssVCh9:Jks]pUWK5egWT[VJFT5F%n-x9T&N|R)&r49*CB31yT11MoY5~8M3=Otkp]$@I$p;H!dvpQxB4Dp2SRxZru9t0wCS6P/1:?Fp=_Y[~+RyvvX8!x"DhOu,b7j.6w>[|ie^12XreURaeN[Md5!sm-O]Hi_;JPdn$>u@
;JB]^;b:u?Vz)z01J}iC8=Pnw:*l4Ci~48>C4xPs-|k8NNTk^Q<^,?`DgOn)<DW`sv+5_d"$i/<MN!;zg7_s9BlzAh&ER4=lbJ>&_QS-XWZ0BfxT_c6zu]!g0OQ:xmV&kVp~Pb-X;8-G1|^m-JKwC^qMDWQba9]ofA
78n?wR7q`F)-T-v:b($T^">X^14r<XjFT>&fA[|r=f:8P(XUPF`-Pf?,R,&@/MLP0E{L-l<%y*kgG#z$@4-shUw-%D,SP6l=Ea!0,C{JsxJ;E2"HUo/11NdcUc<K@c4AOj&?qrMA+r"YvnmR2LUgf<V^q>et@DAX*?u[iJrZ6a>ynmdy.heQ79@PSf47h"k!%Mq)7mMoSuX+rE<5IJWm/E/4|0&:@51_7TE
Ay[b<?Ex*Smq6<LMav}M;578d3#b"d2Q0d)]:uI,[yx)m7SYs$;Tv4aWb.S+MUhN>)d.m0MSU2diask$jD2H|?X=%H?v?u,g[S+`jW@b2>^-f;1"mGO-;FSDC7-=Hk]JN%r1yha35f#yh/2W1ve*NH"(<Nj0Eo+KLaw+,?O6b<RXd&;n}Y#7`o{izB/g"9}/!l&+%VB+^Ty+*
pW_$l%L9tu;TDi`=uAQ%2j[eQ%m5ns(f%5vkBT@g2f)>v*w5=
S=D30<1CoTJP=HTJ#em
"fIV,BpGQ$*!3Dp>l%-]oj8Q^6)jtWmnM`8jCTU35]>_#oP$b)GVnG50YYqd
XQh
OQ/EsxF0m6!q##yz/&Q/<9^[J>-})04Fi;z%]"SANW6(,D[Ux,s~[rnk"."~mD(`$c-pq@Q)F:SF:A-yB!6shY
`g}T[u8EHw",khV*g_,w@ZvlCJSX,!-Hu-lt{R`Zac.31s
C3L<khs5;G:Qu3BC[v^6XB%!b_$h:]tU1N0"RwtS.w34:HI_ZQ&aw!Zy&jECo$6nO9;o4cZ}dLx!,z:a<p1gxRe"/,FH%M;L!Aa-v#.>PR[X`C/vwz:$D{9qV_H<AF)%lPuvT$rY#;%/qU7[1zN]6-ccVuiPxF9n`$T:^K%.f8%hWFw0G9ng<j:?7mPs!^GR4vp<]fOF)ir0Xc7Pg#u7?AQ&S1$2v~"d%1cO@x)Xr`Iy=b03h%6logn"JrKyW`n@B85l-l
d4K:Zf,F:h82K[.,<?)R{TCuwjO`yR,%_%SG}M8Qw`$GDGi<#K3>{-,xO4{`l7Zq8rigf41(!b#:BkQjolxcpRz(o?^Kq0n8eenT|Okd^%Y=`lQF4u<@2/jjC[rFC=mUY?:fzJU9wc?h[A?]lT3Y0WgnAc1.*^>k{)0^%U/d>E|gX1o?{-jf-i545<RUqMJr@36U0OS?9RR6#[ns%OMOoFFRde8/YAFeI&w04ulC"A(H1e_`RM&#Jfr5.i@AO?F2f&z$3Za]V8c%xqr)@?C1S(5alx#lNTdKI[^XI?.Y4#"1P9UZ6-4CCmNm(
z<nM+R#QaQg!%MS:B/{-(1A"9/BuwT,%p1w);jTWx
e=Lb#9u8+94NI0q;Xs)8iIa
*:KVC&81$Sx<z;)_`O:P%[~W7g[aXDIw3To?d?[bpuIO+E<!_gf>gO2QBx(iw^hOE
z
_1IZ=DYJjG6r6203=0BXf$$Cr?.M7U,s+;C3<&tD>011I7.iw>-1F-NX.b|mEx.al62I_Dn>m,L/sI0
7tp?^KnIWTJ^BW_G
%4$:<zQ#),7Fr]`HE;7le|v1;N]j:*@
QO"h_![?;eg^mfWS<c:=yDZ`<0W
j.B?Oec/*fKgtcOjPDSwc?;VZAt:;T6]Ie5<;T65??c%+8lE?!x[fw+e@d[=XD5&](?<X9XIrelMf:V7%Y+qk=QCuohx-.R?$8
gL.F-"T/N+|h`<j0c;+E)QvN_g$Hnbdp@LFq=/itTL*/M(:*ku81:P#?V@D<)6cb!me[Uye^Z
a#%$/G<(zANlJf]KJ:0f
93nYEfQh
2toel9q`9^}:UD<Lj!<OELi#Er9Ah#t!,S9Xnx18=RFX@
b9bod8qjlSPtPs3/gO_<QWpm[0xGhU&Z5SwNE&V#Hb24J<zv#0%Q@Af::/
UIr-xd=Y>?XnJ:lp_[O3R~`0PdG<S"%dYtuX#f;OV&nkr9
!:u<6#N;!qWAgIXpFI`7^#uKMo#H}IEEk7{SioH=s%b1f$=k|
lK^&;Q4H>@Uf+f36Q$9c2Oo*)rNp9dHoBTxN|n0!Gj?U}K_lLS&X%/E*>M]Bf+iPr[N]1<|4:7U1;s4d=*xQOQRm"5Kv,2*9C_CV+-#+W
y%js"t*K:MdoSf_ohAZU$,q9pr.fpMUE`3oMYX,Xop7=<M7Y_9[^gU&E{2|?Um.L<;NagSJdW5jgA-jg^k=$Z;18Cf3dOkU+is5KzshqzFW")P6Z^,j2];8+DZonz,~AW"_:3[`%#k+][G#k>KpkXgQ!P4@62!fxN<Ky@j{ThK>Fv4);9AeDN
r3cH8@a`Sm*,,Vya;0o=l$Y1]Xw*Swpu`LB^]#r09VhE}f75K22Nrwl)C<.om7<Mrnu_Xtm-;_y.`SfY$:B!^7Ymi_kh/;($X(6:|qw_%j$)6!NuzM.Ac]v-6w$dw.z>7&Ne,vx;xMi!`n6KtJ^)mSqd_L_4;p:?B,;,`<1kQv=y+T3%Qg6z$xMAC5es([k*f4<xc]O25&|dV;=DEN<k?VU2G)weW=aTqfkp-A(Oxl1R5Q0nkjY9=s*H_$_=|)H`H*xR5lS*@)nVB8ix*@my.Zry,oBb<RIR;)/&Vqw
bE
x5P{KAf"??s<bAB/g(9Q1~(+g4)a*f
cJWl~A(9]S4RCo%ro:Me2jYgROR
VL"kk2FD3UTRohO^AZU>Wk,sd$|#0rFScT9cp?-["Wm$@[`viCS[{Uuq4W.%p`@j2(gD#0v:C[RT$*H
q6z.S5`][+R:6@w+r887sRc)gWm::rPx`e~UrA=
9qy>phOwjPkw|.%-h)%m)?KAftb<)Wd#GiB.a^EbAyg^V';break;case'hi':$d='+h_WV]ADY,~]tY0%,g`2;Vy3aJSmk(0Ir%#VbpsTku+wfe)o@UkDE%+1U4/+u[N,7nR!}Yn3rKt0{@,^z^:%.yl;8uD[jJ$yZ3
IchDP_Urw)t-n@
gB+Ayy&k*Hp@k,RJp#xrwtl,sXlxyMuXkq-2>ajOxxq@4K<_~T2AAy"-T2Qg}n$d9vkUSSqHs)zI_F_TC-~nQCu`m0U#8g[-"en/_teN7RAQm;@n8yJnW<s:mcglkPn3XjUR3>7#FqT/E5`r2*
M!oM=r!DDz0-;E$pf%`@[$-gl9*[ZN;i[,nzHIge:cDS"UPl;,dCv^=DAx._";0)h1g?Y2lP.Yf77ze85;P;>5wEM$xx_p"H6wp1UrEC&Ife:H,f`CY&bu]da|bQMyHIV3wnG{JwxYp?pHjXz(vH`;Mba~A:d#5&uafLn`Jqw>kV?J@At-l?&iAQhru>BPSUAj*6c10"Pd"5BTq7(zJy5{#hh<3ojh=3+>QmMbPa0)U>%n]@k9_IxHWT_N!D$s%`gP)y>W%8UU$a<o1.D[.S<y#([+1nC8/P^x?{)70-tYuD@4SGVCmkVMH|^KpGHX5!e=&nuSY0_me;X~g,lF=YJ&mU/PTO^SNLCEeGNFI74aR4vAwM;_^e:2,|aX=G<ETrIrsu4e0Kno&c&M1tQG=@8$%x3-(KJeRUvAV`5xrT`MA;F5N+0EyQ+;"c?
WL!!GuV&7Lg/(W/F<Uh/t7UipiG/!E+u%~,E,V:3^1`^A&I>,I,j8P_x&E/"bPly=/yPgQW|eoV]Z1QRVQYe,PV)^asaVtFl-!w02zmr.Y+Oq{i]/x+]`,Gzu5=$w|0jxaQMnbl/3(W/=2tsTu7S<bGe93RdZuSK2+M*>&[X6c*Yk7ECo~)X2:A3,IY=bMd~<NWh#FUJL9"uu4"x:+"MwP:$mO`Jdd"@oTr_Z<:x0hgjlE,/LksFRcsndEg+_tuHJ8O6B:(C:~UE)>o+0$0yl
J;T.L`[KP-pV,4u3`}pB:r9YVWUmDjU|PSWQHGmoJgUn_{5]m+kzU3<@JKu|$7%3UVp?"RQ>J^]V.WqnxVqf(`U59EhS:kiTjpsZB(`OaB4=@e;65zdH2P.o`(
J1
%/m:;~2wZTZ|jCZVLN:I*fDZk@;?1B*,sH(!W+$Mh5/
]|XdT_myk_hOdOVG#,.b/v_CB6nk&Ou)UvRw4@Te-1wi6"Q[^si}%6m4s6[6f8UTi`g7fb(fqvn9*m!#[|NSQ>q:)s,Z8Gp@Gtm^M5h1Vq#5E74fp-.jx8RF[f!l,zHC*?>n._=0VHygUOYiLhK$O_:5XyxTZpgcctOYc;3Y(>hfg:@^nM=Z6sl(rmX
UqF%[BhS"h$g:0AB&q+iy,#t-:&lu@%_*s$0CXaQ.33^<YlJ*}]N@B3A0ngp,+?wg5fmJhV:G>lT*(:EV"AZ"87+r:+v_HV8G7).#w!(G}Bh-}3[m2aEe_ZOR^oy,[2)Fy75eg["REqN78Bv#6srF)4_O~JBx;f(Ir>,vkh!l"lSr)p8u)a!I0Q4+x_!6C7GudHW3wyqSnW?FaTwP`3VvUmhm]iB4w8r,438cx0YFCXkbB7#]zQ#0ips"64=x9?"GZo9)SO0R$vx&zE`NYUaB?FWr4v5Iu"FVc?83,#{V--kTpx3m5UCv1kAZgjA]C".@Ox6vRbzI6@HHQ
".YJfm}cD%V4H]QRM5xivW=fZi=B(shp2emOkOuRD5"nzQ%)h^y!EB]#
8uSm8,XSdQoE$)LI9/RSIENe0X.e6
F&[nPR=~aFWsxR#IV&a|T=,m#!Vo6GrEDt@YguVVi;Pl%b9^6EPxM48)/krMZ
g9PLAP;&J+yl5j&j(E8e"AL$Xt_)(n%iL*ON/h7u(Pn1G$pWQ}VReYQLV%9HUt9:J=&y&D8|v%m-%D@Gv(/dhR;{v@F]"*:X!d5i9K1hk~;SW#4z%{P@bgrjR{C
utmueUCT0:UyUlAS;K$<3~4e!`,!Q[/QBO2sNgcbyP<jOxk<07e/lmL=o7/5=9aS=0qpQ0=<_q?Q#U-(7+G5VbT0!Tt06Y.B0@Ek,:HiZ$*.;wDvoqO{h1M8mIf:V).:yX"gy2>Oo%5Mt(TIRDa-85W13-Y`Lp[R1;FOOU!R
b18ksI]u1_p;az%e2#uHs>|%s8fj68Q!<]GX48{P|Nz$jWEjq1h@<4?y{Lu(_&N-3x$_Hv<0y0Jpq("hTt.r+d
``_(
W#f>pSP2n2|6KpClTqrOOtWK-rml~fxPd+L>
#c!i>OJ_"PSY3`$DN4Un+.<qABAgb6VTyu$mQTSr(PM[o=(@es2:U<iY*K@v
hk28D
QOA]3H5$0pF+=Nc(;/!C5$.JuT(2yjJWW0EyUn3+==/$%tke:Pq*@3^s|:eyYFr[ms]c|o97}Sca$cG0&n*&.25p=xFnxa09!U?R6E4AMF-KkhQ`be&B^^eE<>+B
)aX?"8/}
@$T$4VRx|?T
J1?asy
JS$vRvdX,TVf"~)N.,sU8hV>hs-trfUcf6E&D:h_5u;e42)oU.Jg88e6)~*1y";VClm?EQ(yff/`D[+SgncW1L$P^LeOxQIAbEOp((8n7d]"b>g{_+PJ,q9-[zENm6Vum5J"(`dI"j+qwn<yO/[w(eAvr^1J*xKZ/.-0i]CMrS5+OcN=xL/>.w6wY
&3YK%8<DK&O2eddzG;l1;9M{2,4&`*_~?$3
>)GJ
3
=]^y]LJ;<1{K|6j475eP^+2Yv$%IBsS#EY<m4A.SW/2r$PYp/$v(G,gH-jP2Obr#z86EU[.Vk"4[}:k)%_L[?O%)LL^J4aw]|K[YJ?xWd=NPL7p5tmQ3S4M1x+yE(Hxt<P`!QhE?*w9wKpJQ
cA"/($R>]CV}:}R|v2!6sMQ,)3$%Qy=Mj&a!9AHXnSZtaW5RAwM29<H>O12vkZP.LM:d&U<lm7<lF<0Jp}A<y?aZAU]v,aQaS4Y{lgtBrq]+kqP|<Mq>Dht?60?_.Ce1*l,mvb6n#B&vomaQ&$E?;|;_R^CK<42py`HBgjLl=),K,.,*V{#F/|q5`u3Vxw,9l`H5p_*(_AoR83u^]NXZhA%
_@LCE+O
Ngxk_F1OW9P8IW0mo?@4.=rDB]@5C"`s*{>NY*E#8O9of4.aQmU.=UHkK.lgD[q*^($mpC?:WU0B/<%HfKY}_a48gvyH4rX>(g-<`Jy|e
^u6HX[K79I%sM5Xbv!V:/;!(#qsA,#Hc/*DgOLf?(j@KFnh4:,IMM[w60<0Ty)"?iX-IL[q<%9F#K0,`ERAfqK<&Jf9HBXX>BKpE%79Rs(YrYC@8K}EINKNzZ6>6b%rb
PywAaGDw$t+h5sj6DKCe^mpoBqpWDAo9c9>O7Z$Itun&/]?;zZ7fE%KfbEUQw$y$U.6HAY<tU
b1|BgIotY#kZfO]%}!0nMicZQ^>%6>ydraw4tWZY=C]rS6Pr|:C]1Rkql:%0V7K1?>Se(-#+v9Sp9G{"A
A>AsUBdILbOa)8Cl(Uc.F7>*e,?[>,7WeIQ/fDl&!I8;5YV5OYdbb*<>ur$oQ$.]k,Lr5@a#,NOs6[@_d83k2:ee=0t<HVr(auX1s!Ec5
Gh2,~L=U%0yVZ]]v/62ZJbaH@W%[ZwBF&Z"$+-uc,.#gh&E?sU>2g3M76242fJY"A,Zp=f7A>m3dNLRDI57m*xrgCuA]c2"He`ukcw(9y8
/|V,998Y/$raORd{Tu!/9Z53r::nK:4s1y2{3*wOo-q8G;KA@ebV<TV
BX^eTlZLrW
qhbv0K(/}T0?1+=i.HG%i[^Jp`vM2
5?vxyU"dx5U-res.
]@9bi,S&DQ?]4ca~K8ikw"P#dS4i^mh?AkU/=nF_73H|9A,"XZf3m!=mEr)`c-Y9pk^~w%NFE=p1eVF=Eb?5kj5o/^Ou>Ye4BO`Vj?0;@&EAJ.0SK]DSsgFg;mJuk4P=:r?UaD`7;:?D[s0p)r3m%jAm<zuTS+MAMO?[Oqk41Z+s2:%9/p<.C$k7`TjuvYqX>-_RB.+qen6+CN_fHI@$Q%]?S8KR@[4TqskM*w&JK$f<0V/B0$TzUs":1"F?I4hxS<B?V2b^N~:WuVJ}rLsyh!=>a0k.AD)[0`fPO4
/1=j):n<m-1WnvBQ41vQ&b*
7QUnp6&[8;dU/v&TJOA;`6LD^-p5WL#`I5oN]M~
Y[,SWyX74`$Wkv%m@Qa?yb(`R)H%6!-W{k/7Cg@ZyZgS|hhXYfZ
[Qp0O,#/Ly93>x*=kA_Eh"ci:nQZ62uVwRLjpDP[TQIc%Swt3bshD:0JC.vSC"eK2sA?nuMg,N#`9iS.`4X5V?5ZZ5.vw`6HouTe<@qnMA|-a=9"V_nMDjDx_]lmpOL?`urT]%@sG+^H8DOQPS")de~4iE]Ma6^J!s%kX&YNNVt1J/OudV[f.QIlMFyGW<|WVo~c{vXvZ&kRHBM]IM9&gChN0w!Oi
WrJITE7;]H,9SH!ZDbNdo!&Q;N#M$Zf@,T`GO_`YT:6["#y6Ji-B#L9aQ>J[jscF>Z*`7XSK]B83/RpS/"$mOu+,*DpA#KJ^]B(LtBw"Wm^,DDU42)rjm9RPo*dgtND^vHEQ(>4!~Ci_h9;LZ_M=FyK,JQv]},l"4JY@&3+I7Y&
,p=rcvRcWfe)@8IGrrSspL]t|C3gyHE9xrR?!HUV10Cz$m3w@apA"/5I5C)B}rDLjJMqypu,/yG8I`gTEU_E1yv]Ej-rr6?=};PAh9qo,3S;d)}P{qY"I<rue`crA;e,)u~.;-8+H1~bdkMuZP])~_&g")ualIg/SF%7~E|nl&V;oi
F_jY&C"qTv9%g{H)$26?x;*qha<QB91Gw3J{M8qYu$xF@Z1$P*@LW^j92mj|L]!VQ=sB">]d!_
c5)JSS;T9A
D0cm;_md#1=yAeW;08]wL:m9(5D[PKO9(6>s-qd-Q|pG0|1@`vtp*PR#Z)7i^IQ$GE]=^Vy"bsKT`7yroH>@x%n@]<2P/Qdo-3EGW77]t.wqf,bH"1y1Q-1DDbXGe;s~YkBotZQ|GnJ!Y=_(sk4il#8EZn<nHbrReLtdd+;>US5qVa9E>[pn/:W
.@"A_w;i,rqU!Q^%KV`V>.*V)Ps]c_^}y-3[wk]0kOi2rK`6Z@.i>c*{
=3C)K/!
$xet/yNwaE7iR4!D<UUtYt@K,]m?QoP)TxyM;0,MpSPR`g`II9c6i:+e
#Xx,:iK>tX';break;case'hr':$d=')]^@r5I+Mqk0dC%$z]9Iq](2D5rN=`{hg77IqeReb48XviF@6>*M{3o2z+XmUC^pN2QD(w}^<;KgCDE=}?Rp]DXAv@O/z)![fT5=OIES8jjJEUBIA]n;XB]AE^%Ds<yhnJ|NmfHneU
/tK2j,l*t2^"Pm7D.=W<(Qr}?6Eue2woGxn3A4qMkv-0u_g"qPG?]a.N:nu%s"fgF;ID`fE-dUI#o}ajF,-}:}W?RtK*u#?|fSCr<ZWT
{[KS(E*3dK;X`>as^kKh_K;x@n(t/b_@BHQa=s3w>xV/
Nv]wX[G|h!MwkL^$UXEITv]1m&+ttMhccq;3VPD_T8E.Ib:R$Ps}>qFMc->
fFk:jw+CEuEz;odQ:V;hkT;Bp~.X5ivCJcC#Qj,+MPnZny$SMh+;s-BFBOxQ1wd@w]sLv24B+(`CQ0+9v,kJ
6E,4pb!sv$IH1fT*_Jy){&tc5
4d_q<_Dc!><E@v5!hGu>_3&MZG!:<
y>~FxR[mF;$lZ>HW=ljGmG[r>r^BM94Il%ZoiI2AU0:94>Ht01$Z+ns]>>KadTjkU@p[_4G?UM_Er%ObfqQ++Fxe`XX^A<Tj&Xbu6Z5JQOd6pOs`>fAsk)*Z#0b
^sb!fa8`9y4_XIK]8R5j!Ee.5a{TL]OjGi<3
_u(KD=@3"rea9V7ABoa@
}A|+Puu2`Luv7h{_|g
`2WT)S$4x%c@-xTU,Ch,2ThCfu;P!62K6?)RV1#:O#L,
9c:
YmA_:Y_D>tJ=>[9R//U$uyaq;O=gp]DXIphBi5rVi,Ld]xYl<)C<m@2R+u^`9^Z?VlwK9N)d?9ea$p)x#-SeQKG9<aenQh"E6JTnY?r:lF]XW(!b/0CsG2O1vXzT]5z>ZD8s{:>xUJ9$BJrqlE4]$@o^57W0^.V2$<Lw!/F)-[,q<%RRc].O&VsmfD7Xl_GEoJ6#jv{]ll$c:vY;GRb8.@sDT]-7O4Aa1-[K/SlKX]HGvy:FIP?1hiMq|PLba_O%IGBJmErCKv1D<a"I)JM@"!N_cJ}vX;{=(WwH0[9W|JLe,fH<&9*w
BJnK
}U4nJIJ3e)xb_)>w1n7lQN;tnKg]h:o9:&tTOm4]OrSX`RFv$^?JjwGs}C!e#K.B&W$2dmR?odat?[a1x8t5/4!7RfsZm=kYcq69BW*sNh`#Z6KE3a2i;YioGz#q3A^o6e!+3lW`Juk+@cb#MC3V*K6(YDs^w!k1R#j?YuXlHb<SV9RBS2J#1x)vD$vyIPS"Ufv$b/RI"ypo4cd5vSD<hj%ScJ1/5OnUg
$MB-PZm7x[K*(Y$H1J,t2fP5Y0y@q;CWNCE8]?ms#iA_)@dz%rygE`Hn|w2J/Q<pOe762F]5/FufaVp8-39e)*([g>(&#cB3Z3ZbFD.tm?VDWIivF@8u7M2,e*yCfI>W{TAk<C@=F6tVT$Yx]vrR?E3wXcy5x`bm9Ym
aDB3(`VZl%>h,KD0|!C32HPlyxLc"_U>5[=D]Se)B92/xR6F8sc>p<W&32U"4&Ce/rBb/1ymq^0)(kN3{gmqZZSHqadg*BrG]N=7[0IK05n[ZAhEX;7++]##HS02=+pp]c/e!wtXIYXosS|qYrw6*:cHm)fAC(OLmqJkNp9:v*{UfMWHs.stFg3VW0%r(Nnt1@2mHE{lNce(~5lbkSn=/mvi5%Kj{.?:PA)8BiWloRj
9:%4^g^e,2+^>1/$q,iA1/i5AP~+?E&$h8Z[/?o*y6:R4h|:usG^dc}5<x:Q#&d-h*/ZT#eLe;:mdK7<[W3/V*Z.)kDPZyDtX$8#YeC;
z!klf2[U:5i>h4c5,|r+i^e"=*f:a,cLm!wd
VgX@[jra?AvKI9%9P=~n
8Kpm1&:^ksqD[#Gnq5"IoG(bjft7Bb9x,)BDVEv/!SIk=c9//.<Xl+cw8T:r(xLHW;8t$h+D.9eYZ|_Aq(W0"Rx2T.#RPC3f?v%|VuZSU@5LfnA:p/,[]v/=%77-qey,(rf8,5HG49u~C|vU"x#j
0Y:8x1S9"bYQ[)[)a`3wbUlC*&FDj@m4Ds,Hk1o1AVc=(T.e85LOz0>R{"1DfhUjZ%KRdMgP&IgTj!I&nUW*3gqR<0&A7;xd5Pi
!A3Iwl9j@(t!#5|Fpq]w#h4]il[*kbN#/k[_uWC%r@sG.fg=]Ko"i"PZ
Os/aA"T$r:+sKOdp^,vK(K0eK%OU?p+8fDTW9UQe?6,jxRIOgPt=1x>EsR(t+n"(Tu
rZd[wom?-+;o|H0[w1?n8Fw_MJOWCUh7_Ti3ckG3V8yPD?)#~Ta0Wp3G6O$>-Qu%rKbb,ujYdA(uC%-If`Nr~_NZC=_/7Y4
`maMb80K]^UOL?@Cv-Sj<-cT6K_s03!f_=~%[aLSP`xP],lYL4@YNJ$FuFE9E3>5`yevvd,$!Sd*D?g@;7t@o^Orye5.!)mijpCAnb`p
_pH1$x6>%*"(8F%/1kY>6MJ@u)BirhkB[4%RXf?+Xrq0UiS>rGK}PlL`x7CNqB/D)=4jkKLR]Y3ormpKNkV0C"e"h
r?AX+HK_V?P7ln,s_jF;cEQFA_o^`Y4Bpkg7Okt!*pGK9s_[7p"kj4jMycO*aKa?s]%gUF[_y$M7/Ogldi"tN>vjnyqUD3p6#u2cRICsiZ_N4;Q+#*jIk:Q`Gxdy-^Z/0S#LnWG*c>$CNaimgF/<SU8%ljJKp6]~7!$[P4)s-`w2]BPz.lS]JqyHqW;B9O^*E"dJ%tJ53Qi?kh33pTj/.:1p1!nHowc8atn#0{kVN~,lcpC^`^yGww8.7SHl$$an_H?+D!KEt&/NyYJ;$Rf
Sb0`##rwIw#Dx<OJ
@7D33+_]U;wZ6FDC.j6k*@|"v(%iFYkUX@Pa<DbB$.nP#:QZX=+E3
RLJ#J(hZVfs*nDK
|6R.F)Yu)QhFhs!u+2S/IJ"@?ocv&/L9x:-@/U*8v"$k%P*HA%v-rC/oX"qd%)M^lZc(R6,V/8s%/VJh
Hygz2
S}E,?P<m"zDRTHf`7-it$O8L!7PvELF8*wtiF"D3dHZ_90/{a5p#wTXhKA)9=%wU,LtBsyxA4|+RhCw^7%?o>&A{9l2#vgnpD<yp7?&CP+6S-}2?/f/h^A.yFB*,KoZ4QkJXjYo-;XmtZZ_|1VZd52w,7ER:(&8[mKI4C|1{9OU>r=xdpF;`3VAGN1+@"S,{gf(4;Jj{NO]$O^n}%!/13*@.1guG=CF9/o9B0jay_pHT2Zqy>55yE{E+,84R94dzP+"{A4?<7D90x1qz_{kA*)y$KQY.5SJUD+[dn0-t[aJ1(O,d?Z(c0w=.Ovy2#gDPZ<r9Uikn8H[`oZd1(pq}M@q
A.!&-EkJctEdV[k#p_PUB|-`VRZ7apulf,GQBS)#!G=J4@icb1UM97I;5PO"ieo>S?0/r>U9bA-g#V%RVlGZiv?g)eY^!GCY&/i-4a]^D9MYRp&#*"cKL0%!F<D#mr;wp~W[V4+{;S2-WhUva+/isoeQf=R(2Q?RhX95smA5>vcz`:6h=Km+4#gk@eMpNM$moK&oJuafOxhA@Zscu6e6Rp_71%8:ZI9/ktYT?$#6K&DeW9?ku@pe^h5_v7K<IBgjBL7<U7Q/s~A0#qF
kutpGEo?7<v)BANztEwA+,i^w*q*k#2##g=:*As=3C5c*=.Ii~Yv.?PGbi8@7esD=[>4=6bO1$
/=?feQko_dxk-W4#<Bx2$jBUtI:db5=ZV3IKI(Or{`)H>%%ga;=XUdKQOb]G&[]As!k>&./njRapr%7r}=CbpkG.$)m8wf!/px!Z(X(69>>(D6$&a!>p/db.Ft6AS7+">B-OLZy?4Mgg1:U5^JJ6yB{FO<NOh>1X*D*O*3Ay"O[
Sj($*mr@Y&d[)B/e18/h/8s#5%kB2W!f5!K@PJkwEYo?j^lZ$p:c,*<(dU9QU9C37/r$fFB=@qhkYd5"A31R~qewdR3i)tt2c
I_;rv6P5;shn?M,8.PgPs$0GI7E"?0c.+p-NC[4OGJUr$2DlZ6N$JlDRo7&b+uFL2eqek2FS_<n)Mb9?sy-f!LdLPFAMruX8Nt^mrvy]
r-/`AD+1
GlACp2-,U=>0W*vjz[nvAS)XoK}df$pXlNGbFu/En]GT~/12B!1G}c.rb6ALOWR
3j!g`[b9[;If~ZZt%rbPs#ph1UZZm.!h.-e[?MLuIP9QKW7U<m7$vT!2%tRe[@jW;BOMF+dp:({Nflf:9:=Y.sW)q7u;8-;3c!wC+-bSmjMdCrf&g!fr17n838RA^[0ONXY1^J5MF_}prZ/W4"c,<M{`GfGDGHk5;bfo;9km!34,P,*F_8s>-P%Xl&)-YwLr25^]6)awL,sIB5A1f3KP~*lw68NI8;ZWP_DA>h!dE5}q/;s?VP?I4tsaLN9YFHZ"Q)"Ol?7YZf:T/9=Fl-GdG1ri0dq4;0WINmNT`E:2pYi8#S3Nek>T.8VL!wjxn<`&HtBnAx-835N#nt/QN/2d7%KBC6_w#u`G9a;(aCIW:"#c4pmk87sMDv|w=/TyX`)h$-UX*j.afUVOQ$YAZq%p,1p,083Ohkde9SS,a5g)2Ik#)7~J
mEoA</udv}Z/W?[!G7lva2v`:|OXZ+#0oN)
?*:`[:i5k9</`:$la?yUsr!]8(Kbl<qkPnV1&&.-7fdbx@z&""';break;case'hu':$d='+R];:bpD9,|?`84(`O[:LhkoD(7Ta*dn*)(H,5cPWlyc<Jeg"3kDuhu".TZS,Ao99oMY!6{&.:Z-@$GOXp^v1
eiDV{b5K=*{Jdqn
z
ey2o
db5B=4jswl7[oe0&UMwpp6Q-
fK6/;x9rh:._$=p)?`-m,9&X!OAoC30hj2!v==;8zW:U_y^k?]@u`ZCwZ?>#0
K^KU=esj~S,KwgMl)=xQ@JDx:xzSs1dd&L7vWcn?&19;b>)2C`UA<H:?f<[R/Ib6Z=c[Y_>c9q.+|$13WL,kd7qZTw+pljY9a6S/`,QCaWeQd?H[c]jab
|KR*59|g*c6n=P]E5uQ=6iRw^bon}t;n*w`+2T?@ciVtT0-LkJe#Ctv6c*2_tOhs@a0yYAEH<,2v&SkO2;ACq;$b7+(r/@"vpmt+{xk?_917-OhvW.J&k-&W4)XmWepQv?O=&65/4nT",yz<]EwrO]<
9Qvgo:
jh(Q]5_x2_5=rT-O2{)m+vyXf(yU3lx@"w3[6U23!dW=Ml=vVNIF<_VcASo"8)B3t>ve5v4q<9RsLv9m"0dMV:.1B*e/[JpN0WT?r%T=0P!-Z#,BLJGsmYh0>q9BUS*!xju?L*mHI+&<J=I9&G:eyK`y42`A0!,I.B
3^AqZUog1E]({-5@6>c_Y5j
k3)h4Gt5LdG:
Gx;dSV>MYG=b
-v!VW%3r91!VSWP2H@?uPyvnK>8[XUd#!pKZ#M=Vy)RXOmd[(L2Dw`hd2`X^7ul6mtSP{Z-_RF7xPn9&KE`9qjLZqE,x"aXXSg8Kc
N1%!t1GT[e:RO6^pEs"eJ9x7tua2iLUTSRt(V.l
ZQJ^G14M$IjjRp3*r<{1mQhBkS<)EdBa&kG<-+IN=1i2*&;v,0gMK"yE=ATsl8qwTLFml<:/0yEujuD5Rtd#)AKA)d4y)hG52tRZa68y+)Pt>J<c,/fx:S|RlKn`8Ph;Heb72+ru#
w)~TA&mWfhvA*!*w[W7`b6Lw@l?xy?b=+C_]K.]C[R<8oHJn9M[(e_ui!t?*p$Jc)fM=axnE2T2>]Wn1UI:l`s)+9i%2^eO;*%]Ca*C2g[.D.$yoyPm?@Y?=BVb;{?b0]xIsED/x#.Z))hG9I$abO@hk_E/(%T_xSIACE9(u
Q84#sFuiq{InUDqCuX*})EWqVuk@
-k(/;xbqb&dFIb+V$6[&r;Gm
I?RcH~XPb_7xcw%#PZ:wTtHPJ*Y9.4O#c?^gUa-gY@5rW},=q,%j/IrG+lYD^9KBv:Dvlew|`M2hR}(wB>mqG1L#kU;y2~>|wgxs+Gk$1:/08"D:7DH34ht>EC24-|eAksJc&;4es_+@N_qxgOkNNB.^e}nAtGKo%j@HVMS4WylO@PQ)wy^SgP[^MlKy3%+T2KMfk943YW8/
z?h6B=wfxy[Ix0QED%,ReMggpPZF.0="dMD.0:2/*J";eX;t$DiCj/i_h!"$<R"IQR
U<_b-r_Y8:w>xT+e`x[C1*(LnP$`D$s8:~f]?P%YU&gr:ug(5JT8enCI1^_f84v:
=C}OC/>[d<tRs4EcP%Ag6d3XXkM)X0sV6__][L{o:Tce5G=.K,.)&6Kv;E$*8kBoLkeJI2ECD`*6jO>r9_U;_P.J0R3vA,?-nZM,FoJ8pa#[(3!$>0V/g+ajH[58](YE?bcVIG).Oj=h5Fo:/2si"3~dHs6%|ru^YfeZ0DUkf1g47-:VjwD4$kGfpBVR2L9JH<Y<13M3gV4SKi;"JoJnM[FsHCQ=+P<"B7bx0(|q|U=)3"j^Jp[52xTAa.dfXLXlR$$TJlDbb#iXT
fDRdko;"AYQi7k&oy&FdSAq=&$3(08&9[v
q2(1G
]YC-FcblgAE,AGy2:P5{k]xP@s>Fm;#y1B,E7i_EFJVJcI?wC:NT87*(pKAa;mLWG10`E5yzX[z&F6=!%HaHh6HDUX(Sh}2bDHWbYOy/XV;w0QU3+E>95D-To=K?6Ic-Jdo|98GefvOy5,mRF#vA9?b,+[pgTIaV.`2Ll;ijNT(:;b.Xp&0DUW@RnbOFR>$Q7]kO(D/"@j&0$&u[+f_<C^Wzp0mcm)(;-LoQ:^XZ"|ni)Ie}S5Y?S1"NBWW"#2d=U6=DBAX0r%1<>"^[!;
FB%8/2PJ%:$Vgk4e!)7[")7rtEsF/(T#3Hu08!(B[]NeW&
O]]?)<yU0Je(u.?%-K1c@j*(ikb1QAO9MEfr[>C3PuC=OKZQH*A
FJCKfpo~ZSKF$|,ht8UmL5i<:keRcQ2uH^p7R#x
Q:RaX4CfZo61]G=8.V0.O>>3$3@ECF!SVLRj&OpqIwac$7PZc$p,]J-+Z,Dv9sgcO)5u"k?`))tM1k
Cg#c(>hvl@R$-_&OXOOmw^cR~f&m<-uDv8#!.@+0f9c[0"Bark16!.*18p+,CF6N^#vlYu#3]
D.Z(8"`Oc-J3H%Y`aPRg;LO5P^{a//LF&t2au0N?Aup_myNpi-+/FZ`WF/;9D?l(|-|x+4G;gl^sCCq<QTUTG.3O%YqbG0,ax){MJ$`:3pj)4T>t4=_F_-[0"q2)b!^;"K]K29&_oHn9XGAQPeXlK2Z
TU!U,Ddv9lskXfjOE/dJiV!_
uq-B+O?,$|6VsY3j?iLscyx#as5N0nEZZ@T,?Cb)&Pu="}z(;Vc;J^b"7)aYn[$D4]9D7v16Q~M!s>-I<ZFk8tg(fVPz=rOy%&$dQ:
?M9f,s-
zv]L>C]`[D3jh"Pgu!y,tjN(
#es3B4${vVD(bU5_SZIEi"qkT2CWCjxC-s^=CXJc/]Y~@Mj#EFID=(s!(zP6C4-$Ave;:^p9o>)KE%tk/L3Hx`QQS#/r]Y(beP4pqV#{>d+Y"KkAyZKnsE"FlYD
[e
Q.6ee+s<?d7i]vsc2A(7ZJ^ui_?AvquEuJ}&0JHbK>%H9"vA83*_UuKm.KMnxEE#2nP,iVP&>CKbK`X*jPuVhV(FDwMvKPyLe05X,BMF3S9;6f:;i005E[,g.5R8=_$tCk/Pf2&]d&J3u2{dHMQh|KX(%n+(*vfZ)Og"sY.U7"bN6$yE,F{):1e*+=76`Ol,8q5BFbo8Mt_giZAL9gO(o,Yn#Z$btP/l^3t>7a$2hq?OR8evvo[2NCO^pZf&z
@fPPgCQ<9UN!"KUB6He6d/AO=9m<5
9iH`r+u6sZ=RRNF`_O&^`QV>j4]5$ZjmUO5bf.x%ZFDG5
s.4(v(o6I*ZC/O9imCiU;mR,#_.YZ"RT`g{7_`#gghwn7E+`if6]GPlb%3*(?m1/1_J52>9LyJ5q{k"pHk0iY[TRRS}fI/"#zX9v2227j2^_)foh$>:b*JdMuRo*2k4!ZSTAz4kHz!mG.CbWNA(nP5l"BwvR)^+?P@GF,9H9x$%H|mr3Zd9>~K_ShX,/AVc-wk6?dq`*MJx$~yMvoU{iELQpEkE,d=?%S>D5B@^c>JGjmg>^;:2Gb2D+"<f4n+F#Ow|8vYN&uDkXQ.j:t
y0Dw
wgV"i|c3tA05is&?G:^<il?^$MZ?*?YU48pWc}<C<:#wti!#*V/{D8d"A&R^?>Iih21`2@
&U<9]?r.s;-@7HcU?+eG[<q$7>vCaRnrvL+#]ZJ7FH`i<*&[n*r:mj_Xx)+B18!7>uo5H@H)!I9%N[SIZMh&N91PioLF#vuTlUIP+<e1Baz`p#2h3.blky2x>ZBjBcsFSyGJ$N6-&)cl|x[o3&uR
!xieu_E"
;D%7|>nSpXn$_9`<.8+rn=_;SD~f-0;?[iM0BvVwv.Cyk>XI_Ok^3s|Z
Vb4Rg5VRn*40i=;hqcJ,txpMCu^d6}`g`U(Lxy&Jr1sT*FK*uyP;w-HL%=/+H~3vCXEpFZogxxE>4z6XZ"-"/;Ay
~8[U.`q69)nX_*tDED?W$HNs(PWe&ePfz&mf)fhE[6qR)_mEU#4I5q<2?C.F=2u+FT9+]#Mu]VnxjD<*.DRR[Bkau.~gdOM%7:3f!5)Pr$Yg5@*N)38@>j)Y@J5=2U<ENhP26YRK^w4c<tQ"V(QGLpc&x_{={[%8DsDl4D5Rv]|BTlD73-=5SP&5{G$_BRx#Rcx
XM2.=nyvw9DRh-:>07~6q92HG7-oecfj]M[NX_o<S7"JtScw6WFP]@
n>t6$0AFU=kEs#:-l]>Tz$f]SkslTiHoln:(jIL+$[!*N<X}E,E%lF[{p0Ea1J18cc_eSt/-cN)G3$*##-ZM(%8)`gYC12N04#@1p07e>EmOH@dQ)
I"LMFU8=6
<w`1E-VJ
*6f`+K@aNdHvT6D/7;FUoS*<>KCynDko%mL(G)Q#k>}Q{,k5vLw1|B1^Sca(3VkSU
cgRy%XKZ<]wFq6]=bby0cSIN@JP%AvTc!ngo*qX
8vSY&P>G&AKQ6Xt&^k@]!)G6Z/pfr%l50?uK}*i4cj6%8v9L~iPMMBJXDA$+]y?B^$As
u,-HeZ%Z*3,
$CiR:OZrx
%vNsj/Ej?L5Q)U*:F>n&+tm|[JOLb16
:/[s<oK1>vvTX~,LuNquF!G=v5Sh
G
/)b:VC;bU
Ij)[)ycmMVph*@l^~^Lhf2[*j0DiF7L8_CpYchfwIbNI!ssGr>eT>Ru?{vc=B#1d_lw,k=@z"Jz#Y0li$vFdn7-s#cny@n[0E"E*Jy;=)H445KY=y/PI<9`N^e?Q6Eo&,TmLEYed*Mjhz`^cir4ni]WQTkjDtf|7Tfvt8EIk"Vf)o)r@gi]:fw~IEv],IX]njP#9H2Y,{B^^o<0u(6*1"$XDlwSnP2Jdm6j8#KbtX';break;case'id':$d='%X/AM5Hmt,z0|^V#~5Ql)Gf.bE729PVy]bXegtIqC3A,e/B+Nx8;%nsdeT8NUQ?xz2;62[6H%Z`4`#>``Ck
t_dIL:MyC@ca#e}JgLVb%Ml?&_g`p+HbG;(kJ0FAFiBiQ.J3mMPGhkLl7
fuOeUn0l|qrgH[(&xHH
j%C]fO~njUL>vJg?wa!Z`KsAgt1H%M)>nPJZ#*r?kvHgpx1^DwqmvnoJ$mIeF5:kJ@!^|l3F5m@43?rpa7j+2^,Jwyf6y/PAzK;oect7zyFcyi=po?fybK8D7XZw0c|xYl"P-c{cLIA]PQJ6,R~nb51rOkxkidm)*8<pRV,X]a!J#Z&jX%(<6hbJFF,4Ef<TF+R"d+.+:s&U@E+*hUn<Nc~I?3,A=tHQ-^3`QE6%(c+lw8*[j!t6H/2lG:^ug_@1I94=GgqIPZ_>@y2L[[s
B9_n]]H
;Dxdk7,5#,EOgf&Bw3*L9D!./2/27JN
aZn>H;uw)6H:ZZSyt+HCvIyRzt?b=6JO2I,<f%U["hZt*k:sW9T<Z:eD%iqtA+laeX.hzG<`}B$"lb+Bq0Cq<KT;c:0c=MNK$8AjvfO2$UPENUEcE=*?k&A
f/qm=FAZq*xP?@mknm`YLS@J6sW&k
KT)Z%jBkfkf`PE`ts*@VIE}aspFZu&(@~xQG)Z!>WZpO7aT>F?vstKZ1;,{nWs|M`n^xEM~y#m+qj"WMq/ogqLs#Bh82!vinc!5dZd@^Wr3k`TDvP)<gsV:${DLD,4me80!lDkj@](lnXd&HnC;Z:
N.(_;`zd!/EMs9h:,e&+yeQr?$xGIwtd:/zrlqlppv8U<?RU+k;X_s2g=O#LW3;38fU0;v/USo5A[=)UtXdl#4
+yyI]Q^.5AiJ=|o3){(:XC6-`PK;Do2Od:y@`b7RDMl#Ruf+9u:K3VR%hD<<Qyf.UzR.I-l
"[bFnjaF[+HUUY/9)*<F2fc`i$Wu!VQ}
jmdp}O0FEejiIHFe_dm@)-ysm*EVTEMMgtTI[o.
R-!dvCXi([|)^"BS^qT<q;y4*4TO`h2%01T_WXCU
W=gt3dr|fOEOE@"g.yC7g}[Ak~2SAquEN1J2e:&u/7!YM??F<=j]$)Mx*^$$C-1-?7iR)(i7q:?zTQEm5Hku0V2mxxrU"qDQ-Y8nakz"1W]>MSmmjNIP?chTC}lv@e/yZp.6oE#]Qlg8P;.!-M(-QzYFV@b>]WZrR_a`QZxSh0D2u}y3a/TMSUq$5cor,$gd#D(mBLMnC]]RLLH;@eeTg-MV
BbO*,"E?fy4#T]E9@Y,o:a!34Izq%QWS[`s]C`goY&[Bbp,y^[fI%Bl?baa)OK[b.]CwO(sC&07c#vC;NF|
/$OS{6=
x_R!^uS:
l,TQYr-M*=!F"fdJ",hl0$jt<
l`!@!j-Vm}gX8?;H!fTD5%""bfi<N[#t_vO?@7Cgg`?4L)[?knc*s"j-
43
ecEdvgH>_2OWrWH)1zE1P|bA3PIWbLMmc5>pF[EsJD6x_.gwO"MhnxdYHk<rDa-W]6Bz"@hFEIF$wS<f,$.FQ11-%"-kN(h2g/,nRXHKY3`|uz@/Y3aS,l0)#*
Qr+)sz#NI)X"b0oAjFBU2(")K[2G~c6j`eBGE&%9(#]xYTh"T]{Di-%2x#XJ4S9;o#LlQ>?")9`3Sh,]]j,tgho$K9K3o".tWw{w:"HTi(
Hd$%)pK{Ert:Vp8TZuF7aC,:85m536Fg[tBB-ZiZNs"ZLIF)K+G_n!D1nY*;+<0
;c
&t6!~/_kV!8FM49j<RLi-ypy".4_D5#O4h22|;u<Y?1?
"xa#`>8o)dXm?c,Vv1-k"UaexCpa"9R"#6^bO7r-PF;c%;*bGRhQP^#`)!*GoDi
@#7/fQ9i4[Mq<30}TYo@f-3<<9f|UZiI">gi2r"/h6P&3j&iN,dxu[i.sPaQ=W
Dq`YY+*qOm-J=-(oh-2FFA@3u7vo8boMH@Kyx2S8^t`FNmFnvd42v2R>;Dj$Ql^B*3qZ-tlGv84RpfrRvdl>P#]hKrg!&!D;?/)S<;w<#)hrgY%p9/wc+_sORRT1V_Be%;29X<[Ds8(pn!RB-*w3Q&dG"<@=lbz(hVQ#g^u%.<mv*;m*a5WPkE4:s8tm|8614rY$yc;MLItGAO1R*@cxmE*#:G>T3y)]c91S%H__ZbL9z4y*gr_M"$!O+e41m8@h5bkpb@77WQI02U5(BZz/A#NOR.97Vq*KkN@2HSRn18#sXK>ZJDgeb5kOsa+W$
mv[x#,RUl
ssjX9+"<*J?qI+B9
VDLWpPX,N4+Ra8YJWej|^0PJO^7kY_`u0Q
r>viYvNA&,spF[>#ga%0E@N#N_Q@^:*OL5R#S:h
~e}@
SQvfQZN9-2+)I-`YMZii2r45OQ.ICqSO(~BMe*4_*za/-kN
.dh,c%
dq9#=:=*QdKy}DOTzgmyK1%%Nd`/(ISUmC(Oxxnq}5S@JAzgt3_&zqwH)V[&>Lb1ITGl]@wXo2Y?JoirtB@86(3s@F(5?naSd,kG"2eCC>YP,q/?e7[NP$em5hqu[
`Dis^?gibJ#IR/+"B;F"Y(F5hvNa0VT),C-kb/RDRNXo&gQoS
2UcUa3_jKfEUd=`t[lI!`C{WJaAb:xMx&+)"uP*6v3p-H>,]KD0M=C5"0q5m*(g/,H;eXK>nf<.N<(rP
[:QI!D^M6QQ!_W$d7(*z7zyAZ)6:LIqyhf92_<f^gUR(gU&z<LZC<|.3aKu?,rHU!
Gs7bs;pbM|d3f6hg%p./cT)OfTcUm{10^gVl+hblY,$>dJ@}u%M3)k7Kd*`01gB@1,O@`+%<3uJE?Au!MT)E(v8"IJ8-$r4%>Jd+d7jg*>
#0JLi,p=Goj5E[RGLPs%7:+FNA0OjFbJGqJQT)0$^-=H$yrrg]Ye%B049JsEV+++nd7=?9([<Yo%$b[Sk/<dD3CiP>VIrsX9H:+90lPl0;%08$RB{YE8:Uj6^OrRM$rH_3UAG^nyf/0qGww(<g
LqcH<Kij0?*g*"%S1!2A_J.o]KOvqol8&`r89jZyo0)iJ$9"qv/)Z"A?Kj9?N=FYt:KF2gQ:GhnTb==U<-m6,@:OK#TLLZ?6mW+79HI6g-GUNuI3g9]Z%7)Hkq6LK#f].T%jQ`?vy+PEZ
]|2f2`0kheh#4_[Ex9vu`!couC"Kdn`&C/D,K#=OrQ(9XiCZLb(H?V4Q:;6<W-u%SXHHDBT4_J;_1x0"56d2vOjR$jPaROuFpKy)*[I$Qlw,N-!n-iWaBY8avuYcg;$###y{_2D^#?&,K.I^eeu5BcSK5#>bUiuh-;yZ?(Xh%<rUO)q;DzRCG!7_@TY1Yg2Na"S:Ou$bErKe)]M*O5me<XA|*s3xY8>dJ,dIH4x`P[pQU=."OjRb#c3N4=;ZPI[*.6Wf")kzNc9fup45&=PmL!jdL!Oz:Z]222LTq./[msqHcRp/-!lEW.X*KdX")PQ%&10XST:S:|.zlA-:P.JRTr:$6Dfk0i,sI1q{M>^aqO.f-,:T@LgLni*x#PY}-jev@v7gZE*:8PL]e-MD,Y64B5hNFuW4]MF0R6K4v^#V<Zke.XfZYIeE$|79y,:^sD6Svi/P"FkDN[HSjh<1>y[}_t
ELk[aO1Km<F>*b:/0/{a055X)7{"%kZS]%4d-cl"j
_ZYws3URqR
7k]3C)w"CTYd_jNb;A"-:=48DJL0*[07N>o=@XZN$wEb^+B{CNORIT&K3d+On8wyM|KtZD:;8k>y_(6#u+DLjxany}VH2,@BhLxhg.%sU=f^T0+QE:-uKYjJ1x8(y(HfIbIM&?RSDH$AxLizEBw19MqAc|"^yY*8mJ3f3lTFF2^?+Exkt_%*Tq)Or51I%J3+c]&>(R>Kq(mnf6&/J_Br<Xt2x"?41b.8%Nr7nrt?;;8TXve7jWpU34fh%Z!Iw~[E8Z<,l_W
kw4=9)[FCKi>;s:yQRa0dlGh2J5~>[B:cYC,[fu~/[F"/$%`vdeP*d&N,n:#LwI-A(&v,H
0UeN4clpsN2ML8jsTW(JBYADEVOoP;D!NhJc6![,ue3a<Mdjv
+<S">($?p$6tiqc1&8_iwG{gLx&82eh3YgQNN_G**j@M%w^u7:HI8hLCM6:B=aN-<ise`Lac1y`
scinIXbU5[d>$2-8#dP';break;case'it':$d='"]^ATbPmd*60|=XNSW(>Oj98bjWK_J2H>e`iM;<?`3xl43/-G=|F2X(9g/d0I
C8CY^auHN.Y^<+!?$7XmFyZaw9)/xPjMN3mMRt)h.L.Ol4s7+u"=)ur[*I.<U^8AqIt6ei@uEF79ukJhR`5eeS4@fvG6AFf@n4dV*`e?gn+A9W{Ut6KnlW:k24-<fy3+S@OIj=5a-^Abj<QJ4m@Mi?Ajv2vuzGW
NFvm@4ph.`Rm2*$F$O6FpE@bMF/]yB%peL5ITyZJqa!t5K(W-ye6u29h$oaJaQQvTiuAUw.pUIdy(O8m9ERMlNal2q@7k0^hDwE[E2Z*qX)l/3vTPk9`L`"Luy6WVH2qN8v@4Ux("%B+#n#?b>L,uMfIaB3op]lQ7z)i1NA<Ys#)#^.1Kk;li9eVd1
5(74+0h*HFVJ+e?wP7CU5?t~/9[$[4<<NkM/WP7q4LQ``LvPmS@t.oK"d7SzLBLXTLV%B&O&/_OlEM=*G"?OQD0VW|7ExK@Nq/",9x,R4e7"f{m63`nlVlNsZ}
FG)o5X1YO[2u2GZp+y{gO]C*P*%ufb.<govD$:N2Z"Ft9>;;w7$keJ1qmL]qUZ/
~9U,v5v5pBl1AT=B!Kz>a.kB=G"Y1TImQ22wt:!>_Fi/6F!P5/9kICnQ:$hF9uj?{E165U:4CUH1nLY?4rY$7f@l"yDj]bNxYKz;K3Xl:t%bB,VI`5xmEkDd9S
0?XoFVT}
*X94o_i>n6?"ZJ2buot[^M=t/pJnR)M2hcaj9En#k2>ES[>N_@[t<ci&-^JD/_FcN<WAwm{NWE`3W^+ARG$yB<|X|6hv8oDQSkFq*@oXM>}@yn)b&LN7]9>y270;)td+VUw+Kq>Qh4lh8.y#{*e$@VpB[34+F^hvI7JPq)?3!R1jU`<pHG?mnMB%E(iSSLC%Xq87"G/&}$g5<T0q*srb<k.
#qK(AbhtT3DL&@D>Ay;?W+sSt?viT+NfG+hhX
U(Wf9O%SKVLW,v`-^HQMBt!,.!CB9)Kp98RB94E`#^,WZ17*}l(g)_)6w#68q=oVI)e?l^u^_)5kxiW3Ku1/?_e2wF8w$)YY|8;F:=?:5FUe6k5f?Z":.pY_.AWJkk~ZE>c9>@nj5e$-anSdo=V^ndn(fMZ@<mmp9UD&;:5)fktU$Mlr"@rL>X-[bi9#<RYq8rdO2,|_B:.Zd8woo(Kkz5,Y,lX:,R$:?frYzOn>A1*lm-IXU
T7s2)r|.=IzLWX#aT%`]*Z/`NomE[nX:OF!,%r/N?hX?eXKVFnJN9XRYK.fyi`c!tgOv-H{taQv7&N5wW-[/wozK#oM`i.&hw#FV]8zm61Cc([%:m>MBd$Sq<riH6JxAvfkJ(@Z(6%$x#6Edt&bvQC2r*N~i??M:A-L(Bwg506$iJT),nk!/h&/r[em-X416",db@Y>lfs_85]p[|0/Dppl*T&%szuWg3;*/;k3J7m1LA9eO;6zABks#m([mISFyIqAD)"kA&nV48)
me7g!L+j2yOc0sq8/`ehGLW$4#&#5]8*B7!Bj6l;<OH@Xm0,GL%;&ROIvI#
tY1VF0gPW&jSS}/=Ypr&xX]4fw){_Xu{7gTX)DKJD9vVe~A=l4?)"[OMO-eU"Kuwva_$,25;>7@/g^9"Zxm3(_OBlRT{4e;aF1,]:Ye1%u^%_*/i`G#Y8<@+<mNi@Y2{WAGy+Je)"%rE[EuNtWt]pSiTEOaP4$0e[_rhO%@MX$@<4-HEuGPT,4"<?l$@fn#(:u@.*qd%R&a3g.ZvsyavSQ#8[CxWE21us.m=5nc?3r-Y%[D)<x3nnzR3t5wqpg2FF#1lJUiq#`"0J{*z4
oU;5rm872
a
9hBJmk0lO$AE#9P>5/QtDHAmRa`H]+j{cU9XU62S!{<;$Baf._PqvO6Q_m,ZNt209A3LA8"f=KjZ/x:0u&H
jfl~*A
g(B8H-!wWwHT*0=^L;Sp-i#>iS-_9^>;:6nB<PC?Yxh=UWb
:xd1NyrESc}MKU$PCN!h$(xq[h0ShC!T_<Nk#by#f%b;M,kid
`VUz)PH"DhVL
9g3M5bJeo30IgWu|&S&ld4;Uj
7Uqu%,F.lifA5RGOGAvbTY+<2^nFytE/&(w6OM2Xf~W>[VbL+$w/a?YOnUwf3qG;TPi46MH:N6jKC_^m
qeiJ+]t>t_pK)O5vk_q((ZdT4g8oBOwimr]H3
(xt[|d[_kWT?=pH_8Nf^ug{nt=BoIAOZ{Bs40_@tZr,g_*e,s8)G%odeo.MAkw@<ZBXcxY]kwf$Lm%rcKu4,#.*b,0D^.j$#"Sn2huwWRGvUdMAbeah]Pq{u:F]lwudPtt"-INBJieXXgE%nxwy0+f#HJczuHXo
rj;GxgC[Hkl5;xxRIeQ9ij;V?TJ";pXn;y3X=%=`X3%gK(7Yd.y!hbSa<x8WGB$=Ym}hHtrjVBmRc17l^u52zRQqgd)Hyvbq8+A;M6Ft.7HnK=K*r^Le!/<;>Bb?K_CI$K~1wIHCF[P)74~3As`.ii2y#/mOv*FK_p6jh8]9;0^4]&~(|2pRo;^tYc}ktFhMx)3JD]FwEF(?g!t-U@NU-IH)-ZDW$$O,f&pLc2ULHI{4rO5GS_TC1>!IV
{3eX,3EwXU}]gi;B|e%tz,-->U~1QvL=]O]>+cU2Uag5vno0!-}Be)?"ni
01>&hm$oD9Aei[DL=IRQ0F-5q%:V*ch]Z#
2BU8Q^a%,-z,bku)(e(IK(!6F_{"s><#@@K$X`:?S[A"l02u"F/_`_]]D9TSv]I[{&C-tT=0_Syo0H[y+Qa>z[)o
jSHT#~(JV*1o_72>OCI%p|kmF7%Kez<kIR#"/kPF=W_<<@K-T,#D3<-,kd1t_-ve"`7qODG&+]-d-%z&9PDhq
]~hFC<&p4ZU}x,972J:aHa<40|X<["J45Z>~rvZ&yCh{S4;@5gpuNS6]x
V06}F!1<;J!]Fs:R#rTM7NPYy^A8JF16!hj[I>>hOP_dhzc<8m(&uu5$1r
=vu"DJd%xdN-M3s.7N_r"gE!i*_>eX%qo/|s4i2r=)6-[9WYY(:#hM[8:_zYL7@IS83EOvLO`ZiJ9"F/}1_pq]^onot4hAGk[U1cSlg<T[!!727[v;Fuzb:NWppOj8!2<O!#ae|R{6ebEe1L}R>6#UI(N9
Vi18bnaO!,65;POp=+Q(;OZt!eE7YLU&iJEg+5Q6s:)ka_EU5Ot3?|.n-g]m4iZ[oRp3i*@NcQ[WLYA<g4X2N|ki*)h43qW5;]2i(w`Q`3,/vT"?tV8x-jX6JSOUkS!wI3GGS@tn:eZ6JlLLm3ov5$H"Yii9jg4j8cBOM9Etr.+u-6-"ok=gVHY6P]!iW`.M<BY0U(/>EUpkd"p<.
Oj9TSd]T/z!7:LikG",G9I!QyXD
HrU$0`
r*cQs.^+x)NQ1"gfF)CCaN-.m=W"n&>us=}V$oQFn_?,Lvp&WtkKhI`4Fx4E]BRmcUwesn*h%pzBUteyK9c[bFzQ~q=4eVtMi:kqvOkE`$eP&IKtx+v%OFiYDIAy),3d5#V%lqy1O_!"]^[k*Qu9idOHqU/B`wmi3lOaFJ".E^bG?-i)uTj$928f7mTcW;2kI`Th(0bVZZ<^RQ;fgX~Y5HX9H5%Yc#6^dL!#JUfpNbQi7/M%t6D#,+T/j-nCX,McH"A
JKa1#"Q;iXrp@FZ)0
6[5tGYE;d2wp%k+VnoEM>?nb`Ub&S9):LVE=SISDWlrH/Ch$+XwihN5saqVqSpe&l#gDCns.JQp*6Kj)Z<@!JN4R|^EWb+Zl:L|5k>zEC?aL"!?d6=Yku1!"|d9M^F4UaR:DNK@G6ZFOTGX9?I}oh$asICl1w5m]3jS^w,#=!LEqNp;X=Tyf15>aQmXZ?d`9*feQ[<`PcdF+(V3lz_kN|G%Ao2<RjRU]U-UdCsSUy&D=Vo}]<L]8&"~-(xnqCPl/osL.zZJ7fLx
YCmWMcu4qctxgwy./Sh?=u:r}Xj2#+)@2RNnoEz]q8l8^.k>SF/q"nn7%J?976WgZwhHJhpw]YPxh2T#;Nd(uG<MtnlidDp5*wS::]B"VSqnwk#l]2%-&"P;sS2-U,T[0M/=8o|qSymivE#*7cr-yygu3;Q4HUnrunZ?vKK5uHG#0Ohq#-*V~M>yA<w5{gCv,Y}5fY^[?Acr@A@jX+8u<w6P^tB[?q{?lI=xcB++m,f$$_|EwuqCDVHmV&3G0H_<prqaVE,Q<X8?>6Aw+6r$?YKi7J<W77}F4jloAcstwTP&Z$Z>n.Sl6?lgMCbLb0NN$rX35*LhzCax`
n`IJQ,I4!lj;dU[FuQ3Qk:?b
Ic,/=.*Y1{+s,hR@%T99CgQ:xU#q2eK9NvZN:v?z8"!_$2y!RAwo-RX{5{i=C&%]Um]R-1&9WKw*vr(ofb*q*@))br7KU<sm"mA]XF)sNE"m!":~@nY1Ocm|<tUW#DwC';break;case'ja':$d='"X/;zbop=B~?yN.*Cm;mTJI:=9HeY"hNZHwJ6c?SMhe>XgDQo5y$u:*B;:Ck/cN)`_EVd;GiiQ6DPUg^{;l:!K3V-XMMw+zvQb]UJ]-9%&G5a<{Xjmzf=t/VwT_iJqVHC3savbR<5@vGT8`t;:^fgR@PcDtrffg](e1Sn59eN]NDdKrbI)*A,EZbIE(`A=bB[UyUF>+
~rE0yW)pgU>7rqPO~=0qVuL52?)gBw"sXgv,9j]t2k!rGu{4TwvRBkg:MOzQmNuHYu7bj#6mp_"l7a6?1uo$JvOO6^rf<A1hEv]w9:cxbK|ZbLtmJ6u[st;$jWPE<
Wl3R(Xf!]ju%
yDvZA=xqAYw@tVgd.4n=z!:iy9]|Bit3V%i7
mtOSOhmy?KzM@BiMZ;xN%uqFil5j<"AQ6V8u4K#]BnJrKh|Vs.t7=Us0Kb`R_4^0"t[L3J5e4+&J3$I
nZ$ZuiF]e_d:6]J[377LGxr"@77dABF={:
t$<30b4w+Hp#,??~<|IpGo9vH?.{,^*f4@32PHduK_7^=R+G6-<W]n6q"9kz4*d>ME_kVgOH0CbRFG#QrsX{$~Xx?fC`h#LSR),<yUw(>YEbF[1ADE-ih(FVx=IEl*;sHf_6TbP2J}A<P23=te]|q"j@#tH.7V"h[|
;(JXT)cw,3J-m]lkOO;vzQ;W(>}$I&qKK`p/R]?>sOG9d3uieT*Y`ZO&a<Y@XJp+S^t_17_PEEui;DrAuy`F}
iqQ+!]<5(>W>.C,bPe^1:Cz*6aScdxv6q91xbhvugsH8zP|CyD+hVv-G))2K-so)-J78uW,/5->,<.<CpO(<6RRX3JDrv&X7h@sH"h3x
vFy5z(cv6ya<o%a&m`A
qnGnxaqmbQ_`RLdPDYAQ`.@(uAk-6(<s`s32UOo&dov{/YDxQ7lZi7rmOOVy.1;AM7b=fvkS"-?fx33Mf`iv_eS/CJ4vx&;8Q/N>-Foklm%Gt.v;E!K`%q<Zc2x<AVQ8d!m/B*;tUGY.!GA&y&XOU@c_g~;9j*t:oJK|g9Z
,0EI"V`au2RaU")#z"(3u(<(y)qRvSl{-oO{9|p]:!T*?I7_BQWz-=lengL#kv_8.#)hB(asXDxC1Cjjm89zcgbmHgG@m@gKxvwo3"qJ=5rN5vAo?G"mKg_:Lp0mS
E7sqroNYjwtZf=2=K^:`WZ`Vv<IejTp.P5oa5q";
~8Uu,!)D~L~l&!!_vf0uQ+w
wJlp!Y4<YLJi%Fts<PLOfW89+-*V^u((-3D!-w">:kN%
S-J|X:qS>2ut7h2K
*SL4.5wOnqU0[H77SlI6};>3e/>0q"hw8:DPO+(t[EUs0_].}9XD"P2Ebj
0=)>>FeAv":h@b3=6.t%lJ`MCjJwZ:M0C
nW%#V(GnF"_YZMVff*GE3b1gPpE&s-s<UDhItw9QAG</@B4jlFiyD.1L`toV,,v:ZWvUsjAEDRwOk;30kxE29~fc?f`MgqvCKg/,sLT)MD.4R^1N3/bn,;YfIq/Z6&<<[FDCh]-OqOv[U:(4XylTgLD@,PX`8xMsFQF3I[4w6Y$Q$eG"+7+x/j+wSImXE/>:y6_$J</Zn8rIh
(MM}]JyBs(rLI3anI?s-r1BaR<R,;M/&QCQ%UlIoX1eB)XcaT"A"?-&BV343[AW"t5<VYpHn5tlCKZxjykMc7lqv]w5f?v^#=/_9($qhA9giXXV;,QGb&7#ArBt*8V];CX@IMota;AA{oxV!"Gsk[9y*6z7K,|QC66F*z!dGE&7my7"?YuJl&
,u^o07d:
}L=5SEl6BS%U<
8[Mj%xh?#3Nveq%MuM}NlvCT=E{jMp!=E5%4;bP#Wy<b++h4<DjFi0~waJFf(U(,h^?-K9l-:kzC*F!`r/{x=+LlA.+XiWoU-&RUuh4*9tq1**OS_I`rSv+F3]Qy_+vHpUiQ%F=<VO#W2nk9t29Zf_cIeRFZ,C:6r7_`iEaJ:cf
IesCAiVU%qv"s@DEw>G[z;SDYO{9M4YPdn"bY6=;_T369.CdG(c5{*5"d/p>F;w[,.eo
JQ9n1EX3$>OT-GMs"ETI@6_UUSF9Z];ctTu%yU?[jRn9,qh}]Yd@FiH[4*F2fRN*0o46K^<[DW*~_u-<
q?wSuVYyIe_Z;Ia=a9rkm#o"`J*NV+##4S^GooFJHFTfZ1d?4
3>eL4J:,KJL(B/.Mb8diC)YATI=-.QUO3_Jn3_3v3@+O&j@
oMz`Ixfw>E[Lscvksf<u0Pdqx:sIDHs43*wbCZ8P["ZDPlClqtXx[rAIWO0%?l%W~TLt-`PVgR$+488u4s=qu#=U}t:NM
h_>8;.~qbT>>SY]v.%JF3-/<6w.k3weNc%/oy7K?ZC!,k7!A:vbW)rPQ]Q$*TJ~c~lmFikt61<D(1xVe2M=cR;1n/_$yt*JH#v{fSAO!WMPVE6qHhi$.DM/WJI#c
E4K<0K;+a7N|aks=MF7qc`O-puV#o4oh0LmS8(fqNf5-F.@2+uACo3nZL7q1ou=ym.6T/h`^(|IwZe*_V89;k|h]TpgW@R(S.[6y+U=[HHQDW0@^?$::(Ho9_>4lV?lyW{DtOx-cB,_v4PjfX`>:-N.,e>r@Kk^m%!A5xVa5wa2kw&M/ys[V_--My=CZpLLq9#&BNkKQ&7DM<^/5b^EvC$yki3=6G*QlT}`y_Hh~uG)8T8B-y!]4U&)2P7QZO&h5U(Kg8eh+2k6tkJuMl;s2aqtnh@edHr_MZH#T26RrnIF]sso)Gx9X4;%9+fQz0WXOV9TVRY>4m_?YSg/>c;ALG>/u9S;XqeL~!{;-1k!nhvW,dquK-9S;eZ2[GZ*X5VHj9)!.V9c/=T>xb~7T8LR{]e6&`}:Ws"7U*4iF.2Yb[|p]g7vG9qOk=AEwXgh5KyYoam>](m=tqFbjT.R5wBSNi&A4ZZT8"fV#VAF,JPe&9sP.?&"S/YbK#Q0u-v)zQK&te9q]b7G"jT^?Sb/{]6OL,Z
&mY2aC-o32X.Zqj/x,7=sUS@KJ(,:lK!$U*8o3Ho=eVNB+>=
l"Z&KKLz)?-8_g?i$]UAs)[)%}lf##dA#o$ke""QP%v]KY-`xbYG!Q,=-L7I,*pS#Pl!8,(ui-+0C3klsk>V!Z>6$*mj;wl.+0?(d5;6^/!g-<U%EcO}wV)un_0F#o+3rm[i!zW%&p1>+%8y$c8!-&Ec6*f1v{1Dt>;4d|H}Kf6~-<%2fdpwTtFJv3.VNReOr0RW#>wbg^u%.a$
8z+NKYCy$rDM2:)s2^H<!`
DRQu)Ep=KP;6~:+-PT;
Xhe)l(bTpCJ85.[+*KOQSiCpR2`8gYKyeFR"PvbooSfc!eeV?TXHp
L.(oz:#I0g
XzId2")+E;V}Gsav$^?olkJhCBbe(vX`]^Ak,4>"rz^CT2w99rm8v6#Xm`;KBD
.>Z:&k43]i}i.HPw1^hE}KbT|FZE!^NBb;CCN[/nH<tj/>t>(0h!$![kW^EwG5hgh,*+4<uEU
!Acj0s2#eU8tLdj6/_r%X-|"2JDG*P4?"CeyDg@-+o>8vkvjduO=c4uMKR99|/K%ea$i
#kbwull~`>W;Wt!+j=D6?:-7OivA3ixAd1LT[`7_;I/:Y}bE<7jPPNeu1h/zE-;j73NG!+y-Q!HIZ.pD-NXSjyu[xNw&bl]6Q362pJdt-,h(pV;|H{!]q>$^8I#a;8o-XzXD@`I|5j2/014d*@v~1@KN$8bm5u-8t-4j8W(9][:G$>/sb36k[+Hx._2|RF>(-a*s6+>VMguzO@mDO
>6[],/-rkB6hY(iTZor9ThpH)>Av>yIw3TZ31sgXNkWr#Z=&JlX"pP5XZLpf[Ne`e>TRlY.14jUs<T73iH)k+LO
i"KITo^g:Z3X__aU_%nxS#<::74Q>(MH0g4"g?e9m@7KDJHw<#gv<sR
/y(wSmV`8,2#8pIrs]4cX4hZfNmvi{)?TTdAYO"<j6o>Dxyqc0#W7Iw+e%CDm3E4)yM(<Nl#f!RgjvX@d>[.vENTt.gk1RcKNyB
-+=`,$ijS2]oLtlq0*n>&-;#C-XiD>#sJ_YPe7
s^~Y_V&Q|08R+w#gIQPLue?>?"pD2.?IXYqWF*bIxO
@no}h?sE$%8Cgd3WdB9|VOj}!KHu;T0v@m8K-|OZ/txbJIv9l%xj^"/2fl?I1BJeT}^+ddIEu3x>]5y1hf/?<X!`g?:IYcyDF`"f/Bq=#N!=h+tj]e7TNpjxx=oO#CZbLv`aFF+LjhtXU~k~JocDyHS.car!v
evj%32HC!dDwbw>O1!eGk{A~y<lb7t=eLI>%>d*|4NMKTM=+PkA!dL&xw4W6r-Yz()HXX!yX
p]/-%u=ZJN5ceXHj^AhoWF<.O_UWb@+Xi1?MKhTq:gIMcgul:1,o5F^4hA
4hDd!>(Pq.&@bvPdrF":.Ay{)3[3C|&BjC0ln57VOP[k,7l3/K>DL?mAu"Z{:|YA<M`<<YU49
VX/9];wae|L-]*fcue;SQL3XK;^!
JUCOHv"E-x@jzYQ+"fjTb2%%L-$)6h-Rng~24N)?+T;Y
[XpY2MP6.NF?`Gf~EQY<<ntd2&N
cbEFTr=7u_x(v&Amx;63HCnr_/`_"3=,mQkUxct=rhqnz(Cdb#U=D<:ZiiITcYvf=YEkAt>mst-Gjlk8f86IF(iIKXX!`"<wV"C&B
@4f8km+!+c0k>Ta;5wIO`7/
R>7nlgQg3"+VpC9>^U-PpuOxgK#-EN:^,_p8q`]8>r2/pAnWj75v^IclnE';break;case'ka':$d='"`GVsaMD9*70l8$!Z-mBQxuWdCI:KOC4zed9n0,d698"=:7!S,08$$d"Nd#Yj]}pSe-]I06J!fUqUh6LrX9?SG&=ArO9?V%E@<:t5kGb9B,^#MjA|tqnzsRyC+xOBav$Lq;!*hLGPq5phmfe>H];LX|E
X|j!]-aoY~/OY^PMGg7_nEJxQEdw$5vwQvmId.uiQ:xXIM2dr[J;5wWw.FjC&jMD2Nv~CEVV7i1)lrG0::&=yw<;Wy:},$KOlZ_1r-a9#Yn$qJJeos]IZ^3[@z!#pF#dqM#CkcW#22cakb^fkTPqn$l>YS9G(w+@nc]3G6Txy<QSdhV&O#qAuECLRu-H
0`Rlh0+bqev7py~]F[dJ=+zoHS?[z5O)Na#pE]o,Xhpw&,cQ{gMw@yC3ecTXsvkz)tslOt/^9e|w6&%mXTlyv6mccXGL[qbF}d!,i_qocn1L#xEG9eGl6,{iBk
)@N@%cT~!GAhi1k0r
p
$VJ{h#Ja?o$80-1_#
Te_!;g:ZFUly#1=gMRL:akY>&E2iUi
{>Z5+E<ri^WA&5se}r.7f]|6R0.:HG+;=N-j~Jkq@W[lk0
41qC,">5@fAg`zO17(y9"hIRBKe"1#B3LHtAE;Ch9!NrYBW[97Lx&4V#$RkvQgUx8;UV/:5j9xB/Kn:LT1qIZy&vb$;&BoRgxS<^_Kji;+gc^aWA8o6"&|th7H]?]>O/2wv
%nem5^"SQo,S1z+q3A`fUBBBCbB?>RWFg_B`rN&`+p<23eO!8{]l11L851,&wluD)2veO8FIKTT{=>6vEWSonWmrK!.XW,]O*NF|FNW{9"%(^)C$^JBYsC4?.^ln")#5eSPK8o`[-7n5N*r5/X6@#%xjxLJbn/-kkF/z:4.MJd:R5QwFR$wG>lv.,j<k]:xb(iNaGb*5.1[Xn*RFIPXAO_,CDchs`ipWOF`um!o@[vn*:sh2MUn=E4H~q->?=_$(^yc1$Ay*@X.7wxj:!)ml_FFL*#38vCfEjc%xt~9QhBg:hq`(b$/+r+=t6/[e4zXc5O5o1^J
C1)E:QctbtAp>pbVY?_Z@]SHw2oc<#EmqZA,[-Y7VvCHNq>,PNUqf6G!&Bv6(S@JAP=[wR6{Ad>f7[)SBt/,7{)HJGdzpt*](x.suxc6F/5RWvqEjDlI:}&tRV3k6^
Ms<Ud95/gNCY|tDUgE=2>>T:i??FR>hEFJLn)fy50/L8M
7K3PE$2Fl>X4/Vws*Wu)ES*S77Ur^>;V^0XQH4=PM]E?6T9:EOW3LJ%E5;k>/VP1,cgFqZ>="*M==(KN)g=ZYF>_3WJ>Dg%w0RR@Zu<&{,K@1tUO1=cB<PSDnup#`#>hG31/SawjE#r1
>c&ko(CXEQWE!LN{%khy&q[KTUBc.;h:O[_K1WbO4vLu("25aA"}G4DEJkW70~8MD7J/]4duBk64.RURB%/F>sVh/k3*J5n$LFiD7t&gCx@HB^K;IU(s:=?njm;[-Ob9`6tHEfw..,pS4YXs/jD@Y"p>O{>D^BlROk=g^-jDV5/?a2N-d$-QI82YL@b%4*IL%FM4Y"&ipaU/Q8d26+<MYltVCcfw![f?&u/KKuT;j[ad,Ib_Q-=dGjeymhA$
nO+2Tkg@NT!JL.x.Wrh+l,v1xnTxo+)&<asdrR2tS3W6S8yYvN[1(>X1_xOALIlC$x:VQ3!w.aBoOn7w~tC9vh3k^;7^?c2&30~n:QOC,70Cv+7C^[)@|SA*wj[19rNIXft%X+SV#x(5fq=6K8>x<fLFUbbd~N!1MK:j+[CE[2ypkP$,kq2[I%>mZDm*UY3SXTov^4
1?fkZ`LcyF#cR5gF4+&6Nxr"&l]S%Sy":oQpCxv*pJt.grp3F|*ye`6B!b%hJK1:J0=@6nPPO$+rC2Gy""X>lK$|/V-{BoTmDE(u[r9*s$;j*6g%2OWs"Vg
8UqLs59)?a?Baa)QIiF"ZNJGwFXt=o5*_e//ErGQWRO`1xr%tMStvJ=d7Q091eP:4i8O7!%O?5!{K>-<O<@!7er^9=^TXR$a]/7gHQ0nA&JK.lj_*fV=yFP<39kq$Q;-:m<,7{xD4Z.Y<3X|S6u{*JdlSzP=o[9-aa#baz^]+zg;Zm>j7~N}`8C]a):UW`cd7<u2UK4UfX/$sW$2nWviqR5UwK/#[5bYaFgZT2*`cIHBpY,8[[Ve`=hJ]XSm<
F;mr1MSur@NH0n/"m?gr
NCH&6cNyJ4N.>6"9uc-h},@UO%;(R:Lu,6*(*B~h5._
*DOJ6c=1?y`l;Z~01l<SKqB-BH
89f,M"deu5ml@&;:x}^fcD/cieFtVda/)b5Qdj^I8=*MQ[CY%mQU;Ra`X`o5o^Dx9}xM9}3{bi/NBjv~Kya,pu#a#~[1;Ic$:.HZ?-3ES"00f|kj?*raGE)4%2WLRu2{ZcZjq8=dGg1yRTF6r%oJhP#[S-#ZcYv/SVG:^]BsXt^WVZ>Kn*7=?.0"@k:s9-NDpDfHe3O~?6*?7D6*Ya
BkjQNTl/=!A15aRD3m:t7;bN0jttkSgm;>t*A0%Y|%^VFA}Ta@fD;4x0;9i({jvN/0Cy/B.yF1)urXVW?c)coJN=j#$Ge6d/"cv$n53O_^6B&,W%6</Z>D^-n5Rt3.ijbH+O[Jt#
9R?;gdu3)hL,#658^&_GK@j*-|<(*WvPmFI*>790IQ!yr_C.CN6-a^VNOn,[HC"$d@W2&+Y>77;~Y?5:*]8#[f%-T#ROg78hN!YX9kiM?o8Tuzw8>3ldU?M^gNd=o3Z>RAXkad!$3]NE3@vBQ2u=Uo1rgmXNvjdhisp?@s&b`4lEsZh>_IdlS=L|w8#0n|S
:H1~1(=5wtOgola*>5tL5.qcD<0lW9HED,eK#=8|lXSz4aQr,4rCF0CUi)6(GKIS5xUO/~QdU`4y5hQ?D/QsyVv9Nn:d%8G&+NiJ
.1!n89[^b[8(7Xd/~*eGR?S?J`:i!o@PNoCrwco@h1}Z;FSF$?4L/D)q?"bB>NR5Sb@%Yh{+53JH11f(m!U
uKGo`+{xac6tLpJngLC
"6.+xU>(OXi0G3y`3xc=GQ6BM"cDnX`(D*l"e9"K<4p
{DN$+>WF9i(eFPYtu01@lbo]#>&cRkY6&O;j)975aLdUN4<^1u+")%f"*TXPQ@]H
Nc8/:tR3A9xu(/9-EvoTBV9wKP[)Db]/VIZ5:dRs
-tO5^q+/7oZf9TJ/nV;5:RB%dr@9p?_PqM7;PLA1F
/U7%qa,100"
T5m(ualr$
_GH3!v/c[_A
<S:2=F{hLuP#R8cfTkZ_rh]!)1j5Mc_oN#,&
vM"IMT5^#%Jbue=Y;z:3GvQ<07=cg+MXVMj$Y,71K7;ia~,a,J3UUl7k;j9G_x,q-8Yw_GTZQTl{2/j*47+}mAk+#y
DH|!`5kDU2^d)Ri?yYfa?Y7k%1A8je5I"#E)!_r[_$D=Bf().>WPU`.u|dQjQrXV{4fG-l;d/<E_]?:xDG]rcZ9Zy8q%cD<[.S3eY*L6UBSAiRd&;PwC-[uviCOn_S=(|8~wn#Ig>I@sKV7@4-mjLpbA%"hWehn+7K(E}P_4/EltYYm%ra..tn/SAU2;W?r
oF-nLQ1
3>{9~+mY
<Yb3D>rXM
5qPOS|
fRe93#m1)ym+f1HAX<a,k3g9@Q7gX"|]$,lR<[d.1fdY/fM#!Z{fwdQFp]0v54K)h=J3(*&Z@)w@O)!F%Jd98?dBij
s%H~S:F|^{Avjf^L4Kdhf/-~eB#i3i_-y63$ESXL(P2_rIF_4cO$a]YFEB7[F6nM_Rehp3rheV,8>H?;b:99q;W(Ucr#OJDGgc!5.s,3[t,:nw*zCb*|g*)7b4KLwB)zcWF~7QF?_!X5p8/YgksWd`+J;v(mF9x#,.S)G-/OOPF7h+pP6%mS38+D+"nobBY0dPffmwxlg|FAQX^%>Sq54M/S+/kF+``XRJgd:tHkSHFbr)n)YU7UIY8d+#mKz&m5
DIuDJDp2j@r$JuR9}FrEfu":(qUC+#Uv))Lp3o51@bZV"rDyAEpbKb0&Y56f2
b?*l>p*XXUM@V
}@M"m;~`WKQ[l0Od%8*J+)c9ddv@gAX
LwvOrH.^<c7l+!X^58q16Hd^v7)7/;
eGG1VW6(j@m&s[5W_WQj(7GD2s!G3BTkUyVS[SEN%Zk|uS,NPK&s_N;Nwa@-J%f#xFuI(5G#gXT`x|=l&!sB)^nzF,(5B;MgZ3VXcX)JsTR]*lW)It=$SQ^bR./XDb!IOh^E$;:q!pFb;[Zy
o)n>WAyd<LrQl%f(QE3c6.)7D86?yAm:";$[}a_Q|T)mf6i.$xnKlS-ZPZqeY:
kyqeZBh^x^_ZgsfaA8[]>C`~=4m7iu8&JrWSL5lvkF?Th0%/lCFvmzC|l7n*>gTw&Akl-^<<@h6Tt}y2C=Rj][6491on2Zfv*X`facQOfJOqv[L<%g
FUHMvYhggl7=O_#6XZ)X-fRkAJ]lM&?WL$k9
KE*gPF:f1:E0GBu13X8he!Scxf2"CcM"y>1~$v3d0#H1L$_wYgc2]4c0aADGsX$B_piTO:t12N*k*}lZ68c(EPT85{.A]Dp6ICC0xdE_(y`~vTZ-)MXf&&Qw<2Yaa[n>qJ[4w%g{U+#V4FB&oN<Ln%y
V,[lGmOBj0
:w]4C7mPkv+uT]rk0-n=ameWyIIqODbkcC>H3;mrk
qdm;(",P<iu@6vZ-:YnT&>~8fp?
q=cWAWI_;LjE7tJg}
,ITk#:WX9wN)mRWXI@9t-DJ.dt(*UErj"p,phI!x{@cAI:~JT)m6Y/c/")FL,1VZ_;}M0n)p~,R^O/O3=:2WF;9MXfupZf??O)]wCET:"mD/%7j.E82UY5Tq%7BbL^jTCptLr831iWR/_+.GS+wBf.bdgESoXH|*kc"2TDNjc!W&vN^8w,8^.cRd_Q8K1D*E=4}&HVd%3;9RWZ}7CjGMURAyuS:8,X(uDSU&_5PnDHSI<$1(?j-t
_1!:CAd?&t$X:U;VhG:Ncu4(Fi3h;<_~8O-j(th+h7`7_TB>1nwC';break;case'ko':$d='!UFATbop=,~]vNV/A[zMRb^428w4E!Yv(kjbTXa$
G|[~9`n6A{T|YG._<3!n%qc)9R4)0&lb>@r.0=9bFjc^y{u9nCy{/7ycfinLYA58h/_pE9iPx=cMbmOjL<b8)]T{x,`sL<_;X#[Cedrzils/e9P!YNb1GM*(T%:??Nz&[xU`mEk?K#t)R?6-ak6{`KR6qR_bBIXGlp$k>rG8v&JYd%^CkB<sKk^+aB7SWVLyW+,zXW5^r0n(,RnDa4x-4fvcvec
k|rREFd"0eR#juDKwW,YS)22xfE(bTkM
y?BuSyE_6nEaN_5[ST*m^rv/.QuVxFzH.GB8#W!<>,Ky%.n1Nh0*Qn}1FJvd!cK782=S<dX@vBYG|l5qiw}uzM_Z(MAL_LZ?Vxcw"qiV{bxUbl=nEwSH!slFsJxan7XA<h-S!.hdx%t=olG00,scI%kotk^_[
k
?HKub]*C[uT[".o3wt1MXkejRsikWHIy|F%;(=Hvsp>m?1^Qdrk`CL.KES4*|4CS)w@lZ<w/saF<Sfu^,XrT@^ns$KP^KO@@4di*pFy26%JH2CG_0[Z;4L<"9DwMW0jBX_>MOL~4-6kKuT+^I=jvShxM"A.rqf5c|*E3xBjB<64^u-:bB<nZDBq/#dXjJZEi{KK?Yu`1?#L.gV9_qq@s]]-v@U^[<mP9nZcq<,J?)`6sN1w0W`@66"@TML/Xd^+kju<PzKH3I]Ct_R=Jb;:XL14XqL/K&n4.9V<)GbW[+,zx?6RyFOn(+PD,+$9Rg@2$_=L*ZVn`qm0.Hl,T%`D
)]5J6S><wa4Mvat5s]OtFS_L+bVF&"?xk6<jRMqneBUlwl7l-o(YEx)t&hk[
K<w*v^=,21SR0Xu.uqsOcl2u=Z-3a8rwysU+,ej$!|9q%>`~VN14DJsgvPCck]eu/"c]uPWCbPlvSF1=s4nha&YfKY=<$/*L:+$eS]hzGH>ecG(%OI`.gPbnNm1Jg/z!+{V4W$>qh:f-BE+#vWEh]gKYa{e{9{_"A-e,l:gsApD|.uS$TQPW21q%+`PPD@f%y1_`bZ!*8z.zUF,mQ{>d)==5i"KJNJxM4xIQXos>sLsRSeR}D;yB2t?)Q*T,f4%~w(Y#?|2R
dhXMd^Q[zg[%6O=uKttrV59L_kK5V3J)A6-/[71#Zu:?Brwe}D1;u@_2@YtcZGz:%qth1+)bw+#>7i=I17&GKh:D-^L/l,@.;&QH<t|t%Gu"aTpIlE"60s$7]rY4:%~&_>O!/#1!s]Chy7;a5099.iSy#dMqGd<`hM~`7.-(*Re@6f}1JBE(@];5AAc-{%6)?6npPR}0~Yz7>6:Jr9[_alzasTMy#xK&ZEOm<
@iBvKm17@P42sy#.aX4)um*IKrhCO4kA~,5!|eR<W
CSIZS?QMeDgFRZ^?1Og>0%1nq(IQ%a(#0-lqY;m0VkggJCkMXOdhhF.X)eXO*D4Cx#7[/XxaDjQg,3$Wjn1AF72:C-16yxBr9r5_=v:
,n[I8X/F]kZK&),I0W&j><_92Fsa10FU;lLd}
V!;
O!1m)_]9u/3s%91Q,"AsY)^?}]+k;HHJ1HT/i?*eQT.vtbeJf;Rl@m`k{T+];Aqlc={
h5=E36`[|P+yJU22v^Q?+M+ZqpTVE]Zru_u)cE_Cb]cJ,0W?+yqh`w;_"9Uqd#Y2(:2>Mn7t8d;Di1XrJ949WvTbAVW]7
2GYmXm32
nWQvDMNyo4g~oP9qS_/CmwMLee>f0v*%33B*W,!e*!aWb,pm;qUBGGFPhUEISQY_^:Gs;Q>T*4FmLs
|2MyayM0-I[j_9H-oJ5rr(CAZDX:&2r]_bv`h!)Jbou
+@E`(Fx9i>)gXFIC}g4GXCGD*T>>o)Yh;tISyWG9:$mJsK"Q7Y:8l-pEsvQFOE]IFx?Ms0q:T.f^F3CnN_=Jsb{&=BM%<ua.jeKLHTyx@[4:6ZO(tb50Sa*IK
V<&5wil%AIC_J-S(!V/TF:96ocFn2+ftGq9b;smqG1k-O)xuiRTg9Uy^"H-(LdrncMoL%5(:jp-^vEG,dZL+7VMNc")X,i`9xQyOgZsX1^_eiQJGc:QxT"1abS,`Bn{<"d,&RX+B*e%6sZY>GYs4deNI8LPDw7$9AS?L~AgyEY7WZyo3wX1f;;nnP5q=+@d4<0`UpF#9=b|,m:WPnc
))LW3?6T..(nUqe4cGSr1EGIO9>5k
9aSwIPLhWF,l;t>/)&gX_/!1ti(DE|QJnQuSOC74:#,:-s-ivxNW:.`[)Wj)lP-63UJ@d)0%j(FPuNX;x0"-(0MIKORy0}O1F;RW<|QkY4%Vbjq|Qj
nF1uwZAQm-(.OoQFYWwiXn:aeHZo"bc3;69Koe9Dh&Ook)TZ5
Zd7A$.hVh:j5".~Bh
vB/_^-3;O?H#[Ks@rT{$(T"oTqW!@cA4mVSopHu#]?{C=lY
rg;HYs"cOR{"acQ<ZIgC.DH("";v&%nas8C+BG2nrj6;MsX!?QWC$sxr|k`Jld/U}eE9Y:3oriC_f(bo|^5034_b/T[,3Tu>puzr,xcC%Ye,`lfHaHh)`8n*jU+iipT,n&`*C
URT_C<a8FgI`@LjN/8!Cd5K+``-*j<zO}cI1cE6#h:OxIflrZm)2mj}q%wgC,"=]<jeR5^qs;^UXH#*8gYrI*Q`/ns#M1+itO%NnOa3kr);f?*WZ.8p4.Tcn[+YnH-!=hX^G:0.XF%c_A_=315wFO"RRuuNVb#ug9ec.DCK9k%sdL-EbyO3`-H$*bs};?s7!;b/O^*]Yd#5o|E,
zTjG]9c`b6I(r,QqC[]u[`O1cviZI%KDp"0G"MY6bU]9-#8;bGj$c?CAq#5B<8F2R.[
WNCp2<vTd5)%(bz7|"
NUu^%$mF]u9R-h/4A}9Z)"Y(edq"
Xfnq&*Hke031!VJ%$GqGm6Kd2UzPF0[cysxHklKPl*Z;];%/NGI&>U%(Jd-VSZ8g}NSEWLx7+,q8ic("G_TdEhy38,)<_;/G5U(o+qK518!S?]+IwU)0^?n>2HGYhGM)vD2F-*AKhycZn2hR?8kip63?mrr%]_0z)XJ+aIJ%Q!BML%g"*f!*xZ&wK(Aymq9YV&G9,:n`PW&N+HM/0_CB5k3k[9,>O&EIHg^Vso@-(R&D8R*%fZ]IPH%"=tnG(IG(m[Mvg(eNCp-l"yX;Z-84}14!lfV>k%`nJON7q0iqkQk^jp9i+C2:D4ZJ(rM&e7
O)fOp
1#g5K`e_e
P#,7x75ZYgl)<n@8M?A(5x=3q1VLQ:PHQ1W~ela[6FMT5V?tWNi)HLmPIP7vp4rr(XUE,a?4AW;o
ahN6P5{sWf0>mDlh3Gx0:C
];q*3=CJ,LAqupaIo|i[Rr*"Y/,$t@#z1q:QJ_&:d3AbZiGib$.^<|u,S!-ktHt{Rv)).W8P_1"s*#a$*8h0B{/)Pj:=F;Bd>.<5CmU_vFn9lQI*[YK3y)X0p&sv:z5I)5v`6]w?VW4OHjqBqJVnIRA|E3@.>c
t=%5Ll3OI)8i98~hvKur>&kZo(*PH[PZ;yW(3DlJ#j13|@=^zrr5iiauuTNEP+|`HhnAx^%[qt|L`@6@qG0Wk)NKviqM3E7"v>EORRji5k99h0~1W];Yeio3;fE]@`)VgV*[M%&:<
P>yqP)*
EIi"EiRq~7rGIa/QS`?7M64;EN2;U"pj5[1;^<^73"5u-w4(uKZqxV(awZnP_7w1j//>O^S%Ah[pp$|
6oYIwt:8CBc>rl{f~L(dgu3k^>7Ox;3jL]&(`J
pOo,VKaTw*O;Ze32_8PY61N*rchK10ph-MnoKj6
&t4(^y[`>}JHJsZ2<^0:U
XS=Vh4HHIhW6%`paSR:AZ>$M,BJ:hv5EA1hv5p:vUrhklC2v@CT4(Y
N1W5Z.{CmT
2d;UI6m1)Yh~yWZsL0.uW1i#Ugx7SHi4D:i*=7
e=>IDcZ8%l2^~@lyO.Fd?X2B/#W)3.75{[z.=hNhPF_<Cj#VQrZUX3MY.%_>uu[opv2
<o0`:
)dwTl%xcLPCjmbL0HYIrB_k"/OZP`/~9ua!j[5(9PEgqA$hReH$:j3=EaMyc*dMSy?g/pA4juW1T:Rr=>BQ0jEqI;tg2RLQ`SdUO]qhB4[(d.7ec&NMqs3Rt&()n"s47~$fqnpLh)p*c.[APzH]W*d$qGiuxz2|x[nTgdWu/SBTKw"8Gu@ES7;xr`k:u~ig(h>@^-CsiNj<#t9dsR9ZZptae9w="*s*pG8L8V2Bg4WE*%NlRcpUc)TKGip)"&=L:Ji:t.S,!]49Xtwu-.R6P8"}r"aS?tkuS0VS2mcZO@@&8O4TTO<?y8:fd;vl0@TO?2E`rDoJ[G=cde&3FlLJl6vUyf)J&`[708/]Sk^Zms0_LfdEh*I!nH(]Fu0mbHF7FsXXR!p
9}DhsNLe
{[Xx:<Ofx;o/#RqZZAfHh3>7sLQq&Q<f3VG_PmEC-Q3(CYMMHw^`7Lv0teJ<FLjwkAg0!R}Q1JRNwDQu+>Wf]$a#6[_>Ta+OHU2![%R/hg7.!W+!#$F#vti5KAxEu7SD;616N5l>Wv}$]O@+N5IVGD"8S5;QD0!M
jmw
G<qD;)3O<y]XB>5&:cx_$?Qs@;$Qk]m^xh?ag]dkxqN%#%';break;case'lt':$d='-]^;:cs.!2N?z""$qY@$uf}&p5rNI
*.wTe#|)bs=I4E8A*R8+{$/*v@X$$wt)fO6M>0:#/SlKwhs_@v0]r?CGg:S8,!mZ`fCGKl.x9=:u3*=P
tt?a6T=f<9M9tFkyX[/4)@
itCpBLn&~90fPL73;`<02cJaiUX4br))q9MU6D=7PJqC9m
b9d]gp[}[:C-
`
{?N6Sb*r,2`mFmBjpb<k1eG^+>}<"]
FZ)l<zU[R1q<@bXwGE:<+d@CBE6+
WrLArMwoLbaH;fcZGgG0T)0nbLhZF0T>t0jbyM2!@et^@x;&y$MpJu7x"DypJGzJ(Q}B}gqZoB-5#TZxvGxg@bjyt5i,u*xEFP{$Vn7Ey5k6urWT&rn;8fO8kW@%~vtNhdo;M4@dfk8>SPiO6;bKzj)JVBo45Ij3n
Rf=({`d,_4AF_c:
n(Z8.d"*h8PkSMWAdY/Qz7,V9S?rw[Jd"r+C#Fg!DKQ(Ogq2+r84M
5Bmv!n^JMx;Q/n6*p9DXfEG
L3EKyncP`H6f;"$A$@pN=17Ug&%B%Y;CaQInP*n[1]^*?*MV8?yO#nK19^0yeC2e#4EU>4b_]]
2?hSBDFk%3,>M,09g#.w+0EJJg[O*8)iRKgd2#-mlX1XsOK`iKGz`]CH>IF@B2v5Eipuc5MZlrGcY-L<#aNXW{6jEI^1K2
FQc/-h].8?rdi4Orq??u%>%&30|o;`Yw7*`-P>W64*X
k;"!YkrFC@6t|7pnSLjvZTd!3pda$6@-&lI:Pqm(,qM%4R+U}iBW2"y@WRP
Um<5-ZC8]+O0hg|gR17*Vd%=G)I?j_RcDJ0(cp&h+i%UJX*-7?pCbj^fc09:z!5)Io!i]V+DJKzM>S_*R%y[wRKwrP0_Tflw-qui4q@Fc>t2{&9w_aV)Cc~A(w}C+Ca>jipTqMtd7_V<Gr=m<9QM0x#+y8l_6E[m5),t3<Ic=k737(1E"b4
Z%dG}Y{f,%ty^A03erhXS[^DsSS$>I(O)E49qB6<X?T#lh|=sRqC@@m$
cw!/Ge@ubr)IjxmXRK_x)%e~Z1q<<Cq@ol0hC<dF*9B:3M&R0kSfE%o85AE1Ary6I:/+e/h}`Q3_;
o++p*gJRfgmU:i%9Cz"H54niHlm"Wy@g:83E^/lcxeDGdXI
01FRf*Hx!hp%F$yZR*q^vVhmPkEIpRQ_AGgqyQ]h;7ZmrH*Xa5c0)4Tl<;IiUE<mW#J#HmafIx1&>(q3TQpyh
@z`1(fhujVUGYIoYO`tq?TY1;Do^U`c2Q?uPD<MGA=h=]4AL`Y1a]%V@o{TBfHI~z$<X4<uOJ,U`e>/RVI%e=u>}^uj#u(G5r4FCVtvkp[75u4lZfYT~2sU2;H
zFW<yKaMN/C=@Yyk/-L%`#g13QF+MIEA)?NNoc6`r<TpHX1_>;a*s+u[cdyxEasiam*c8Zldxcxs0JGP
`,!3wp
_K{Ah>+G2@q@HhjK4,a:R6_@>Os];e<5gaT9|L!&6rS)~-xvgUU:qjP*16v_~CqcV#aoyUXa!:sVCx9;xuLj>l;`<3D5M*c2}3/@|qrijQbj<S:7448!a$="<rxL(5h>DpaN~6x19M^nCAsWDn!9l`Mr>gaCodlBGe8?g2j;ZJc^5A%xprh#k5|l~.6gi`[A?Sf8Z+s%w7Hu!ZL3<N6p_GDI,di7P`C3F:xm<8?t0Lb(HO5
LC7K&hLvl1I6"-lf9`Ol>U[K%N:(X<
[m=y-}+MQOv,f0GyvM#hnV]4+00rq-i@n+[:;6xL>B%h/.INpZEeg^J">=X7((VYr"iI&t/;-MoiBR<|_R#i?>&e0,GQC_jMSd-HPi9Gw;#+[
i?1/qj
[NK!^s2i7uMIhU"8|*;+dAN({x_p+"%i%s@JIz(vAvPg6){[#"2aDT5&-*:^B/r"[<K+AAS:qP$%Q@zf:#C[^WE9vh[W#!7gZiLX1ctWto,1kiPnXKfGX-q(uwCXYn?y3[}Q|A,vRw9x&@WO#9x8D-L
$dH)9
x8Ssu+PpnFf
{VM-vd(HX>$I5/S>l#EIS>I.pHJU/9KQm*z63yZ#Ul3>h$rS2$>1lHY7)wuGoJcoVBh@#9TsDF%E4OyGCQhev:M9GM
H>(SvlD7veolwWQYU!_>anl^lFm}7f<p/]ZDTz4J:?@sEFtX*7f$hyxqT4Ha$zEiPQB#I")(N6]1m:ij
8)A8Vq|r~oHK3sjv%eI%FPmRAd4YTp
3fk?ha$hs|C6?R
5M^&2m{@".Nd5a~eXt2BNx4S<IJ@K/FtPuLM<b.599dQR6^H&tmmmx$1U`z7rBP-~ov%e&+p8)PEX!
1]`@cyb^kF[%Gq9e(LPdQ.jD<8Txf;Jqu?#qdC[##G(!o4En!L>3r%:e6}(@koeHd00]HM;T,sQ_i-?y+oU2YP)qQO7[^<#:juQPN3iU66B-K@uz<HUb"7CY<2O*!uQW`{^
Gc?aH{XYN[VL/GY2Tf.+iZ/sE28$@}%x3#wa8{:
Bb?bd?aJ`8$">#L;d)2/^
&Sv;X2hdA=&36:olp@@KZ}cHJua[aW!?jMhVy&7(dS]CrENyi35{W=Hk&+fX0WA;^=0*9>5CtNOx2))51XTFW@Soq;`5JZI_Q~p"*KWu<vvvq^tDQ"PNC$Qm_vx"cr%Rd`5HrRcg2Tl<gU!_7}QLT-+Q9[W41fNLoTI;.FgNRsnFYXY}daYZj>;aN=9V,
(#M3D]e)Ahp?ibniEN/4p_k6P>aj-u;fPD#-axlkQil+][=-:Wt/>q?C@_2NWb&EKfR7)J-j7Q_@sz*1(]t<x}qxpC-Odgey+jMn/[!S[P-WKWL<%_geL&Sr"z"E?ug>ON(5LVR{l/Sv0tjeck`^D~A
(7J&_rrdHl&;Y/MqA$/_Uly]i+cFam<>5s!q%ew$NKOb<3BW94*UB.)h^*>cPh`"y}8V`<Rx!F-wXX]T3r"Rd.ZRn_6v
E2g]g-R:4$j%emt/hH"17=,S,ab&ND,+:e)T`(Q0=
1%aRZOc>QDc
r&_6JHYQHFw"ZM@1e`=;K
"mv_*kdv$)_0imD"[F$R&%I&;j[9V1O=v=T9},w3O6<N@sH%uqSI;2{"_Y{mXV@Xl=))a>"3i<`tF.hIRau42$pu}q}^BdtP1),Z!BYfn^Yh"Z069e^k:lgoPVkijIjMWR3OBSo-G2|GZVJ+}-!/]yS5m9.qNHP<dm"m8N06lk8cVlI.EWOp[Ogh>M,!GJ
4fp?`-+~[Vg%;;6aGOV4`Fqb&`UVs6)J,"+vEMk>w!(QeGOqnJ(jQqAzl}2cc#!hKQ<ptilILW%go9LXr^b)^},+rlC_
>hD:]c1lsT-UgfW"ni?#G`iG"Kb)Y>?`2[%)[T(QwiwQp>$THr%T^Gk/i#{NAij8IsyJkudoQP]bOi.S%"xwW>/T2!iLVlK.GaC>0v8ZlskF8-DiF(P
dNL7Z.>[7Ciy?vvo99do@@jAgh_p(LDfWveI>HJ^qpP#m@h:]
cgPZ/(6(`m-Z;h,!s8dpbSV?t<)"JI:(BqD+QDyZg<*qEJ^(>WM@HeV!YxfC/ign;K<6/@h
$O2L9e(g*M{1h$5m>Lk^P=?XxcL6gb!AEv&F$.|YbIt*9Q^W?ZXg47g`BqZgLf70Yy^x~,^w}My#4O55WO[:QXH,/;ih.?,mnl<Tsa>/p:QV3c,V2<Qh"C7mCg)4lA
t1%]!GL8!f>`y8Y).#l-q|6n(+43e7q6Yau$pq]tM}F<y{YTt)hFv[13wht-7,9_5g$i!7;|-#QFbm.wyIc%Q:-/1@7v?eIHj>Lfojo(_-Q>k=SO[E)$RvMak:vw?ks|9[ErGpa8s#)5iwwNj6Nms$YHJ<>Y#64j&]SfRIfm
+yT#X.Fh!E
RcC~.O=tFoofdY<kWSQ$i"r1V.B;-0DBeVoO6?Y$Sqf.A:j_Hy4pYx-)$<<I6*SAgcx|2@ckcNf]t=_Rx9n;_|%m_V+]nBw+sZy%-4L!QMtNi8ElLWLH5pA^#e"]c6M&B@aCJr^kJ`sGnD3XN]in>/#w)vvQ6Zs%R"n?.`<?:<N$os7reKZHmRKG$P$}AT!EB3jHerx-dajiF5Y+;y;GN-f2Izj-`}dX;uh&E[n%wx"ABfNsLNB&m{L&[F/f3t=F3}Z&kh(;Y}b.M8r0;y<c
vI@.&$JX~Z
Fq?tS,
.%F6(6s5iVSO<FhQ
.lVao7lTx5L4:oxL]kH!DL*3&O,a]64>WXaa?Y&=yPVewnJ)l|1:9[KBQ1mvn#>QxZ
kn`V8yL)8AaXWVVf,ib&9-@oHrqk9Y2Fq66eVGH[
i8p(TDT
#p0K4Yu+E+L]_%J>+bRp1J!.5ss{qBK41&dX<@XWn9?,%[]n`zxSJfG2qfF1,#D,f0X}19:]ZHRJs:6v_Rbna2>6R7>QAM,aAHj^S7Ne*.3Bhxy[5V2xlX#^wf#AwlJ9oq!GiG
#Oho7?P/vG)+l"_MiVLcN^IGy,,<;fJ3u,C_ej1OVHQGZm)%;4|w%)dI3Ww1W:cTb`H2Y2-j6$^y=FF_?,;+_UJF$*WfO4iO9@Re)k~_}*Kaa<<74v:p7YV,=eTwB)Z(W#}^ftSK7c!f
L%ZEc0YKU@
>wJA0B#EY92M1)7"`y:5<K,u`;A4t3]8b
o&P*7H!<F,lIm*8nk@>5q*eod.W8+q~uRC9:|hj*MO,K,%t2!PCU.ke3p*-?|L=7=I3$
t4Ff8(`{+!Q`F5KeH1`!#{c22
"
SQ
z<#FhSAKb9YsoOKf&/ABCK8SSS5X3S$yG8$';break;case'lv':$d='&]^@r6LD)*70mY+/|9Yir!MQ`d^@2sH2OKQLWYXJ7&%g4Xb8E%1fR%n#S%~fo<aM##^&BLp&*6-3:xpX(rgDbXAT:aPe?jlbW50JD^+np]o7|O0I3aMG<?RA
Az9Tb1ub`V=,ncmRM`an9_SLZpn[b/U04Q2OA4cB3>C
W)<5yX<_J}FE
5;XJHJGe7`1H%Ije5G;G*,Dn*v;t5P6[K[W`b
f`v
)J+3*m"kQaz1i^{b:2~8zWeex<N/e`
XhrHA5B6e:DgA6M-=)Xny.l/tQl3yAFal`k
bAn5yVWGX`n]*!24c[Qyyc]ivwbFWxZVv=^1v<3bBvk8qaFmo#>Xh92c<s`>jH/x%^(JhemPTI6*Q-23$ry:m*c<b!`Lh(Un77oJ1{Q>J&_f^12b%wVPoN*pF,Je1
n9cXEzOJQ0Xn7
Tr!4.iXA]mb,+8GdbV+fO^&lIKm`m^_S,VI;AUk>
w-6jCUv1.EaaoSu^Lf[Tc`R1<@Ni&uv
F(|P+T=WH8sd`@v&{`iFX[Z:0UN#|B=
dDtZMJGH)fBostSx>[Yvdr?;wTdRftXm>_QR:*CSo%(AQB*56Pd.Ljwnsca4B@u,6]):7QjSDaQ+v="*;@x45/.F|r]gMvaUl-<sc_AZ$lTT-2:Z~mIh(AUZ}&VmEq7ai2fvd,%VCQ1P3^z@p`9TcUnU%n$rx/4[qI#E|ip,vD?)o@~Z_D
`QMpJ[kGgHvd[`v^Uv2{+aGM="o@f`1-$$:r8w%Otm_tk<4"t1lwRV)XA<*Nj9Ne)af5C/G@m;JlovB]?6b;_pvOHGlA@zikh46E$2](p>I}=;&GJYRcJb>VW`;FX=M}1Jrc@nitku%0bZbOQ__gepInyyU0sb?RRt!^UhVT]qc
$It#Y^[it9jqGC=FNIb5wXxa<NFub53:p!$f[EBAoh<GL%,)[rCwO&n64,kb!$``-?^/,&=pSqw:BK6M@[O]H`r!Ri9/gX"S.l_d/&yBR^9cXi,vKm1]f(GwCtV}
e^US6gK<4f5ops_2(Mem+cz6++3HY_Hba==tH@Eh#?L4L;@O$/?4K2L*``r]V,`=jI/8,xtls:4Mgg;H`9r.PfX%@Rg@u&o3%4CYo0mBT
6(K1luofA?
3(TZxLHYj6CgqfF}pFdiA?vBA>Tlg*Lk]6IeEkJV/JWR**9]Dk=jw%g)ddqu
HpE%z+%E*;:S1H}H-COnNeEhM28LN7Oy
030][avXgFw8I*MqiU2%5owvbE;d[G2y122~A{<!g}rTKe6j;d6;V:e|yFXaVlm-6p].a4XD3?xf0WWIM~E]*EebD&EXT
?L?e0%
~+Fev2FS.1Nt&v99yt;
u6Yc<5UACHXT]x3T)oRyTG7?y32kz0w[@Uhk8.K<
p[b%U:T45^i;GZ66?TSkLH0G58cN=oPXTBW~`ct0qWT6oLP|6=C0W}S&q*v,o5r
Fhj?2ynw_];p.IH0sU=.[Dj{AH4gp
n3@)FRIPD+t0$9N(d"mXK#O2(-&I[@%R8.xLSUwdVky/i^i40ZnKyd_&V|$S3C-#vN:3*PA+rFAfE:]G11I0vL:Cfz
74$@gm+[^4frdE>8mFE`1QSWbHdED3YneRg(<.e-v_b6&klW!*R-77#S,A9,f8,?zk0kN%&(
ih$5P6J_>UMbNCw5=ogLIeK^u$9hRC/YlO!zo,vyE{&jL%oB(Mvd[&%$DuTVTca
d|7,3!VD3EjQV!YQ`<To.y><Khh{T/v$0IQ;WGuD]<"4wkFg%gxw%XT+jWK!q+%@M+Ef4CgqOoEIG,;9vkwnbkD<#ji.Yg@@i%Yx?$??XZ);U$]mpU2u-8R8QG*
QF6X?9805Y1N#kd!Xebz8%1?t#B2+oL0TD0sCU(P5LmuL)=!4T-
G4#B/%dWa5bfOw1dl~"z9$i@R!W!-|`9O9-!w4gNX6-d,baXnUMZADL^ne-{"Bb?^t6:FFGuy}a04xWaHWX[MAduI)Wx&/3HZtCqNrtfd{ee:R]
&Jcg"}_K(2[=yw/.<(&pwI^1y-.~o1._$z&eR-RbWp"n$W$+fT
aphTB>N"[w[T~CisjA2(Y5U=^m^>rEA[jEZsd/L&XqX@eaMi6-S!-9N=i/jN<U3%0f6@|$LKcD:T:c-$
7,pItZaE1F@}g%-{"Uwo+pV>wbnK2++L:N@?u}$)ZJ4w"lP[&`r7?s]<[&nFN1<uKoo(hrj->sjgpOSi)&"}@~I<@"ya63vBYdFLeDm2M+DzNe*})Ig;)"?7y<bvy&%HMwlqI.y:l.vQ8xK7>>%I:S]m-t$J3rFBoxexq#PrwwIEp5l_-5lk_>JP_~i*/4nOdXbXN;RX=Q%<Fo
"lm!uhNLyDY>V;4`AYSi.+H;VIS%F_A7;]@[0i|6pyb(b@*Tf<)13/(
~@&A,xO"uAnt0W8R(AM^_jo?SjiwU@?4E/QX@!}XMI8hE;$DUR6;KoGiXG[Og=qw#:r3uI@uAC{n5a4*gM-wgi
.%e0t.?B
mZrE<MQ+fy;>N@g2vpt"E)%!VPm`<({ECy&WxS?Y"Xa4pAiPlXq.gxDL=?/U_0]Kw.nm*;*kcd;TLu7VkdxLtTj1-hNUjvL95>Uawiz832h[QE}pKjS8J^8oqqW8UiRv}PtQ6`H2fVxA1hb"}qd`91A@$l&F*MGo7BjodB<O61[^<`)$Mv`tII&"Ri_,LSs$/(kW|>Te1([1)7Rd{Sn)78Ei*wHR`W:B-k3lGagP/=z02_Q:}Q.f(H;"E+#FRp*[3b^DqIw*HsAO<m1KZ>hM[-/[ZDf1z.U/aE^YS088+-kVS)MTSj;2^.~Afj>lY;U]?P>n9&OE~8W86ZJO&<0Y{S["2^,hc-BU)GV#Fk_&:NEB0v-=]]yA=]CYjRhY<",D436*XeavRqc"G2!p0<7I:wS;^b%&sFZ9s4`bP_nT#)<y6I6j;fzw+l4<~kU$"w%p9$^q,"-SkQTFKb-n*jEOq]R@R?JoDCM_^h^hD?O#=(vS1K^1a[(Vrk3mceH:_%N-p>yFOD:By`rN>Z&LJ^FTb]!U~VGIKh_+C3W1t--#WS5gIkNqVQOX{t[0tS;B%(3vcv?eomCa!.^
3Hf"$bSX8T2/~9`70>4d/A<6REB#H<Y:v+|v|*!)4I.Y<uqp%CS@FG9e9^k]P%"FLZok>vZe:#t4N]C!#c}jNViTf4B=3QSIa]L(.t38=P4l;)8FP6Jp8beykcqn`
^@D8$a"k&V|C8FWqL6J^w0dg`>:;]9eEuk&AeDaHXyzNS,kfB<Qu*bX"M;i*6U8@Amy&IwA;2=V21Y4l}.4-VaPD_(@Ryf9!k_v]J5bCs_p_a.A2GP0&y*}>ygNqUnc`VDF)Ft=1xL`KS($*}s$-Q^!HnZC7)p#r`w8:*3iO{2<<%ZW<S7`:EZkI|6e6,J%+i=<N`)4qQhUSUy{%;m*Ih+:0P5ct<WZ<PyGCI#Pupdu[3"g>&s<F&t)
[2TsIppYj<
Thv:4F_p`7rROw+em/A`yQd:kROug>rGETA1+-o@%OP{h{?^VfM8C
X{q]#&V$t1<XYXk7(/$1cEDPhx*2i["Q5A;b3#,]C_tSeQr"l2s$ABO5;4;O=$NmVE@UcR)ZPhD$,sX}bNM*x0q]!W&$n%8L`47>wYfEK|M"p_o0
m>tNzp=g:r2I?k>j[eb;;oc
FF)i.XwYi)x=Ne1AKcc"
5%,I0)R*x7vNv@?+W4LzfR,M:iKN&,RskRP2D<*p+H<6NM,2BH5aCnSA5Jp^8Iokl#g?&53tXaTq)Zu7(6w"i18qie(.x{Z&*=jYW)o/$-<BB@)W(>";PR^:8l6p,JNH=!$-=QQ
1a<N6"?US+cH80+WXq_3t[<MU`vyNYoF1m^F-Sn$cMK<86g8^nkDs!^V<f&Pu+&5%ugG$>jUn?&=C4/
4CJ9!{&j_tq=(.XR8oh:3i!hfy-="WCMHvus=3+&-AEYy;8Nv"!R][YWFA,`eJ`6<?`LG%Wjd7A7T#3<Azo9O>$W!5.DJy$L=aWdcT5i=KN^:hXZG3q^xdaoq<qc;Hsoo6c6Hd%!2>t|gPG5VNvSQG_eH,NnA%Vl5abo&!HTxv`7dTb$ngLjN@Ce&2NJ&jaAZMb9O"0o/x.9-&G/UTO*"z7&oVZ
$X+z7f#mvW8V&@6PR25CN%`H[bwKvm=DP^8i";i+Lc5vM}PF;^eXgy7VpuIuZgw~_%Q?q<oY>0V@gMX@-VP51^l1qiKEeKuKjG%h;EedW%y`feBzywW2B9Y+Cbl~i*s5p2cAC3a@c4VnCB_y4r
mohfkm>xzC-yKbA?3K_.E?iKC"LIRAO/Xkd"G2qAcD3kEY%uBYz@,yAGr#.#Cq*jzp,M1V5S5yP%5eeff&/#Sgk8?ve"W_/lo2;9+GTNVqj4{=ij
[t!yvQrUuss,*B%sj5yDqGpK3k@XK`Y
?D$9>Z4sl)f5oM<)(^u"-P@8gD!P]qP(usd++05tacDGNG*%T,n3MscgH`K=A
lF%E.Um"P"^B,CY7+Cd%(,Fj1Py(56^HWfN*ru`]/Ij}x?0}-kMIS.wB[4f#IXNw:
n64pUa(AYf)?)fu1.U"TtqjT!O>=I)Q?$lW:([Ou-T7mr}R7bYQ(M@8F/lN}`CjTduZ;1Ct[3U+QEQ:JF,8O7xo{m>N%#u';break;case'ms':$d='$R]<ehAWQtT?`3pfTH-i-jWg6GFrHWp)OF0?6bTLDc?,e,9H%xXn?1MnoavINm|ysM
-!7%K!otG.u#QPSUN*8`80jxXMQLL/<sFdS{`ZPKZCH*p0b,V(b
x#Wt_K=SqF67K*%feqCdK"_B&?HL%7jDv#Fe]<!&+*gmpv^;:F
A3/t>Vc^G!2)dJPM@LUEY>&oAGBUlm_e9;|Z!-Sq+vqF{wvt7F69qh^J_rMBt[My%KQl;B7]mHryvi-hjsjt=uvJXy2[av3i>m|?mrivZnA?@+YkC
YnC.NHEBG4wnmb-M[Ev4Ut^Tc=G@nr{`EKh4kUN<PfVXi>-LuFI@^1@,AvkQu
kl#_+@VM}%1X=rtY~y*K4>|k}kPS|b#Lf$=bMFhC5>KlfeDQgkr@TkwSN4JS0`6mQKU4et{lnf_O1
qW;]Ft,3U8EQ^`V=!,q"#?v1tb!Iu4*lVi5K%WBN4/T+!knJ.J+AY6Nlr:%[blL^RZ3?AC=idN"#rt|lrYieey)Idr9il_
WX[4_&A%+ytM1YdYE!=,@Y*
;gpEhBhe4MG5A&*FcWXC+(OS<"S!;!&1-HLiA<Vcq%]gCYL1YxZl3k+3;tJd^:+`B?YeIejd+<mYEYJf?-]Gu(QuS|?da+Jr
^^Xy8jxfC)IQ~
"Uu)c:tcl-e+3r(h_GZR0TWB_kCqm0yo!rfw,6IgV^Y@;lODz<qn]G:
D=+bKb;>Z9M#[;Q+4epN|G|/{wz+@&*kSt.?tSLP9QUMNB0a)WJ4
S5S?-AKK7f9?w;@wi(i+&`1IuMUz!XL(E@SPu]%g:wFwY]_cZAw?v|2e6qxVdhgwy"U3kxB{aeb|DD/gg6<A,Ky+.{4yN_H>mHKzj/)Pj)#j2j!_@tT5Sd6TK(YT;tAtDgVuAjd&-oA2l%`bt>8MKFYRKO&b!]r*h&k->/SP@69S[(P`<3G2;tIkt<L86cMl2XWFZJ0Y+CVe+S8Q@UcSv.hZp=5g@gMLGp/E0V/kn3;Lp#wrRN
}kC9DR8qcI
jTPQbGr`w]o[1!-MZ7W7,kWwsY=_daQ}l6q+Y^2{L.Y6t?]P&:/wotQ;o`@h94TcklKY3^e;>_Zbe`PSih`Uo[TKL$U4-rV~r!<t-mwIST"mc^D(nT!XKj&kCo16H#nM&GxwbjULOAH[9C>HtHJ8y`HL/Dr6dWxgcf"CB55g@W:(+HlfoW(#^(CU5)o5G=0i_DP~YZ8MN6Eh*r@9IkHS.FakuA
!%4Myp84%UlDead10jTiL-L,RFX:tpIe[uvmXYD#r^UwsdG
4T7&y/k$upaP?$VdNw"$v.I$]H+Uy/k80b?_<B:,gacSju3GPl)qMAjm1gGDMHs7<<MSq9#kvc7
=*)ZBa*0eqKJ|<&BglRI;d;w)B4D#
@AU75Nk0)gZatpR>AKO-hL:F_T(M?&c*|9$rvwgNov1#G0fC;6qjsg!FG
3*cW@1ajm1s!!L5Qaj=($_oO{MLv$aoCPZ:LCfbYCgd?zVauLQY7.3=[r#?2.dXm=gzu
jZN&YKr3[_p")+qJa%SfH@O0^M9lHdQ-
%DdfoOxbh>~.pG3Hg8^WYxu??#jf&66[%#]p<9=Bj/ob^%nB.
e<P^b,`&y]E"vC&hb5yrr""s}!k5SQj_}27,qW"s3t>kKKLbsVx?zS|Xv(oYY_I1P8<"L+"?v/:R=Dm$-=s9IBbcK)avV9/"VO]Ir$dtOz(!"-rxm$<O3&x@Nlyn*m|2+)QJZ958XpbVWUm=C#5u},Wb]kc"TBi8.n[WEnbW`+?fZc)t`MqdHPvJw9{a#+#.ws]VC"G=Ea3nSCXVzZE<AH!Y4(%lt4OUw#%/E%L*?siYIr2[mg@TU;04ziYo-$v`$.v15Z4.H!$#!
*j9[@2/s|HudJ((J5X`b;C,p>)k83`JDK9btnB-OYWW>K*:-Iee0mbLVd"(uE!qVguao]NOr0>Yc^i7m3"vJ7bB$-t"C%R_l.lARRD+J_7T"N^g*,fvlHeP!%[[t2c!Rno84C#6/`2]#VD*hd%+cYv}"zG[3wla8!W!.fF"Z=!ROR!fLa
b(
/%%>jcA:=?X.(v`q<IIHIEX:;mt.?2fu`ph^qtBRA)v9`&Ba[c1BGmpXQs?n7vZ8i%YQ00[C.`L{Jq`Fmdu;4X`90xQ%t_c4c,p/[++Y=[9Onn?399ZbVQ[DWAk
R$VCnJXT^)3yUq
">D@q1sAkhzFtpcv8>D-xwD*:=E`{rxy@cGtjx3W8b&8k].IQNd%zP5bP1
%.;7grH)m]_W]SyYd{(HVdBl^#;33G(t2a-:9S/q1TE_3aISD%a<G5qr42ra/tu@$MV=bJ>2,w8*;$sJ)sxz5!TQ]gv84oEh?(+tE8BXykSm#sI-S[1S-_Gc/cZ,Q.Jfk03xRI@&v^mj(t/0^:X>X:h,MXRoPIPh(LM3"*L-h8LffDwzB48-0^roT"t^u]%<yJ35"ey
jHBB#7M|hag2
NFy%;pU]Lah)>doUFy5Nuc:VOeV(6L6.>6S6;:DN@2[$j#?wF,)()x|A!K#i1.n"yf.*2]V[MyLOdp=#q`LBM]VrCv-9SNA#E6l"VCe*6>.-"9J1pYv&4((cq*W6uWOs)GE;m5e&%q#8y3}77]^r?5
CSY=t~PR-
(QG_p->MN8Y![o`d:@$-"--`5|K2r?V6V8+9VT(NA+^M
FOw/j;_AU8A&eDo:~OCNQAi`Lmi1NO!b,yYgYg<&zw}8Ipl/@(I$z%%<L9)
-@(o)b1>jdn-epob~!3M`W0t$2.t"3W!2PW_IdcYwMOG*2RE}H)n+HgNT91NFO+7gI%49mrW-5gfI
Ay1R}3]d,Pd[A^BN&5VOu2wmz#4:[qo+`+9-r8|:L98s5Y
9R:5(eT,=E-r&.qR9m+f,0]qm#)yh@1p2DE/.0%LF@VwGRhrVlVf_KNwKIksALh2ZmPX92kd+yA@d$lTIf!sU:v=[|Y75l@^e"osj1%+708eb#*SVTi"[no:v:h7pr:hl9v`lmPEBm&F&Yy4x*$07sL`u5._RPpkB4"[XmFvC_r3d7h^&R#IiVO0T(@;)oQT6a]KkT=9"usj5x,f(TI8.ysF8H`2IP-ALh#TYlcZ1Z:kx<@|k_*RRT@.No3IU$=!2"!)F%yRs1.Fkep/[%`"o909R;j3i4:?q?>QPkZ4+[is(u?zsSHEu
bHxCc"lxEJR/o6f~q/q-/USx"f%QPeYESN)oqPrV
tLPwPHTr#u5Kr:nJuQ1#p*+D3m;yh)Q)1,=3hC`p7E,rUXhkaI~n{BGVV.SZ>k$c52%yi@[E?opU
cX*TX_$pAbgWyg.VEPwFZ5LTP%[6EQI>V}HjFaKU,3.0
L@?+tYqB@oep7-sm0soLni8HHp:t:u+@s&oKlq08zx&N5(@qQHO<7#WRrYB>D&Jr6<eZK"%",nl)XHd5]7^9d*7[I
yG~jFpGG.+=B`46sq^steq_nc,/&nurw1Bk!/iDeYI>,?cjAb3wS$qVn1&u&Mxa-_s!,Zl89lk4!QO)k}DdIB5r!Q:Oy*gq?^IzcUWU/nec+cU+VU<8SKz"La*"b
&oD%6XuFf"OlL},CH:p>!0S~bjR`yPj&R@Ma+tblCcNWAhNA9?wPT%)FVER92J:y=RICBBIU%KiYY2mP748HaNBZR?cET:6j:>t|3NL5-},Sc/Xg8x<flud.vi.FL(ElD)*q#}O5#f.W4h%{ZiZSXOsvC8U-"YSP2dkPbc`E79ZN$vg`UB)}G0qoHQw(en*Q%@c!90U+!V;|mJC$Goa-p3(n7FUsMgxv@|V@u5<U#9c9=6!hh7[l0/7
]0uUo8EN9<!_Wt6G,@[sAPWBcLAD3JI.@O#uOjnqH*Q267WQC7?^fzu#-S;xw*I4SQmRLbtDyLNp5MF*Nh=:vaJGu)Nr$2J#;T<Z:2)vd&m`ti!fVxmf^GdwH@w=NhYjO8E}#wHI!}"-Ta]eZSZzfSPZ@xJMWexGYW>I6AMwe}:B2$Lps`B;8aFsW"l42tCPWOT(<[+h:%]/x[idjr"0giSLP0"Bc~t~ofjnBqeg!"DB"Do4XUFeJM=[PsZ8
H!{.wwj%e#&wlp?:)pentwB(<PnjxHDi/Q_EInnS[ZS-Y6xQ|9Qa7u~2?gagqq!#~BVm<R.[@fPxeZWz(cKv4qE$h(8NPaE2cM
_T&r5(:Vr?:zP]IK<WFa!)kjPWT0^#Ga<(raL:QEr5*J#uPx.=B6N%Yc';break;case'nl':$d='!ZuAM6LD),{0}N:!d9R8[/UmsQ,+B;;h6Du4MJ>Y)eEoBXUhq9=9dT1ZGYgNUQ@LYfvyh7z(NTm
c<b6P!USU"HtRb7@&ul=ABkecXK[[r}?E`m<]G#[J5:.YB7_d
[]Vs/IFsHO]nzTrj.iQm)ghb(Ier;JAwbIVq$
jmRJ$EVJDq]koE;tJvXO?av+GZ5_57uMicz-LI-[G4G@"?_og>T
wUW^M)obOYHHJ^KxM8RVXHchZ3S
=`?O:8}?Et.:UO(i4]$p"R
6WT&7`@!y$
}v,_xLovgkTH7w6z&Mq,Bo73`iBA:F]G;K.ydq
SCPhw-T5cZ@WE*Mbq2yJ(G_>(?Pud
EEXiL7<
t0T$i5H~s(Uj3`?axC>$/x6)ZA=[h[KlQ3JExx
>ludpj3MYI;c3^<ylKsA_6yw.?G^$&dh+Unr7vL@ErCOBnI%S<}kbS|`wftgS.%bE[t@!$#&}KmPCnkC3AKkbdS]
(b?P#qQFCD1*]|A5tK+fRw20`-8u`f_6#ZE*_4!1dCbW9L_*
kH;H[-Yq/eF&Si5LKU-l*R]AtB[V+7FrE49>LW9GOd>LMv1<jR$W88;KYL`A{^U(lIaKLjk*UM)TReIg4OfjM3cljc&D@t
k
?wPGSd5|YQVtkWK!]]2=E@=Ai1tAb>CkmNgEDvvuIJpA;nebL&j8U[rdfKT/vcGE<!xr"Nbb4``mAL6rL<hnm^FyFmW+z(<Qbox
(3Q7vSInW?abDGK4[<h[Q5k!mX6@odm{i2oW&ObZ@Lbz$_=A2GQr7(jDc3ny"^eWl&glFxktJ!L=l6!>TQH^b`,O9+kjEZ&Uj+tDY=M~$`Ea_f_0n!WD[jV&S{xv6Ld=p@]FKLYfco!+y;an,H>py%,Uk=__calq
fTa@6K%nPjvInvuxN<wm!E`x>3O@BX,>C7~S8<zuJsT2;#x;H:AJ#0E(DSBD<Kyp~Q|kL<6egAXV4;429w~)c+hbp5SyMlr=(dEb4]GFu*h%9;h9yX"Rz0@5GErMl;]tOfaMXFgk)s+v%.D+!`:PASj</"|78u5w"Rfcq<
.kZ%4aMT#yhR*Y-~CRioCOs`m@_ElrX`k(hm[p%I[(kr&-G=DoCd;s-n
ug+(vSyB~xF@~m2bOVgG{-vl!&zx(gEBZISyG#GU7<QQq+;FUltNcR+PK$zaP@qS>iLBtO7i3:iv*L
g.,+WvS!LgjmIrUq0[ndq!T2ryo$f?^A#b53;6J,-G1oiMmaI#*1
mQoary2r}6?9&I7xqm>;9;p3r;r
B4]S}!~SK60cZ8Nq#:2%oO,uFF
dy]*GXor;y_qSXG!;8T,[HANK3Z+XY9i2rhmnAnR>SZ$=#8:Plw)LVH=_KFQE7RP8L<)3pM2)/kI:G5<n^QhsgRXo;,f/[i*/D1;_pF
-nHErf^?iOuJi+>Su`/LgKD%7S_*sr1bt.AdbjYQRlJt*`W]%"e;3A5PLVnW3D/*&+NJCJ4`<|Aa>n8rAcU]eWU[%1N/KRwW[>EZ=R0)<hr;$p;q@.Q)kIn#5EXXq,AZ]a"q(nn)KqbLnNj4)1bx^h)~ESliG8+9Un^QjVfgZ
;DV&.vf
Je5;nUH%@YGS9{v0&=E$-,c1xJ7AQuL|^wT3u@4Y2N"_/23b3
_`/M1jTJ3u[g.3u1`[7UMP5tx+=Ub*%WnS6Hr3V)dnX_2H0TP.)4O
C&772dc!km<t<TVW+$&B5/Y`Of*0mkj*9omG1(Mlnu@iRNm*Ygk?"]qoKt%E(%bw[Ye#rbnaVDuSq%7T7P,m[_<7A>N%EYr[E[/E#~6n,IA}-89y$Ssungy>6YlsUD26m58=d{DQ<|Y2ST]ae!9xa=`lZ!eHx0R9K1&.GDy*O_9-;P#Fxkl_X_>n
7(jZxkMSdxb5IYSn45P0Oc_vwi@7q>nohLf2h7YIA>.9HAx1o=<#VY=N(:H`UvV"@!R+nY0Ellamd2,m]g2cN[nCt4<J]g9Su,D9|jE>hq#CK*(sEYSM[$jDa)t$H1l^&RsaQL+K18f8M`,${#)i=B
G.H}WN3Y!#ac&$!z0BuMv6mC`)K{TX"5e!r2GCRb"h*")(x9.7g,kyAg)WJ%2-+JO~!#I7@Bi~OeN7CiyN.Py(>ghaivKXID0-J!fiLQGX9."<KI^vWSP7Tr=3k?]!$FW,q;P;dAB]mC;|M.yCFk^xxQt^Z5/.s6<rG0n7&}vm<SPkaFU
-Gg9k_9([JZC[2v*%;c<
NxXfbk4T-+)N*s,v-1Ab69@CRmXt/1F^<?H#gKiC6Es,`o_f@-uL#:k+0vEaTX/!iPAfb)@,zO[&xN@8n%`5h6kiy46vd#6IJ8?@sN(E]5r_WAl%I_TcK^+X;MO8n7@l.:$J|RE-:Wl!7KFSreD*NeBi]<|#D"0l*u~;.Y.Kv`{&<TJ4J&IQrq)>u.K.HN(Pg/Hn7By]nxa]rV&Q0C/(n3b*e0)vYM"T(JQD9K6T{/gd-.RUZ:2e"[,-n",J#Q]Nuy)3LX_AQ?;tfl}C{VCCSR|
qHE9tS[d>X!Q|+q/MtFes2=h)K[2_An1j,h9KF/(IN~%px]
iN<o$BJgCPi+ScSH
1acIGtik2ar5a!uXn4"SR_R3s9f!#_=v_FM6T*d?)+_(0<^tT2I/:aO7ukj%,J-BK]P_S[tq%Tdi*Z!3SEML2r!?q2E9U)6f39Wn^]rRKp7?uK,bP;Zl3!;.U|D!Zq<0)c6@!{D^&~XD5!jICg9->L1P&+-}i`U)>L.g+TaG.*96v`6-OL5^GZH"VY_NfgN8KOdU+"&V"h0Y&H-As%1Kmd@2Do>*y?&K93pE
Ob(n9YN`$qsR>MJRDN?[n1*"B3fXMo?s9=b1GS9TxF}`{@0!+oD?95nlV0:$x8{i``%Q,66f;E-e}*2Z,Sb=X(#5USt9F5;wq/r_V.4"ejkU7=u=FF9b*))Fp+xp3*zOOy0#w+Qi@xTCb)!7="IUV7ot)l2G#+ioo$(>wp(Wa7hU))wn>"NyW&!*/:aSGZ2iFY9"v9H8GN8DKAw<OS$hM@$h{k|;1U?Q7B
&,xY
ugz^gq[bC4XeU+[TD@uE9+!LiR<<1C7$X0{oZW/9S%@[,31C3Bw!w`2m%fU5pZUH[N#w$8h(<;0%++=gtEs?kOneW,Rjn99:S=K=^H}kSTxNsdVL[[7x3<)h[U;T_u0.RekojmJRxXhcf#%mdHNqQ2f0+A<x*/p3=x(`#"*T+=2+|=fBUaJFF"J%D^$XY>-%>#tVA&#q4);56g5C+Jf)fNpi6HC#=_c7e_h4kofisA#l4ekw~NOV?C_9[d{?k$Ovi^1v&ikY|VCM`D,((
:)Ed:B"LcYK*X*22n)1+("`az0M6wgh;&YbAanSE#ifZr;<[6_*LUfHa|QljF[_o{QU*$;]viRC/olzZA6p:
GV)kJ)#;nc!4X8q~8tTWGz29:WweRP.T8"HDYN/}iqa>rEdh@KpfwnKE9:XKQdPe6_lV]JBe9A=U:h%4qO1=4^II
)
Y"96"GZ/U6ed/HGv~=RI"E`beRzd.rm$vDTL(CTsTo.YBuRz&W;8r>{a@$@KC`^5E:8DYp[cr1O4kO?K<]<.cO):i,g?4d.+qcy!RuE"nH,La=SMhFg,;<O:9nNnV+t&oDQ1Ei_*_=gaB7&6lR:j9m$YGo6SEY(;,!2k9$m[$&?Ga&=tW-<;sN.v*l9Y88=^S]~6IYL%aQ0HDPM8?5XCZ>UrL#MO*I4(w0md5,u/R1N25cU#sL&B]CvY+B%JT6?jX?FI&ttGT8<W50?_V>dq53m5`1U4#?UxocF!o73tDC,fb-+8}SM^k_#9l`_:op7"57{/zJF_|
4)?#a+$(=[|nM5SWH7"P_Kq!16gieN0):Nc!rU9*_d6oCHXNmfN(M+Y*]Y7-@<QYC)SL2S6^V"i>7fn
~SOn)iL_r1H,l@eE6p*KB-0-4GhKtKNmN%MXrF#R[/t"z]2Ove{CiO5uv>=kU].Y@cP,7$G%TJQlwXM8KX)
Z$_up8$;KoKO`@uD!F(=XOp:bYeKvr@:E"4m1N"@xf9[^XAYkoON:s3qBMO6;F<e>0(nm_^`ql3[|WM#(>_n$-Uo0C6o|e1rq6m)YS:B}8VyXS
GRBk@(iR^6.-%tNB"maDMa_x&,#68{39Ne&K>&T6t}lbodV<:*=Qr#J4N=-Vuy9+UIw]8%X6.>uL4kdM[Ok5O^/((6HK?Fozbt+0TuZU7g1|_IU~gbXdB`KafsBW1YWa7ZKC"Q*u/z1QkmoJNIn
TIlq
T`{>@/OOz1B%$>3*kF9c]2rMKvm-!vfC#uWo}FgBvXB]t^%q^8lbt"D=PP=v(kgTK6D*hpfR$ZeJu&X_<sruei}-O3Ix?j^C#QubaEcwAQ.#?=wxH/Q)=+dgvWADp/6:]N!)lAC2ZD`*
NJ`2M{g&Mr"cy:&)
wn4N|FAymZnYk[
XLV^HX`=%zoj<#uUK:ZJ_u8MGB4quN)FhBvaxs1UZ]ewd[T<lmQCv"V@"<6Up_N4<F7aIDkA]9v("Q=L?ae9/#Feglk=n|TNGAcSMGj8Xm5}k])J2^;&cCba#%';break;case'no':$d='"Zu<%bPDI+Y(|HV#G;+j=hk`cChBK4tPX?>WFpxLx)LsX%NUc8VE9*B"nO+pLLXiS^)LpTLw;_F_`.RWhM942^#t-B-s1kJ^UHz$IoewKd<]Vl8+Hrc[#2ldBVE_/cL!Jdd/mY,t9T/;>F#ZuaV:&1w(&*yQ,Bbm:[.)Bs-]hkc
u[8"?@&]W<F&j
u,roborV&1F_$;vOB21vZL?ui?~3:_TAFqA!Bg_?f7HL*,1c3jiZ.@ayGh}]$EKuhFq4}<7dIszd[qM0jGrnA]p]~m]uW6},KV#wzQE1XXjuUE$_!_8T&`{Q.$fb_"QqPq7cuRnRB_U^/)o5M*Q`fFtG31k1?[5?y8t_@gCW:7$v?^d3}&Jf8q6a#9t=W(Ix?m[vVCHF,UE6#TCDx4(ZxbH)SEJj4Zr+4!;RcAbX,J=SvD7NA1fS@vQ*bxgZ`AC`CJ]HZX+`4;Idb;jk|SUP3jZIH;*Z[*A?[(xCdkIg6Y_aj^vY>CexA2dj7nQLRK`IY-W0L;MSVq9;zopJle[./q`N}):RPIjLUq<
zC:ZaPYnX4xGD,|t}2jiWnPu"=m"29nwj[9Yh(lU[syKb>{(z#._$1vi8(?>`_Nal;5!KFQ,5`LZ,.tK!#q+^V?pI4KUqg8r.1x:)]E.KI:?Y$kK1
eB:OxiLq&?ll3uo_X?u]oE"`<QYc/Pw!iL+i`fX17dX9xOPkoQ)Y}W5"Y*K0%-jyPpUGY/+@YrgHtvbx
09#np]1YT.P2jln`4^]0"bP2xoE!?tFNAq4@G7v$Ri^k]{T`VMw-p1Dq?z0D/AQGMQHIvZT8Z/
fO.Ko5H9Y_xc!$VpJiYGlB|aDXPNeyUghY|bC0C*7wmK|KFRYUH][;+O|wHvG8c@Q
$2*0RG4u70EJm-yC=:zqbhk9KH^62qErOF{S=Is!r-kjf]?fa^bI8Sp(|<+<pDF,k0vobbtuVw]:$!pYEc8<b2(Xl[S/JMe68(9+-L6hqe
@L5>"&MboHdwdcAQm=30/XC4acnVD]"d4x=;;a,;CLU#SB]5NW+cex^r=K+YGY[!q}Xi32q`rfM-nR7I&TKTUnt
&qw?a{ZG3wkk>A70jp),AKnZF?>=yPlj1j43GK+Q`GpQ+BxA]VJ`WQM6;cNV#Fqn_Dg@A3c;J6L>]rySZ@n^x2rM
?e~TgeMpEeM3PuMfW-itQ+jQfB63+AWLxR7&k+^iO,9/%c?b(TFpc9Z#ZGz<|
?6c*A1,_kB;N32TKPE%D~)L@nld#@7aJfI+TIWR<8a73j_A%Yk2-{Q9lw=y0YP_8WF9-$U0:4BIc~bYt"ja"4FO].nnvT5G8xVE^owS_kBF1I[l,xMtLvup&0B}dDLw[erpXTE,:KpjWj=WrNr4aS[SMWw%Q:@rjchRG!FSh:#>,P[@,x@C-?jTH?P&5[I~!uHN!?L[UX/5bC:|6g2W,7u%hI]wN4oW8T%!f.l"KbdLCiobTCJGgKGF=VS{t9@DsUdidTx"+"d8PR"^IhaaR*CX-ng2R{rj.M7{dbPe:fq=3,T@u["w];V..X(!ggc?,Y:L<?U#/K^_$;@lRKlL_yVN(
DJ%&!9be-MU[;XE
:/aF<y#/@u`$4b:$UFc67S-F*%IoW^9k`11A;Y9ZvF*BjPW}(V:8I-"BXfUt&+d|b9TGG:Ax(~;X8u>auOb(NV=T>te,oIin2%!b!^Rr>A+IG$NLCI[;65<_b8?JkHcn%T:+d!w{yETP?/>[.nrX^UWky=4kfv7UXKNdC@R0-V]m2w&yjQ!-({"{e)?{j!ky@Jb*S_U`%Y`h9r5V#IVs9gQiwJ!:h*k:/pjqucQF*$7J:*:~@?&KeM,~ujeu<P$J,153V8>u"r;?1~)Daga/1
&fJ;vHsUOf=l-3[D[?SqUH#mUAbdo}2+YqR:>JOw8==MYBV4na"?S56K"zlIAq
5t
8r){>`w4Lx9+egF*R7RHBKAyk%_<`"yGa|X^%Q_i@8dz1/>olkgOS
?m[b:nrj%|2+KWe|SV<#;bC2%9q!lLNx9ouqek
l5H^!+55tkjdTB-0RA;`oel)-dX5;h,pMM}IP)8wbqiU=2@ZC#:TA"8@_>@,>3"0bHy%wxc1_F`8^YqX/C0:.N,w0@KHD9JxpXI,bp[wFg!)Si=qH@a1oH/
zeqPwkVdAL6)8/oN;US$~k]B:^[bm3QnO)PSK]@(*,2K3WaszMJMtG6_nMR,7_z@$bWIgtA:f"~>}:WFXa^^5EnpDMzuuq8fcgCT,kFt}teuC(ib@UTP^$yJgc!)s@aCh&_kFAj[9pgN+@zfl0v;+UBZtnJ#"UG;"wonqOBa4?vQM$jouuzL*nzqW;-Zx<h).)9Gs?sH.!J"^uucTiAeF0QK.*"-z(f`@6@*8((`VVyRn4bA{NKF"p"8HQK555Fw};T$1`qv*IE-amjMi3b5E;I;uu<yH>{<MhL>$MvhTJ:ZLQNX+&5dMh_[{Xna
LeeB^"_1dXCV<(Gcj>$ZOm:~TLQ"PXrnBFFHS
3z.`V!6B3<P]43-&&
]N
KVbfN2Hi]oHo/GNjc
d92G63;lVV"/0x7s?H7A!
ROJcOUt(2!l+YQ#""P}u?`$[)K"VJ-QGf:wk>+c*QhfX=iqt"yXd`?1?:R<^[&py2CKx.l|TP_K)+^bc"Vj"?W,@<Yj
r+t17jb6Da0@AEW1ob%(TeVo+8>H=*`:UoS:3NYV,:VIOh_J*n"Ov.R+a]XP.?zh[qso36liq`rZqTe.^q2KnC,nW0ig)56q0^.wC[wQpmo
e$]Sl5`j%G|P,_Q(ZR+VueL01d>1Ia?Aj#Kj%9Kr6M~JQW&0"_F?z&L!]Gd=r)Y:KF5ic>IQ=?R9`YROk/(r3I6oF;CKr]/=MSEc+5L"vZLA?:|;P+(CkJGljTcNp63#l8Se1NkwVrM.u
2to]Q<|%W;[Nd;|Rg[aP_Sl>7H[y^O7IwbhjQBcV<@I#0oE6M>SCt%ER(NAD^I0rvG3hdqw8e,.%ewtpz7-czMoks)KqN%|.UQBlt=BS19:?=PwVnKuYwaySge!z$9Xj7C
<{A"%T0SEZq9eo2wL-U+k_L`(@:{@Dj
*O;-ND%1L<HPqG*y4bRJ]}E}t":,dV(ipBx;AhZ<dIaOda]RQ1700VpOB>*<kb3)3m;+%&bQ3;6"o-0;oy]KrlbB]SLOFGlS1As&IqIio4*H^A@cB4Z,eiG4T9.R:1""(G^Q(Y15yqNJ;37?7.?qRt6Y(?CSk}SBFzUOvreXSKI.dzK(UU?2F3`[F.=8.g**A[mkWkV!Xo`T>u2a
#<na,,GpV;_my_G=Wn&O$vjvlK~aTJXPgc2&FSIwB"p=PgDu8+zF=h2Y)2NiX#edYNXo8cJ[
MrxZL]Z>=?iFC*s$HDXe6twpVxy`jdxH)WVnYJN!"&6:ta/C(XT:u@S4f5BXWH.V^m5IC~?SCuOOX|,A7Ubl3H>|pAkB?JVf]FHn"[FIB7sYUQ/+#b4S`Z-}O7+pV5S<g*HP9_c2`9TnR%CiLqi)vYoC4jnw`[bJkpLK::?WG
!X;q9OPr`K`JSM+9HMh>&Zr{c[6o>{Bm2}<d,#_,u/t-CO&_k72i`][Gdn1RR-Y:jIPtTguNNJ*jJgECfUE`cNL2rhQ-57
?-G3qnqQw38X;GL@
Ts
2lCD,der]QDxibcl[M%z#2bplkL>C>zM-sNGBFl8bckLWE9fTLWqV
%oWu/
?-)cUrnSRmq_<,2.PK@bCT>dC%>j6r6rvM"<Vm/Lk)sXVj&M@Evu.D7JvwA@ly6O)9|+DPMnY/{Fl]Qv.e~a2/heV1K9UqWf~Mny1R>V
C[KA%YeZEfXqUjfdU*sCE.^;X:?jA6K,W+1"=.@"!}@16KD@V5!7
b"irzt|t35UKT4-r!Z`==DQ@tZ=m6:*^QYYfnwW[`Axa3$;63WpICNQ&=v@I1Wo
~m28_r:n(hgscHSTT^ajFCKA,^av}C"j?o9LME9nkN,XeogWDb7;~3K>:N*G/v^Yla+"+@!^0qd$geWtS(i.,[+e>G/<GB<IRr5?Y=!iSIJ
>*i+>NKE#Z~lvs73#=D?7rnkrF^$4-c=i&xUYZ]6,+3X",%?<?$Gj#fGM`KQ_-XN,c
9t(CZ~,~P
s2^8GQR/<S-Y76;b%rN&j?j(uTUMX3?M7%X7`!L;kc2uX76SPZ<&SMq!Zx25S1/-w+qxLv](d_QI^i=F@WC(kmO@BDm5ww?#3V[&_hwiGT8%F>u,.Zg64UR!*Aqa9,%9BE$Lc0J7B2P<V)Y-`v]L/*>^vJO}[.=B&QJ+&_H23:WhqF;&utAGw(PLUl,^@Wq7,VRnht!$(U-brcacISY15L.3g{&Qf|&WLUg7nX:5gz:Du+l;0a5~jpRrf-(wA7b5W]7s6i)"
X)Uw|wB';break;case'pl':$d='%]^AM6LD),z0
^V!ZY|TRITteQp:t1B1z+R^f)BKa6}C2IV&><n",$n"D:n%*oF$_xt(qR!qB_x.QjH]
q<[>S6[Vq
_PAA_N@&up<Nd#a)soxYGDhE2z_1J=2MjxkHP_X6JIU61<m[K,B1my;o>U$Nn?B>HdMi?=T#<d%ZKSS"7+Zy,+aJ<$smiXmJV[]/AT_35ukn7r=j+o6`<PTbTN$Gc;mxUMB$l*k8-|1n1b`SE8
<=Vg"r9wzXt]w=IbGMmWFV;q:Q^;,E@,.]/Lu1^t:sqoxUI>m]^7xa0kkuoV+tQEWbYgg:[?w3_hO>HJSMr.K@i0Uy)xv#,p|x=wZ5gtM0IE7P"U}#"L3+(!Jt0IK,S:25
RkWAxnv3vw5^)L&O5vKjA2,*8CYY-546
DbZ].(wd#V$nA]xtOM6N)Tb[ZnssD2Sv{L<W8Zqq0c
A3yTK39r1i
_#&K7(P]x)>v`+XNWmv[Z,Nqxhnm%@m!&)~tGO,[+pl0ggMh9C[x|7mv0JoCZ^F@T1nu!Jgr9+=H-l!DkbnBEA,UTKtG;M
n#PzNaiMOaBEHs@clq]jj(?]
m=Z4]gj,C>X5t,IsPUCJ_@Sgp>#jtppGS_Ov3tw@CaZFWFD_7W^U_n]0UX/YF`0BZ>J[SECOkQ+s<qOXb&;puGT2O>.b(ewUyk[?8cAe>ns[{G|H1?}FO60I/19VP^cb7=1A.n3c0GHYDUyH,_"u0MXP<05!G2@aSaU2}j?n>U.qJeZjq3kCNl!.>:%y)5YcuT_E
F,IC+iO0.02j;wQ"6zcWy._JVtdKqSgbSniIu7OP5v:{q7`F^yb{h.onJ"`ZS}0qO4:<Fw:L98<RDY8[uL+oJK4SM`W@plYF<<F"5qb6kAO05WV
as1]@PL?bpc4KO%BG/bq7.SM0!7Pi&3G325Ybj=LmQd[vuvIt#T7[#._$@y"GS5Ro+GpL8UY54<
qi(2]<:!LB)P;1D/KN^.h_`8i]lsw{0z-u
n]P@%dqAIpq`!/d4i13Sy
(xhsQo@lYi%FcCe^}H2u0qFc<<97(Z4mglsCL]zd`mKtq:}KQ^rlte{*[/C:9]AwOQV4mK~s#`GB=%=Fgjvrxf+oOVTr@rVA/tirGEDm8o5rB1_[=c$3KjW=WR=7T?^
YGWZC_@o;TOsJ9G/VOn7,kmFkAorx5_pR[bbeuXey1>%5KagZ]P!X[[JSPR$f?449QsaLnVWW8ZkB8LiEe=NZf*ZVK_7Zlro.jA+j3^i/@o`o@iAJ<SKWQg.Wy~;m0BhpqZSMY2];A%US9_YZa=j&Y[!$4_/(PW4t"H@9[h:}rwc
>s&&HK$UF!mR/&?if5+z2~s#BCKpv-aj;R]v+;IpeIDXK%cka
e=f|G82eO`rNMr!^1|[lY+W1=}Z8v{>cnFbI3qPZ>5_/u[<Sv5?b8p^W:M&mAm$yDm(qEZ^D4Kq*0d?q9m[LG2]63?;yl,C1@_)GU8H|V:`FSU5PEEH
IQTJ[b9#*d#2]^IH#!a6+.vvTzCXPK`|bsV_PnZH;oR6MWoBUb$`ogOh/pP&KY#FB{DnLFF#AA1u*[aDbGwh
Tmz!L5}yTm;/{UexVS:q+*MO]N&xfF0[h$
<LHVcq@d.8[b3Vs"2Ue_p<]$&X/~e$/]G7_uSNb<j^EX9Z(]?O_!3}?L5~D6p?Jw;G)/]&*a=%aKN>?Jlg*b&*Eh_!=<r$_HOQPQQ^o?1?Gy#~H}[wj56olOWE+/h#M52)hpuL&.vxO?>all4Vu$sFDjun:~*?u~fzJ"kX&eK]"?_!wzI.W*pdhy-,qS;ak
ruR>"7YILpRURZTVfjP67Z8M$oDL/gdRRTv`pX<SIVQCGa??d,dce`Z3cq>y6ru=Aa#)@?@!ZP)QHb-)bX3y#G.?kGoyn>e5gFd-gl(3xp2UOQ/i&(Y&yw23a)v8Jz4)s,ACQ#bUCa.cshqWm)Wb(kH{@
"t56FHs*h@5@0+9tC.-XUC47slN!b|SO/1ks)m[F
;c4Ek=]te7TB7vh:kXQ$?yU]d2RqYSOEIB^Ff8Pi71^FW!nIOZ%=yMBG2gSa$h^US9"C
&KIM[/T|qT<^9Y2@6@th
zTP"&M;_ewW+jTU"83"#*ZZ=_:WOjdjVkA)=w;BYy)2iv3{aiLs!ev)"$Qc.lQifBsBVTNL<gh>Ms6fl"TVtSfUXnk#txV;N3=DdCyuo8,/<&nl
FhW$jaM`2Cu3#.2H@@h+8OfH_"`t(Qm$mP<=qxG6>wL5CbgI!&:f9Ai3=64([5EMbDdOLCB,h;X[%o,#ywif~,L(_7MMg5!pZM=H1(9[1A$G4b!JKu3gj.
,raOR}kvj2AL5~${ZR(MGAU4AI2"sWJ2[&>0d%uIv]ryW+KWSl"@TED
!eH-0jtqjA6b+kH-wZRM$8@d<D<{:6V)${1{xNgVp>guP)g/n]N{c+R
sG`H1g1<$Bj_<HFdeQjgkolnA}pm!&kURQsOG~5+0i/jn@1|uG613(:QEMjdnR:b-34s8qlB.!48YuXwYV_j8%QI4D-{>@lj-
NagMros:64YPC&`-uEn
8e8.aPy}!1l4!
aFiE:fa]iqh~uK&z;8hwb)!g`I8:]ClXRwt0=cj@&!t;GHz%Q6l}0NZ|j8OJbqNeY9#_!L/u$s@IM|ShFQ,gy_b0,SqS=yS.H|m)1{18?O9`^oL#78NK>k<gNjDAQ(qLU*k}_9SNJbsYne%(ff6vIm8IRCO|A@"Qj4Qc2GLP+;ITL}0_JoZY?.e6apnefD/!03!3COo/.D.^h[vg=[>PW4C4:r.ngZWBuyJ+83[5OI)`K.*S4e;EU<x-AJN<5q79P(hMHq)p&p/vb]Aebf_Tcke`#}k!w)h:gH"u@2"a)7T}A<pL
81Krd>Vf<mL7YAtoMj}4~9uC,hMFt:,r(R"a2.2#Mj6!Qy@o<U<;1W_H$:b#*)pslg,t1L5o3HX!mdB%FMOx^"#j/C#_F*:B4maZx;CkT&k;f:$WlrV2E+8q^&8N6iw6>:plsNE!i9]>z8eksI1WDV:nKmsXx!`Zu62p74T9Ox,GdY^HS<Dw[98fNk[pbAEh"k4iqPW][l)%MMKo&C(9%_[NR4D<3890yCq$nd6t5?YR(nxH-(0sv3T4"3b$VN]
qk!Ni/DY7mWV"KOUh2bulJjPtKWYrqjU}UC^M@L$CL4$
G|cKW4B$xQH_"XbV0!NK5gbtfM_Ju!A0^55F9<36;TFj*^^g9QEI,@Rod-RZoSrlKM"6d9)MuXnFDaSr8uJ9_0mbvMx%`y;}Vb*1[G7gmB]b"=_T>7%Hh1BsBHcCk
[HBb<R(O2hs8)SD3qGU]ZgaVeU_fkj42hXHW3lo.*x_%gnWV&:$6H:/]$;
vnzq.:cVvw2u<eH%,*PrhiwIyU]gZc;FQ2S]nguuQ*E/;t^CODc-wa]0m6agk]Hd
@0t9IAAX?>=v%c`g,LRMs4q@A5vS/gH|-t=!;s0C6VQ4DLDKI0oyH]$s*i`
"6biC@#O"N!y1SpgL{v9sZ7.0S,IA/^<9yC3a+A`BIVYNRhZ48>Dm5tLghsIslrO7%+&(C%dT2`M?)8Oj@YrFq<@Mp2UA|N-f7,fMx*7B+/d&FEab49baCcDo%%>lS!A6qIIszGCO$-cUM57mOh[^j#RXSa,>C@xe#)hDW[cf=HiE6SaU>(Ho$Q0oY_xf%<83m63(cuWJ$x[s0#7PVp@odo&*6rABuMtH`!XE)TE0lNj:2wQF5hE)*H/WeE?WKVsx,.NggHq,9uAl5A]p;FEm]-&ohH9M?e~q?v5m1@OQt]c0g#o1W#e@)))b
Fi=MI,DK`,]&gt9)!PZdb+8FV
=SWYEE,bV_.x5x6DT|2>`PwGq<?S@r2F!,h>V;;afWVDUoE6ayk=J{PlnS^}co.%R@.5I4d_mkybKYJs4J
i+3F<LG8Dm9-c+KX6O7KW*=Whplg-,&?}(|vsi}w%]DsRjSe,U_nawQti+Ae=ppW|;B]%-wFHE]vRj-95PFC.IfSjG]j417Pj:o":[obZWgY}Cj$FpYKoK#U*=_]4aE`|5.;ldrVQ#!LJ/tQrnVt5-a"R!$a6$g8+N_.LbFIG!O2iNLGJP?XUossyi5SIj63R1nj2=/$Tr""hw%a^XQ#+@#9wZZJZq&D7g"
D.,0m
D6mr=Z#T"10H^yaWT6c=bylc0N~]xSri"?hfmL""E#9&Oc.Jr1T+z:U[1G?gVZ1pKXE/jbaB3YG4#fa_`k)p}=>l%_~fE4|amt:U[TvEr/_.O/x45@5j-neUcoT&;[dxU<>Fu:RS0jHSN)me91~[N^Pc^4KgnZ"&/eT+KHd>;Ra-VJQ4xav2v9nDc"XF%iir:_$^E5<N^6yTQ>~lpC-C2TB%MfWD`]D5Hm!j;rE1_!xrTkIEj6::+66eNx7.N6#b5v=GPdr8LH:3L4"xzk+A)lz,%=UEgW,HKieM;w`B2_!+.LctnDT>hNOkhmik#G.ydWeMvxr!r[TfdXp-akUZL4kc?w+1X7%=Qty[Ds$<_f!`)d$C_v9p-9@!+v)`)Ia^bB?+gCPu/?1X|/lDC@tIr2y94k"y/Qg**y)Vybx^54I.nUmR=F0!<EI-]5C5M2NmTx|B(H&v+gEZ?7|5$#R8>J9Ry.Cp_rS8$l~2IH{TyApaB&dVxEVE#Mz2A8oJK,9X?ID0~9MKx0)iX=^<R?ksONKB91u8(9X6EeL;ZTFJp?QAy_ijB7=/jvq@zmE`fBe!v([UnH$n|!^3Xu$r..;D~F-BWv-vgiB&OJ*>a*j-zJg)s@3PsJPow!9*`xQM>*b<9$t
qP_
H[*ma/zOB=Le)DA9VdAK6[k8C9)$52)&CpiiR#E<ofaacOWJb-r&#OQn3%-3
4csxw5MAoSoTx=a:7hIY4FBkR>KV"/.V2B/@)<AZwD37=|SZW:d,Ro(9/zZ~=)[~:4(#)Ri|fZ97)pJq$GJKz)V6';break;case'pt-BR':$d='"]^@r6LA`*60dN&*Co|uV>Up2CTEhRtv^vwS@+<?`nFS|$[2Gj
k3mNY?&&N-^f<@d)Mej^3Oz$<NDZZG9p;`nB+M)|D)rrTQ?>QhvHvau5WDbRyd_>:7H%R_7-WJ:H]H;Su2xTw.,[:L$,w+mx@.rg<e21y5M_c6<A2wgg*%>ar@IJY}AIc<wp)T`wkF`J?3r]:7X%<~L-_rUis]b,GVRK0FAQ@jw,r:P|:@Xf6x
-E7]/v7L7Ti6$m<#bo$A1F7W

iM76;lq@3tQkWH/I-w@vKrhvyw>v[]"5b0v6
`ix^a13PxZhng<jIangqB4yCb6w>T0_a,@F547ONsceIFl>d*?>DV|?+yVQhc:xAgiqehfD&j5Skw[8zD/DpHR=Eb:Sw`Y>a5HrJfznxdi!w55i_D8RVM8ZfZ@&rKG&V`*@rYqFI*s[2r{P~Ks;x:E+VXL^GFWY0
[*P3[X80w9-gSE_TuZ0ZFZoLr;lM0m@!l3l0
^p@D]a<q]|Sa!&p_.S>`c+mLNydmOiRDFYid1llOryWle|ry.yn?v4OpWK.=V4c,oN#UZ7[Ha6xp^}TgX0
z9wnhsN;DRn+$4uU"l"?mkorJ(L[22tV!n4;s4(ZQkMbIPnm5i<]U2#!Rx{qD?|`K=L?a]}TTHQ$fQY%#X
urP^P8vu`4k22+^5nr`QxKK4sp^Pc27eQ5!T:
>yv`jYP]lr<Hop=b1[3!LJ_M+EC^EgZrAIqK:
Or7OF%`kyqlN+X2:czn:G!CzD8AuW{"z#URgbDV^TOsHB{0pS+[
/"KVjLkyTXxDns-5WsNA4M6=yc.mD!*DGHAZT)oeI%0%(])6cLJ"e!yg&%I&L^TaY_KX^!d4?7a%-~yJbKmx$
p5O!hF1KBAR
>@H%Ous^l.m:APvRKn9p1ng@mhw(l;N/^";ZW_i0OTNk151-h00-r"VT8Va2=%xgKqi3nSXetw,"l~eA:e[B^zg:yU"f6o
[L=Cfo93Z4>s&d?VTI(bVro%cm8k2/"otIJfH>#"rXOVa/3,&i4x}=BI6VFv`afDK]r6:gqKxN<2QiG$^Rj]fEyw>(ACJ#6Y&:M-BUvn3RGR]23w-SX."<PCDG,xH91p^tyJ9SMmfa/@
="TenSPfaxhJ7+M-DQ?I2B<@tHf.JnpRr8fb.^!jUz]EbU^~8DRaWOw"k|B).&^bQ#d#gvpXS[DK";F&[z@mG7wS@#GLSF-TN$e8QVd1V,:c:7^xGWdSI7j&uUG}$KTmvp>Jbh/{Hx5EdSc+c4xS)
VM!?F7fTsyZ?TX=vDbWeBw*E7N;$:A-&sF&E2XkKP5Nk8p$wA{/4-RPFk`WMyd_$fMaNRN#QAMrH%8"e$#A2K39]GH?iaDH1->f0<fw*3sbm_iw]=eXw*i)xgCCsDt!W*hwU
55F5PGEy<QW@=bYjQ+Own?y
tIaiTjc^SMT.X96^WKgGdxa%|Le53h;9;Pt<B;r>rTYt"A~cG;,BsGeewsW/*.gmKR[1O<,A_UcwrVb+T
92TG[<H>_@+;<y|:L33ga/*Db^Y]nJ@l)<@I4f$uxZfRea6FKCN0?GbiS;3o{`wm:h(!iY?8fX^BN3>KB]"luxtRtVZJ<+.QQ0BY!MhP]/33f3(wqBt:=j#JnIW@;/~k(O,gM5!#|."))B<n5`?D!r9/XqFc}/{+KOvM`p/E4b;.+qy<9Ru&D%%%4"n(>?)(fxR(4Y{*y&;XOnO*amT$4ZU"a*8#M"7RDZVcb-5]zMqK!^o;#T#q($xBC#34X..#{<uz!l?u|5qli!2tiGg"2[YU_"vBkRJylN~Wit5A[b(-TT,B,Hz5w>h)%U=Ls7
PO.r^TE<)SAjc"$(1yLu;Zt.%8B>lDK36d3+n=)1d*gY8#iB7ICsWQyYgsasi+hl[W#?&wKKfnK^qEJb
-Ib"58GZAnP8EEpq`-F)ZU/3q+hy$@3^"$4yS&Xz#Aq)z#1q5"BV!HxaJpv:P
[vJ_xZl%_n"-zt9b>yCNIX~dAC|`#%.NAaRyl@s8^
oB,0N>;NG$W]8:L4p%xEM>by`d2-hD/W^:2kSN<D)9a9,iCe^LcvBS%BLN#/.AU:X>[Aw#%77<Tky=?7qP<Gzf%RZIZ;Pdy.w;F1
Z}_?!!mhj%Zg;DT4!U,nl$<]wab|+}8FwAI;?L^2JDcd$(LvgN?U@G!b_=R<5{=9uiMy30/#h
BH[kIX5}q
B2+7?tW&Ng>BI2[>`?]k-68wZ2R2kFt-VP+=0{6Xw23UbPrs;_62HY#z(7[K>W*
[#H38e6o6LKY*KDoKS4bY4&&&xv~QOm855nJCF<DjiNB`aYEIH
_ofdnB?Kku+##A+"o$aAV3^0Zg63&"JTFtFiT9exO`>n3qRJO^VJ4tOf%[Lie&=Dh5)MKXOVjh;!_:<>zKtE
VcymEd<ecrHOZ].RA$9W`HTW1+t+?;gS"*"5-b0#v|?VKDqhZB0Mnpe_w/avB"2@yzjoWJ@A&d8tAcU?"2fa8f0
m@J5kcp"@*RA(?Bq;71%F<IEi<r3HB-^-79xE?"nEHes0%t/b_OX`Cv-iy<F,"%NQYV8b)-k7Ukh3kZ1@x]Vh*I/&N/,c$GsV_bML;]{TVMzrl+W4>)DKUdvTb^%fsncFK>9t6D{YJ=nbr_%J2?EQExalS98<D6"p%veo
q12;]@*#y8fdFfHSHB.BF-b}-i;lI@>T.8>lS6P*tTyn8kcaDVVk6
OQf[T)g99:k=R$N2Z]%q(><r!K*!IpO129UEP&&[ipWgUJHxF;gj
%78*#-3Z=iyP,D)HDLp^eX
3x6ZrvZB_ytu;v./ww"|t7
0*oI~Tzfit?d>!SvJ@4m5O-b"+Ok_1s@=]/Phm)TO9L6md09/7]+gtV?l-+Iz_AqjC+Z7!qG%8F?L[3*M(/O#(,(Yq0>/e^T</Dr4$@]`1H04vuZZTZ!#[yVk4xRD3rD9yCkQ_4=_"JEXfMF`$YYd-}`J8Pw!4KGC6(UaGR>sP7MW8wHvZPL`82"^>ky?#M5rVC1A&IY6O+0tR:h&hsrXpkAz`)I;tY0vA>*=w6yIFU`[1l0;dh<ltB%6$d*?v5-;!g9)64
avg[4_Y=!Hh
`gqFFsh+4bZntH@:U^TlOy@&OZ2e&k"Y_[t*1CFihSeA`.5"JoJ3rRzHf04bCmJAj83Su%[:J1T<X%Xc+TP;93.9oc_+}s}^h%G:k7|0]G52{ZW>(qqbIF~r*FeR}cWfBX;b5.E`Hor;8c>!k>wTA7jktQh5M8/#`ta!iE`#D!ctzO;gM)CI^VogracZSK2Q{tY8nYhwcW1*t]+Qc@(H<+)5M<;y=<0hEk}4-Kxt>.nX]>R72QS^zqI9Lu=k8qJi)mi5U$eI.FK@M95
>]v$1Pt(QKhG(4$3eZs<OX5IqChPDZ4
vAH<{ho
HC}:LWkhfD7[s<!>Ni|;"v15WH;ic>^MX>*XEbw1Uv^)2Yy)|fMwFNJop[jlFKyW#=E8&h_+q0H5Z6ECNv1Js`A5Hie.Y2q]1(M2H<h)p1<Z~)F:~S}=M2n@c%nxmI#Tc?0a?Ex1Z]v
-.xOLI9SV<E&?ls]h.(o^DNcIgB,w6;_<odP/.Fs~gw.++5n)v+%ayS#bva&gq<CC]Wkg)ur{^zt{)nIYKKy-OiE5B7Z?eJS<?$>#.8+ac-JjurJUFL0_%K-Vd6RY;}Jbdd0>C"r_&K?Y?4f6)+J:N1YqpJo@ekABFk(rhp=De<t#"m])<4%<sQdTX7=wZ>&Q,|Zawd?NT8cP*)yAHI1~H<5C-FK0^}<Lt;u
c$+o"1hZj9nZd7SX.5%et8+e;=ZVs6*=
<^=].hTsJ+LEJcX#Pws$Ue]>jT0a(^g#sH]d=?PDGE#P[9xcv#L?LbP!t9piyHg`(Y?[beHdh,xG-Y;
?.F$pMAw6]KS"2YhJ3a,]x:Wr
VNE$knhu"40:eD|yh%
Nw&*NW8`B{KNe{M7+Ig.(<JHhz:hN)vPv#15beo6Zyh92*#B!KoJ"1A-L[!AZ3HtHHTawj"d/kb$?tG(WJ6[9g({^P:|KS;7obXM3qc5M[7T!g])xWt-GqC(Z2o}w/_SQ8LYt0`]lJo?>+@b/Ho%%%5F`iaz3*3xdrS{1"lH3>=t$c$U
_to)n/Ea5MXU|hJ2(cUP;-UT/Q]-_KZ[P`9O=*0=p=_N8&/7HR#F!jDy?2`#B?1-DY1$5r(Hm+=Re[^Iz.XB1*=OLOGrlvUv?-6F<7CU+aYH=+4/.Z(d*c8UpdwPjR$g;#@`
:g*>c-o(0l#DY[UlLNP~=tjx%7bT/8fM8CnjyQ$S+
!v=S87$`.tqkA5XTjD.dKw.?UYSu"tp`!ac}i(Ibw}dbbA/."&5wDqY{0?eSA&6Z;0]YXfg6al(4Mk,:MUVxv*r:io&s4qiv=T:;M"r96|x_[iR,4T@:.|Pp.v-~*:"VTV8F?X*Qs10h<yVD"%j|9#*Ll8iM<gRkVYOp
c")*nvl[lXL:1@IGU4*K@L=KA#})<CE(bDKyWlpP,1$?S_F]91^+Kv6;=yGax>5J},S7BF;,_T%YbL5QM!D=N]k$qHsC1wj&I-`I/<cG=qa.D3q$*L6809Pd-+&6nWr6arUW@1r!cxZ&U@jTHl;?7d!oH';break;case'pt':$d='#`G@r6LD)*60dN.%<-xuVT^5?EKlk775m-06=AD&"aMc1XA3XyXWDT,?k$hcM#B$l:n".q{n%O=njQI=(@%`g/3QA9WB6]HB<bW@&s*?W^IP
J(&qO8EF.@/X3s]"`7^&)g4A^Pog+`xDpLei0XC2=B0SA"sTv}y,Cm:|(et]Z}6d*[?DG;"j`tJuFb4]?[s#)%KX(p^#In/>B,O~8OB/3@k6)AtVn?gb1=Kw.?SxRN)+iMXYKD3L;c,%"k6DWDAT-ZLT6}0H+*]@tSm
^c3_`Zxs12U
mavZ5eV/W5kY62c4nkN{ktkD])(RY#uGJQK,m2nagQy0i"3SpbXkNmjw>xbHo`/Oso=[%-4Qi}]mQC;9Dx.moMcfn0wIkY51xjOS5u`s5EsnFiy
@q=UBoX-2-ECjLjKfb?I^h+C2eQm<X#;CO_m(zmP/@4<f13hmIkgk}<.c2t~;`[W`P`c@x?7?O`HBg&y6(2,q@^Hg[HM%#eNSHD^v/0rs}^``pI{:CYwekkn#e3bIbX1:qQy=z0M4manO`!b<:=^SZGdn}rx9*<
EaMycgV(b#Jp"&1]LU2f!SsoWOD3imd(hjO|`eUbPr.;M#f-&]M?VBmV1p=/
J8lP18HS>vL%Q?@2l,Yk-)4&(GT9rNr!;=p4gTR70rT;;?+
}qvv&=[`,T<GZ;bNeuF_SY]uKjR_#irw8Shcy>efVF|V-vVH3W%B#$!q0qJM=:=PqrMOl*pd2G&=mWsGo0Buc
spS:>?V_|w;L~!^$Qc{hY]Y17%,3;pfI@gB)iS.o<!(vC:#(lGM`kh._a@Xj<X8xiHq$r?SEwxby7SrE:[&niY6^KkXpP%Z^3"UbIk$P=x/w3fcoK*}V=09Wn*SF|<BndL6di0#p~g4W>4gEJk=fzQ(-u32A?a
`EQna.+/=^23cq[ld6g72X?u0S?`vTh<M@-PPJ7^GM-Dt8aSXiGsRXwAwvfJsBTZxp!"Q,DGKxC^sN6*Kp=4)}iyYx*rf3HyCP%KQpO)Vir_Jl)T[{u@;aq6lidVFo@W%m5aFBW9U8VGmRE2]jN=s6RzXN#KkZ%y)qNf[hbD2D<QM.Z4ea^0fIB|.CE%aXVN2(]h:9E8CzM2F^=4r,5v61`eaM0I!lj^:0ofl{!].)(k/dw>LmRH2[LjZM1_4XM&a7P1L7Voaa9BT
lNf}vu9_^8+MYNv#VKlKWdOcA5Ji?bjCFmf08t?/Wkd#-HPKTRp:e-lY!
M~<khF!UBp.0"sC9vW)?qvWLGBa1dNa?6rdoX3DlNKT.rbt6w!y(.c<q]I-vXd[<jfI|^G({PK
cV|GzYZs~)r35]_uH>?5Y*_6ec+H6/zNyH}EjxA]V
EV&2S)=sz&_1g91
{]`;rAEhEG#my^!]o8(nxot7DR+L/S**;e|ThL>mt2U;.oK>*`l1QkW@H.P%zEiA|->[)@}b>,|iUk~DCL[OA)0?Nm#c*ccyvR^2B:1#0*mVB?m;K:h25u(GFaqNTckr|jKjU9y;N_Jm380S}`mT
o[DeHB>VR)sA1t.VK1H/jQ`KY#Z{YN<r.<l^w"xfCE_>q
!Ht=3q=GL{&TIPHPs6V86uI?4jar4v--*^8D,&Ca:Acr.B1Ft&6F:NII[zVJ#A#Wnu/zWlE);&$_p`5~SUEZ8KEA(cY%!|oQseYkT^<)vYMW)Z1HG(rWcN&21sAr/y>JIx5pj)?">R0mGq20i-QtC]N>xH(P<Ex>!pTx,{rk$_ok"H7YHl[G3{?/g<NcZZNi"X^Y-i)?8_Ow!w,0q!
/>2N%eo+JTNAS"p3%(u7D%fh_7bbTMFop)b&~D|*n-fe3KIOSPWms<#0vn93g&zuSkM[bpL@)L`mf^gaSQ.nb=e_ttxEVD5y<YC;]M(Doiaz"@MwH*y
]+8AhDpKfXu=/$v+|tua@%toyID$[CP"5NgpE9-lg.9Q2%#+rV}P<7!GC*Q-]+KB_I"_WECSc#^f6p^?$tJr:cvFUvH^%w}._jTV&V62`2PM;I>E?Na;;H"(|$$3^K}W54~qS[_UC&lLUe.?^Y
H4@kM|2E-hEKB=WbXI#j(V9QNkw-h@c-Rd8d5AWOhu9$V=Sf?O"`/XAS.!dKT3ez_l%:Uj.MT]f6PTKpBBwt79?0R2==,}-S*K=8#&ExI<eM7)JZZKvo
0OyS->(%8nl+zvv05p#sgU{9UNT).J$"B1(u:y}
"Ko_%3vo^$.[bc;@zA=13&T/W`C+P`qbvai6K`1``:wT-A8/q<DV0?OhnTv-drVl.>Y9$Wp?te7x2g@Za>*[``}nNUM:?eU:Xg];CZx-t(GM9d#cX"[`PCh?*[iWQDIZJ($]q)OIIre>Gyynq#NWJ3kbUymd{TCOq0`6Ux7so3U.nHY%8X&A^?y2~!$Xv/?p{o+J*Y<E$)fq+Kkb9o2<oakqlO=`+aW"uW*<@g5G4XN&P6R/r9w,cBlb]
)WhY&V|I}8
&Gnn?=IL).%y0T3lT&`,WRgBbB[84/ExA$!M?HA!I}K<K*Yjt8fKj;A-=y^$_L*!<bp+65ZNn@U74iA7(kVej*Y:++xVXAlKd!h7U=8;1?B<DakVGDw#[&apbb=F],Kv3U0Kct7Za]r>G%g3gVv:TnHZ&3-18UjkEt1EE$inT7%q@f@1!P:Z%p1q]$a:K4Ged*!yRw;1bV@&-py
`>]#Kv(yKG(Qy^aL0|nT+>t1-m:+k~T1CC``HGQ2i3.UT36[]{fUVowIKrjKKf9A$hosy|;0YiQ5&Z]!-@?+cg&5iuKD4WIQlT;Q=ZfIZ]8E=jXh;aJVv^*3N#C.a+Rc/o6nY/4`>AVF`W2P/.Fs=>/:cq*7P]o{O*A-`VdDosP[Q:fp.*W-K]9N.YkFpoRm!KEpm&e<ONvn.O/<Jz;OaT)QC|/A>a6kNA"GV[%=Xi8paNTzY|DuHh&x0FJSf(5]rVkX,SZr`0]xlx[<K5:F0_K0elvm?XieI<v?jQ]r+j
x2beyZ7>x#js"4}.ysJ6(K7]]:%6T7+#l?8Yy8$2U7BgqwD7/EU%u[*xR=4#&_S8)i%xTH4<_8fr(Rh8C#1pMb<2DRnw8x~iekYaCZT$r/
B*9S-+CallCU={XHs?NM_ncRPlw!Oo#KoF<wF&kVngM;_*37O$CcK$5ASKuv>2NH^@b#+}/w/lemVVy>(P${FWmdFPDVf[!rfJiR:[e!]t+LZzd;YcB+Scw:v.!3_Q)I[#8e#0fY^?v=1bUCZ_joAU=Kv1TwIt^JcY;P],^x_EN{B.^on
YlMI[q+OsPPh[RCE%`=vUHhom}1K&$iN8NW2qNa/EL,M%7$1@:N7TH2dj090M@ghXC&P(~"62Ng%Yr2(jD/Ho.RqV8m6P3+L-Q5x7R)l4cPOia;wp5T{h.:b9RRE)3`CazBD?rZO]mK-cudW?->2TKkI0.<pjBj]?p+y5soC<eI;9h[@?@82N`
6D(l}sphpkDDyo&3aM"Oj2o;.9!E:c#2yH$07UMYcNGS8f-ve^x>|RN<>Kl(5>|M_D[IdPf/{sR1#bwc35{ZO<@&NECb[=[6l*usM>fUm#-qU7XVjPgtp#95ys{8QfCH.1cw8,Kt
TE?jo};uooy2,Y4`;blZXOH=3CwVl+oC.mx2/B/Z%?V`9wM0Dy%#t{&C^{0Mh83<dM=
YV!,I}bws0O:"%p^wuqg.T5-Q5]sp?c)=>L}fRAk-$OT59efWbu&KAErGj0J)v]@eSO_ut5Z:8eu+YH_n6+Dv@*K>co#nBEN9is9>mJ5:{R`TCWc9]*QD6UvKBSUv3d&1N58u.Z.0#v|+XW:44iW6%"5bAa>IvCFTrHqayk*sX$HX]-,ZCZB77/YRv^ht8N6>XC,tra_Q8"fio5lT*K7T5p0nR:eM
^F3K;%Tb;~Z.%d:XW*X#T#B,AUA>ogKmhwFUM#o*-*F.t@P1O>fQ=5K=6BK#rIZ;t?8.>=8_%es=-S)I+jNXiR9Xt"W"T>2&
uaciDQOE=R(O-rjTt?@+<VHnQQ;u$C-2n-!KY@3w,+@@%,Y`fpn+tM3PM8R](Wq@+B_J$6SR$sh(`I#60tja"4^LPuvliSp0@sU.zLzL7q&F;k}[GFDtjn|63l&VCt53e*n3_?H@DP(5fNYkz>6+Ef"a4?%+(s!JYn2=1FT#dmhNKL|,xy3<d/:3,dPWxNKd;O#yOAcrW$gvFHDScg`pW=J$lWZsV]v)`u<-)M5x1Xf467pK]n!q9X5g0
S&:SKPpLg3C-nKdtYxm&gXy?&.),A>alb^rccyy:=h?P}xzY1g#-Z7Nbl>upfat4~BfIz&@7tHH/K"D"oY;&<uO`A=[.IruNje%3%p54>`EXHY#D-mft~$Cw4&Q+!3ujj<NC+EKUg6dRoo`4v+@^Rh1H(!?.L8?ZA6+5b2+H@[kGnY$xT3yd[oE;sHk]y,k(j`MS>U^ke"pb!^lf
[NY,!"=r2vR~tTpUN]P+!S*e!1_{RF(lAtAPC@S[lqAMt9Plv`Y`?X7((
y(KV5TJVwEopC]oCcW&ypruT4CNCGl<
6QmG[YDJ_{^0V&(FeX6]a~o}2[mXX@(}$CL3
kZ(KfV0!Ptj';break;case'ro':$d='-]^@b5ID9,{0|N&.*VB?7
KoI_IAcN2SoIol*-AI<:8H8rgIakC?$*R2X!-Lrv$&.&m&9"AiX_c$&$Wtjf]I<Dm[hJb2RR="yEGNp:2Q-v)
wp@J)sxbX
aB-="+Ik(]0)I`ubGGvsj>(;!WwqLEX,[fd-EwZV#Z3_T;%anQmAlK|ulh(/CBh:FW.,$RDg0s.(g/;l-]aGh4D_>JI-)ospu]hseDrT],"3MP>l&`WK">@<;b<T~kQ`?:0Ahx]I.1~GW3U?8k?F$DLeyRkaqU5eGJI
elu^>tUf6UX2/y5mz:_n3sbt/a2tQK"JXxAI2$Fmei50EMb2/]H4Bb!anOpm_H*FM<_nQi.xejhFh4DR8[t[^aZ0fA/axh.D%Y3DdH{v}`zb<K`;<<wy{jQFK4HYsR.nq0Y&+/bhGWM2eD.(lrxn"*A*YVcW.n#<ZtyZ8MoP@G9DA2m2|A)k[])O/j]
DT*YR<D

<^@bw&4>ksn7m}MYjhKH&T<:jN3ksStPCwl]cy
FwcBn6vq(LL:/Lbopbt
[A8-Z@gI*k?*AK_yK.1TY>d`2?0Yn%J]vcUAh+(UiAB[tZgh#)9sg_GGn]|aP.c;qprWwJhWA@)b5a}0KDWO;t2IyDS]Ad:bbZ}lk#vv59
Rigk/%`PwbO?3R26nIHIZV2C[F3vg4mz4;6&_]D5t*i|m7h<80(}XWTfJn82Kh_#S,H#`z+"#|@wS+HaPD.{[6%}@E_$uiK;eI4+RX%,X~rfJGfIm
cBb!@Sxs4t]$BR14nwRt@OaF>FDJXCHvA+*VX])1ZI#UE[a*+gFh1"ung+qr6,`5]vn><}(^kLPqA#
ZpX$2dLV|/{@&l*uir%(*r7,2m--<1[C&H+4u;|?$9IQ`uUamasi&.tkJmM..I~$t4Ao&e%M&0oh)x("pS=OH(9[ipiX8%/>syKNCB&7o*ly[vPR9_",awS^{!>#@<.BDkCJw)~k<Gs)hLSeH:PJs`p$&c%#|:ZZ<dUMkk_@R1ux_#&0JTXs=u4SAa|TdD0DWEy<a^_6O
pZs3}+r(foUSErXr;xZ7G_]s+pS4kQFE9V7f]Rd7/3-=I7p*`=Jb&XbHRc*AtH,=Ixce89y:FIIGoKDkG
F^D0*prBce#-ssSQTypLV51XNRd[koo$?y+p:#Hec2xX*A0Glx=5&2SvDl}Bw7|J2n!T(6)T0eY3OdG6gNCPegmTU#b`821d5K
w!fNJ6tg^6JMKW%weV=vnX9oe/QJmFC1?@ZqMEDvrB0sD)a|Pnw!!]0StF0?XXn.GW/$c
XbXp1]^@KnsCm?tkEL>+BC18!b?bs(t,9b_^!Dh}<}Yd2IGJ!$S>ojYQnZq&XsdQxq[fP%W~KIiu!Vp_:}ioOxQi9F-q<Br@?of98cXyHhk&IuG5uHqHKqW}RnC1GC7$b4K>Kbs,JO#F;*RE
4]]hY/}6hQtX3jWrC!:aqn2N&#R:H^ZhBh9Mi*p]UB/G=A6t.Xf&*konY9If7Bo9GgvekP_GSRq>TZ!<f_99x`w8=GOYr:YF6l3A7Hz7^"3-T2;^-e8p$J8ttp<biPGecfA#.+#.c=w+5_7PXl|U">CZ"?.mKw.`Seqj8t$xV4TvQD?jWb/>cT/H"pOaa`K.l:|X?by8pv7a7YKGSAQ]wR*q2/?X>4C;)gkJ$h8mKlz
O`w1SU7Q8DvgvgVyGP%;Yh7?NX;9E"})FZj]q?=0$gQNU@lSBdEw&XNi|C<bP!{@Ia"5^3)Xm<CNk4pe:idUQDvA<M%Kd(v2LMG7s$X)Pkfcl$brlw>XSP/W>!i"h&RiX84f[*H)B>1/1(IWaq|I/*`lv
fV_$@4uj*@GtU":n8a!8w=2E/cX0R`RQ$qTe`"?6QdZu=>TvAe3=5$8@>q&@?Wj[Av>4oz)WwMy:ZKO9NVOW7jyNL-1"?Z=F}.cR.8=p|l6u@M?/Iem&JfyY8HZ$$^dtss*Y+%qkIC3)+(k1_@%WmD?ep
?64o._a_ZLwAlv1])jGlz8[hGW`c6_jm4g=X@-@Oabd*JL>:S)7BsT&61>991uF]7R00PS2w>f@X&&oER2H4"VJ,@[M!Zu(kG`)h,){pcm`s6C50_7jSFngFS=:i$#di^JQoRUeuks]W:-;m}Y]L([/VNHn(JRJk4B(q.&&]]?}g+YOu_HU+b`c-
H+7.C/g#Tcm5NzoKWRF(hb)C3W.lI*By2fkp8JT>XDHuZ.Vb""2~@SN)TKHh1qh}.$$MN-3;ReMrX}vMibN,4Gu_k@#>Nt04.cn.^Ih.(RmkOvHBGQqCZc$BD{(:YV/3Dw#4(^CfrIpqqE@;8V3#TYcOgSE*k^"McYA@R^C9pNYBQ2=!ZNnf1)<hhM1qb:bX<sId0]jl$eOo#!4R:X?IGIbPrQZ2mc^}s?Pw(TIyK/lgW}H-=^4W^+&-07jtcDc1>On];YA!VB*RBF:?a~6AC1[d8o"dtOkPT7wRXL&s@$CUfN[ABg:!#`&@,1vFGD*
LcWI#j&l1e!&mnCDO^>6c$L5dzo[TG1DjA>{=t0E#n`td;a&aBm][,jn1pas;#=aBrx0?`([*8dDxxU=90K19%-5&`9HZW$P76P<Dh#B:&eW8fYweE;":|2]$3>+r`4z6jEo[as`egLYZoMP+$N$<W[9o(cmi=$ijM<mU57N4Q#+K,)=2;^Z6:7r,iCHBi<Ujg$P:{7Byg#gas/uOR,cpBeJ0p9
@M!Sa3X+C4D,,SrF/ql%H]9l^c67ga#U){P|9H)318UBnb:f8:F"0v?=P59z1D-Jt_%2@c,QKSTm=GWZk"]p@7P{RX<N-!;[7H39WF(bna0FguQN4VqzBF%">=)lwW5hT)Ve^=G
#]hmhMRQ;<QA@W1BJc]oU*012u0|7!fz%E>zL6rnA/<Caal8oiRzd@tIT-rm5,!"Td<;EwMLnyd~d`?{>>]EwSh[RZ!0MYj+AOAZJx4of^e_7lw;/nOk7dHl9a:sB1L(LCF+
qYvCX#h`r3fL]cll@:MG:MlRIA,7WTBcCOoVw6[xyd+>_,~mESurEA60XJbp%)R0NwAQX7YLQuf7ESlX<*1AC;
Pu?`dC"b4$wgrMk)1G^(RaPu,PL]7p6K&=t}f$-FIEkkQS(Kju,4<ng)>q.0g1v_-&"oEOdS8kmUBkS0lXk@098(&w@PR^?aj^bTyeRQ/Pk!=}uv=x/}K.`NU(KF0.:=b[j-v^eY=9&,4VA4N{;ma3Y&QZ!(%FC3g2SBCTi6apq}WU;>l$wB;YY]gvq-Bck7yXZu24P3,!9we9UQHP&I)F=VPdV}@L<6A$u0";Zz)^w1w0UAArSiH^sAln>*s2.AsXBju>JG,
P)O4F~+3;v&0M>-N0B<pf_;K3rODm><mE+&o]$:BrdYed`t+
=;fV-s)02rOnyU<W=NjuOp$_1lsl|iaW?Byd%5~gHg~FaeKhjL1IG%0?R[vPI$6)+8^t9i{/JdrBY)r*YtQ9+f.R$B|EVK~/-9=3D[@TVl}c@+Nrc.rfJV")B:XLj,wZUK~7&[SaHla&,cj"7P!vjqj.bVg*@8ngy5&p#0a<s"UrZXJ`Al|0A1SHy"#St([d#-Rw/MYuZ:?1gOa1Z4QG9/%hp<hU*H*f0QniK#yss$S_0b{+|mGrmE}b2
^Oo]mLpU
[xeKipj[0/,S^$cDDSDUOidT4Ox)vO#R`zk3O-OU*gM=
L#Fe[Nu^2fKo`!PY&6n/Ve9,sLcX6r;s(-;(:_J)]RgV]HX[`O9:G_T+_RgH#
_Izvt_?!_(FVG=D[
XQ/jY&SD[sH4r]=)/V"Us;)}AL9Nczahz(e=tp!XKpYg
1]m[Se.z)PY1&MIdo=;r~X$;3-%ab=}+fKCT?,gbu7yPO.[/tMT>
m:_b/M%Jcvn.5wPzt*Z:KC9dq,IWG9a|<:8_e}YM]`Op^ERD)$T7-;3^0+mG<B!kW/YgEqkclFI5u.s)r)Jm,_N7Et
*">@k
sz#K~9B.Q*%&^Fx&kHy0=[~L;#u/}5E!4d9EOPFXF9+0jp
r5o)Dm>q?(7t0BX%8_<_3T,oH?$V,>U0iY2lYn[GJt754(e*<F">@yLRxvAw`kcdVlx`M+3lp
n.4/)!k2dMn53,?`B-w4raN])64gJ}rW1C#E;3CDHvF7Fg_uJ`w9fCF&sHp31$3_jn@uF/5i+&Sy7;:G=b54MZ57(5:dpM%*7<60ETi"n{-z`|N>nO6,^cV_l_38nsLC?r!H^k1VgDqzhfmD]:kxUNMsJf&G,7aw7E69E~Uz@M>Mvw1m/&1,-]7&6>/.=#
`1A@>k42,_R9^a
K^VQD2DqDi6y>)G7bXn$s5y,T
![=xHCCe@8OzT$L}W,rq:0(:nP;LTJ_to"s,+%8Kx?B(<,Po"]15-+,>Acn`rxcnf<BA86e~$udfs0Wu>l.9oFK-5~vkF<[96%Yn_quz5wn_;]06?r;H@<g|=#4feS&1xMa"rQH?LPZ1S#LW%3]LS5WRK
M,oK4<EpbnhmFhQEw-=qptq[E)YjYTM#vaUAU>D8=H=Yu/s-+|gP$gjSO$#b6WvuO2Q(ioX?t.KF+tD!c3#O,LcX5%eM=/%1wa5cDUP78Zx-,^[oVEIBF4`qxFYQ[Y1>W)>&2
c@WGXQ.u!?ikn,Gq_CvwQTc3U>73=G%[4%p*/c1`(t]ze)Em9GprQ,p-DN2Vd$BOW<t%(oiM7eDog+*L&,1hcT)iQ(c;$L`LGL&Ef&qMqo<]3!!HMgFqBumtaM@[-K;-eHvveiy9*|BIlaV3f1KMqA2wE>vF$UK&=xx=8_Hx_nj=s(>w@(lGyOiU4:Q2CU7|68ho)IFerXpAJ@<%/J.>ZguZThMV1wY
<IG>4DW-Ciz!="jLg6r4?;9IuRE;GP[Q7,["Q.kQX"cg6I(OS
f%
dK<xod(';break;case'ru':$d='$h_LV5I.7@91$TL_9]Mx3K&VqkISf"4Q=!T&8bX1tRb9)2vJV:Afo.CQEJu]_LhU//iOf]FY7NhhMnA`w_`K2;
=aH~t
_pi%
MB-bWGH@
#qQvFbX9:Sl5MJpwxRoWJ
wq[<1OlUMLQgG;xpQ|0N3iD<(W4C9m,Y7@@8m@SF9VrCx7D}g"A&;nHx.V$Juw97SN,IQbhA1_DEi}qb[p+%=ti>OYbVvm5!=+$9#}q77Rck"YK0J"P2L6a[Htq6InK06d5zh_h[=[Zp
#d>!QMs&TN6jv@hVC_7mV$^Fdd>=+@FMfZ3XGs&w4s2K;q*I);9SX&P<em"BFH^%;%xR(q5au_1KmJ67Lsm*QRrUWJXFxHCo$i8]^JyKkXkyzaxA|bQnql+$}^Qw-fhI-FmB)neb,uYB8H/V3r^8suBWA__K+u
D+3tSGh2$:HNMB_;q-L=Ko4v8|)"GH62k9L{7Fj3VE_L_a,
EBW7:l<=W&?(ZcG"D],FR5;U:?Ex)7J"fBvo[JracE7:v?[0O%`4*X;3h{%ZuO+w/9=R4`rP${^=XV+/%Gl}oFg1VJwttHHGbjX&-230o)6y>ar5t%wlW]I-a!ld3U6v_SLFSF&W
}@JL?0@WE%1bLE&@W`?XG/1p>7#d#TW0-;JSi6Fsh&uS@2/.@6SY"(C@-E-^,s
lf^|ZNkNXd-gFB[<G_ID@FeY,Z#{T:1]]I1)>1[Gb?W
v=qt*%@=6ooqv89{iEO:^F9o^>x&%W>:Ps0gL@)=`u=F
qq0Cm+JO5vd$lxYW?SfF+`?%I-]V]K~dz4m(:Cn&]Ji.&nACxK.b8f":WV;BwVSKqNuH
HeGO3(ZGT?l/yt6>5t[N1Q*1S}@PQH:E+:a}
GZ_g{"mm}(!aK5~C3j2`No,-|z%19&cm%fZ%hMGOMVX3Gr)?U4NH9W[stuTkr2EtSl#qdPJ4cWlw<fStMh+Qr
Y,
@iwV`LhqMIX+5+^g+CI(S=n;>/(n
S9UfVWkaW_-OIr_jk?S^%g{>9PS13n)7|(Fv@dDthdhJ>^;iea6Swa]jn<?h,eHTzKO]Gb;r~iL^=(L?]y*JS*+9Der)5dWgkUoayt.@:pnf)1ib;z%u&6#g`2mGFm.3{amBYW$Y7Atq#dQ@ZL-jNfIWlg^=MW$%xoje/&/?$=7G),Ze2yqVu]u^r#o%XRvQYpM%$HIS)Z<.Q<8Mu/ZbD+9M!]0IwD)*=$72gkb-@rO#|UI=+O0oTA)p2_|U_r3*t`(71Sb4O$>b=aDZxj6DZj~yW:RlDTD(=#j7+e.W"]e%Z=D7b&|?h%@r162j;2]oolir2LhH/kA$TpJ^.V7DNc8o=x2J:
4HnvetXV-WlJ`S}-GeCNwS5B2$kCtlbia&v4!)uh<vZQ}5XPngo-A4wO%w{3]#{.b:;=UpH;j?sc(nX<Qr*IQ.`B11F>q-QT?UahZs7GZ$*v3TM@U]Llv!V*88)B63d*w=-va:(f7H+n@58j78TjAoJ7iQ)%@W{
6p9;W)O1(-+ZZnP"`l*F_C*Dv8SST2H=5u^HAr4/m`_wzXb^"q1@4tLww.S2<oG-~]
1&>bS`aS:KCw;<5ZctFK%Zp&);4u(PFqIBl!$oe^+xeT4>3w/(9ow#l+_~?x#22]]tTU:o;TP@weNBn`joER:JiB(l7+yJwn#%o(J1MouSf|&8:&F!o-KlT,"NyKWI?^eNjvQx2]`I<9JFS)JICrqJop[ohl>lI8fsG].M;B8^a?U-YUuS"i,/*hT02dve4K^|o7L0"]2zNV;F_|<S>=At5gC|h/.G+x//!:)sEk`V3i@PvR9r,TO}![V.u%5>%
Ha%:w`chCIoy6,;p%ox}TXh".DR`^bYX9S*$kG:T*%%<C)ux0pMDqrBlQi@zF)(Fi$*~w:,$)
Gt6?rUx8,an/:#F:QF!6?/L)NJ
}HY)&%kL89j0rG$Vfb3w/ngja?e?x&@nLi*ew2TQ!OwL[BxRRBVs=)zDCmqjK+p:<xzI}:75u
NF{;Ia+%Nmo-wSnJ![?&/4SC!`+EjBa(4q.11J8W8;=9Csw>~Q"fkIhcI6fP7ZMf<?s((,)S!v&9jaRBUs!!LNx@@YRn{K`p04]2E/E2n;[0,KF>QB&[Vp9pPi(0#(8sw((oh$7oO*!VX,Br;vz_WhKQ@T|BG[-0qC?J{fjqa)_(?nR=&*-#C57LSPpZ}IrCs7"
5<]IwshH8ZLs"82]R&wQ
us?>.Dawm8a<D*Kh?UozbW^PcC8splKx/=:G2wsLUn4`I`pA/C_lQ<gWgf_;lPxJ=/)CFKDpkM3MR:lFyA[c=)Ee$y3DE$U5Am;fvbXC^vd^5]:oY-6&Zv]gko0ZAwS++YwZlG[&s04"Th?!T7IJHS%zGcFr)_3426qA?K#;*S*`0>eveQ(VyWk^lT7dsZ+=WD`D+=i/`T9|BC`>#gp0E80av9dsD0,2`H2!9SV,V@0^T#psW(?ufQt~(D%gy<:}
"e1;38MI!5g[2suZfC0xl5MhHd7La"bEe8X^jipo[_AC<x_?rcva44P=bS-PH=PYP]$CgQ8q/d_TB-KHvgh2>5OQCD9jsxk/z]shPK7A1NiV0#6hg"n0[<)5TQ#QDreDQS~`ct`ttu|MWkpOYZ<p$
Z7[RWV?]Li*0LkUYuEK!a_h^;1=p["tLbi.?O!A&&K9N7um)R5J-ODoHxPcPBt09d:M[0?
O,Q%#PL31pw!h]&6no&1(<a>9)qo[:6.i/]*4L0*Rl[7$H)95;BlqUDx3
q>az,qyw2zVA-w.JQ<BPZZ?
I8s:/:H[h$oXYXL*"T9Eat1/k])ue_AVeK-X=kdD;t2%2-CKpld+?$f9amARNGnw1plpVAR"ZnE6Mt?oV?&&oT,8e/A"%=t$U[F>q6uTe]S~;<_1I<m?o(+X
5hd(!x|b6#Avt!"0IYTw:luMMGkYX?CixS@XS?W>Yce;K"1Z-=Liz#o"W*SA^#Nkc`NCkJt&[ML21NZ>
(FysgqO*e#K^LcHqP[<%$@-nOf<;cYMEFGvX5{@HY)XHTLpBQEE=a|%L2QX*KlMMA+0sv8ut0$EM>iX
^MY"h_qgYRpd&?yEAy,1<YN-6yPd"ST0h[ig?K)~wN:nDIcP6CH#?)J:xkE$F`"*_F1~H.U"i~pG1F^]1H+E?ZkB;QG}9tb5nP.JAwoHCvI346anXR^Z8
W5:[^i"3VyUht)
TE&Ka?bJOLt]|!z)n9tQ5`S3CrBOBsLEN+k+31zfp
JiaA{YG>9ys#(",(W&nGs
.qKQ3c@CWVP4{waTr7XbpY7wW?s_oXJTA?!>Bhmnm[pX|6>noPB
;I%=11<WVZw/ljb@QdSUt:S@(1pXOEb6rRXtGPFP"D@sB(3mgd>deU>(gb(t(KuCm]q(GsW]j"4?W
<5GOi@mwpji$8F"&XR{>vg57>MC`n^L,/>yXl@AL
BO@z_Uk&FbN/2sLpsIv&T
;^=GAKr2V-HKG%.HkLkuAq@cKg+:3|"{[i(6aLlT-O7yUOFbB0kEpuK/$J8d1wxw9jk(NOJenWOI&,UdhfG
>yL@3lh$A`wSXe=m$[p1f;sd$kRzNc(oE/$V9t.TE?n,b/6nE^s?@c*aVAHE#]qT2QZ9]I.OV*xLGK=Z0zFyW4"VEK@NNx?btyBj>v3W:-n+e{-`TLdcBSLpaW3M`U]-^Dl`;`R#DT`4A4L4jJ(#fsR>_EZZh2
V)+-Ra5)~w6%A7ZCE#Y!>W=avi#R`XZ<V>[][)W?j?GmBIP<760BJ(DK#`}t_La>}?G$=["i/R"+z)lW[%a<7,-V<UVK?GQ5>aQL$$K)Y2;?=pG#$Ius+qh7{oyIUGsT@D&SFK/m%N(jRjW8!KFmsh}?v>yXnP/#,ZQq0BX;hgUS,uSO_vYx4vpiisKK[$*&ST8w@"I+ejN`rwRr48ZM6?h7/,:4>Avf""!r1*40&lt]00]vMm~FucB$kuBO8GlAG"qCrd/CUOO;arR`bDc[%l/:*TpA)]JIWseev:dt$C5XqG_b,.Xp^;"=5nTKu.kppg9$-fln6$^9e(KZ#O?6b)%Hqi#$(XewCDr^:k~]Z)}7&*2i-PoI9aHbH[wARfT<bB9UkhG4#g7?JcQi@hh4Ts;RO*wH4Z%Q8c#v|VFm-/Ld{Ov-y2nQ>@]vM8IK7bR4DMC426/pX]6)#N3j$_&Z[
DLci/>0Z~splPFH=76=_;<ep"5ZY[M"OJ&w!#%F]]c^A{cE?R*L
m*KqyB],Ewyp}o<yU,tdN6O5g;
[Z(t><L85j7Ra6fZsUD677+Llra2;:6KHm*Qe7.}T:%`qH2Wn0
HAq"hL|KNWRF,V.4uYK5H]Gp.aj33a8W`aW=,w:5Nq@VEtG/AhBz%?,V2(;^=1|Ey`Lv8f]j?de5]!6IS`_#Yry0t,`t(t?M-"WxIA%G/BR#2J0T$j+2rd;be)HR"(."dVb83Y/%#:4E]`*)A8~e#wfsgebD=66]GUKm8aScoe0K|=pEn2CTOt;I^m`>!/aw}q%Gqyah^77T(DJ2QXEV<GP@jL4<oQmdZstBS4~v{W/*7*]dNQKfKHw$Pl^`qR`.@J`&y*dgloLDZJG[t2XsG<t7`0]9]FiY^2nTs,zfR<p/VLBG;9^Vw3Y5w0t1:6|*0AivejeqadW>s)qk54<`7p"%COyBkybb|3-=[?@bB/aTELSdm%I#9dzKWj;S7&<+#=,0;1w9RLN7*G
AE)U!<6OsO3Yhi#e2}x"uvb4p"%hB7L%..%LN%j-dt$W.k#w9"yey&K}Z+6$qz==pFF>`UPEq}3`eBej-VjR28$kE6wX5T,o#-(Jnf2k^TJNOp4S4C4@TFVo6S;F;z
iZdo;FlG^yfv`7m(Ac3EE`}f!HGdS(;#x%^W7F8#LG~<-K]SW
/1Mb5PvXBfc`)Vfk@MngKYP8p+Vp34vWgr9`9vgvc0!s4;v?fM3Fax&>Z!Nw1oGM_!/Tl@jx>,$/m[EI}Ol1`6|&trC@gUx"3T:@gJz@uKJW]4%bi3w;>iN.WWhpWRD0w+UXD.@F)^LJ##67gAeES4<!lE>^$h2`N:J4+(p<cjn1i5h%3CT5F#apyPT%L$!ITL{HCUZoEWWDO@UcWV/1stx6Pj$^I+y7,"SVKy(Wz<^?
USf{US
!X$k"
4QcKAvYJfg`Pq6]I564g%o$f3j5?8/^K%
XOm&"PtlvrYVXJ8fa>~32iha&U[?cbcrF.c?S=MwH4l"
orsH>KoRMGqlQ:!C8ya#=PQ>6Q[*2+Ttya24<E;v)~k8w=Mh_H:OxW.8&uo~S0-,lV-)"H;"210&olp)-s"^sIq_1v,<v4@(Jk(1?8:e,@t&X]8BwONAY&a,>nkh;esm`M-:b7^3;>fq
Y+|lWNunJ"bJu9VX>wK@$r)2zy:m~D2U_32XZF1hE+-s)6|X~UM/f$,(c1A:KYk(yn6q6P59=-yXz@Cn"^4D@R9
@T.
i-
-r[X:gvnsTZx]^v5F0E{_$&(5LHBn8Rwa^VXW$
e:
>x%@f`[&mpt9iv0M,c"Yf@u>[NKS"!veMF.4&sRZVkG"FUv[R^t*[q65Vpl.d[v26$eJx`SOifwK<WFn@3n__ucg@YPaTFO6d&P]aX!/c3UYVDA%WS>wt=(?sF-/30S7M_t^qT5:cNZB[#s7+tXq*d=DS&
s2^C<MbpH';break;case'sk':$d='"]^@j5HpeB}0
"*(`R:kFTq)6N(TSD6jg75g?
2]Ho23]@N^|3f!B/[2R$g"CMO23Ygv@)BJo=U
RsTdKu_^%cag#wfb:f4u,nLspIZWtMu7JrK6-<shE
8_FbA?,7*E?bEx7;pId+pP0pF4g9.
]PeF(F%m<O|JYuC(0+<F)Vzmpm@E}_oVE;`C~?;qSu!`CwGWwkIp/ZiG^gbWJF8?.RKm9
C&jk`77ZfI*F}"qY<SRA-=&C!.`cKvth(Vkl=X@@Q;-*10V6*hWJ9YjvCJQW5y&S+,Ix}p+E4qguzKLRY=&pEZ9U^,o-v;~u7B_D{sgFL!Vb`<g2P`Ky^N~ywk1Cm%QHu1uSp?U`Tc>b_m@<#Ifr/tivUYNWYbK5FJarU,L@2bwmDev6UJl)66b69y,[%V2l^G0B_rp:%R5)v/=k>.nZiFCK305Moi#sx1Rn",{6S`e?d55u)%hsQ4f;_L/EY3:,>A<%TGGvN
K42!eWF_`#|%oUywJ5dnWEMLEc>Co@6<9$Os0_8r12{L[QjF"TLZu<EgRQj1h]*+HLF$7MY=^silx@q/<>6fD"Jwke&k89Lsh/Blchnn*Yi2AvE;kwS
ml>D6?ocxl"(;,35pA3dVuFFw]x8iN6XYOs2S9K]hI.l.=6=^.pu[u}.*8{EZk-HY,-W
p_(5NxMzE=C/7i^c<*W~OOFC*m49Wq@Pt4w+xKED:s"F`I2^so%mumahBgYHX8/ISb7s>QRDV%4qGX]&$dxt*vtUgtMu;tE]5bCF]P>U^mt!&bB3:W)G^l:8?ih%BbAjB/q,)^v!:5<,K~,uD[pQX7"x-5dgHK@3;?Lk0!x^Tv)Zp#hLS^nVSoE>r5k|r]6Bf{SsM:m}nx+<gHo!,tiA>OUF7lW~IIjE"qi77Ly._hyw!?<*wJJXk6H3ctF7t@R@_z0*YpxCs&pd3uW^9tBHy2:>y=c$-r3vdf:YHFhO+BtX%a</emM(js7jDIFrQMo%EX0wpgC[sniU@:`U)JsPh>@H!?,>X5heS>Pw4Z!--D%F3U$SmVS``~1~Ny2BTgf^daD3HcxsQo^X#"L"(]!neIi$eN[,b&S}P8-B8>lo>$5c&3(v>m7(e[9KjYCS6N7&R*>BDeyD0mimGbPfw|;u"q44P1m~C]XHiJ7IIVePI9?aYiV<D09tIUI^kDBTi,[?0,eY9RE:;V<YWQo}?O)!Zome#~&Z`uqc
FSsX:dZ]8I]fP:[!vmY9)@8e++PQ5o>]2#h:9T<n81x_]C}Wr,>h*oXl[a.)ayDt#?"cXZmJsZCm&]iK[I7gy0%uRt*lroAb9mV.I,L)Xgs5Z-t?W]VR]L?O
2L-6]KtN$W&a0]8LIil/dz>IlUqI9AmP&n3e?08O.~B|]=l-]4EJ*C&W2/pOQUpO]K
Irfa:yh"9WDxH)znS)v+[d$DI!C>C._vL0;m,J5XN6EN%&m*8.3Wy)vJh:[K59U;~-WW=>z8^ifoUw$CrBN_P>Ve6g?cS1de:@6BJR^;@B%wx756q$Vkgt#76uw3g378v6?m@u?fnEwn^2@Qy<OAgwlrX[a?KiWh1*3!<IqV%?-Nu>tQMFIY4-1!5RPH+Er:XhOkDd![)LT*}blITvw>#UXY]4}3{y6kyy_*Y/b>mm?hAl|0Q/ug;uGMoi,g|6;V$jQ=(1C;<34"nn(Q~Gv5SgE@
=!2LSU5cJsFwpDp[rbE8?abImVJ?4,@
RiZd:*V/jHEH>"D%a1
WFm@.4m9W`&GIm]%1Om>gm3M5D^ReKq<aXlH|lA5Nh[];-73WB%cov=X}NBSw]{Bww8i^G]YlSqpw.fAcJ5AKk|M-n:@jy:w6N-+u$6P~7>&75;CCN&?3CaY+QmWAa-)p6<Q_8w@eEperlhpZq.8%;D]naZa&8u]FZvWS=S?.VbC>Kc:E<f7f:xt%DF`yY:Z?cV=*"*ERv}8#ofqokjP9gh*?;d$`eK6~Q{_W-r)!Z=<9B`T<.tq(,BO+ghE/@"ag:~jw&SdRxd[MpQDW>3)Aga2Tu(AS*zL`x2rI#ifuA!^qGqIWxrYe2:DG69jyFVb.E@J@A^MiK4Mw0<FoUk,oM+>14X[jl=Ys,M&kt9ZQHn%F81Z!lHeIe#=VH#lO;We_!
:hEPa!z#ekF>s:fAd0C+`%7.&u3GJI;,;aQKWe9>CQvvN
u<:_kod6U`KnA]%BpODFy*2l."f1v~NF]Rim`_%Sx%W?kp<VC`iPeC;/T3Yx/eq}1m75Ca&n-utX,9Kg`M!#Sm_yQobCG!WKqG,Zc?gwc*<!].L`r|AZ#Wj&pggW$BBH#PLN*+CsdV(uq9[&l^8Y1t"IeaSa9:bD>d#e&"=v0G=qv$Qk5D5^1a!oaHsjiy!Z/K;rHCI?B`tKve:_=NB$:K]#vNry-W!n!cjZrs:Z^|@OIHC[;R^jSz/(x.Vi%@BHBPV&vJ^v`>ZR/uvn_-YE[9rO#"_G:OH19_S1<bikms%0m2J=*o+:$qTAW>EL%+_nZ,n2X,M;:;wHECEFPQt
h0lw-BOI+nF|jsvGi{c>A6(tfpU/d.D_Eas:^p%D*VgsO^v,g%lMm{80=d;417<`3o7n]KAC!sAa"A(4r>?KTY!oJ*2w;Ut^p.Spw2-BJ/_jA0))K~>|*=#r*bISfBgV7(nuyiSy>xxQok)j(Ve+0UXzjJi_9(wLPx9pDOaP&cYo_X[ATr/,SgQ:A4)&;-)yQG9r-}`xmEgFSHseD^Il#4nyV2<M.U#EeW;w7H7dS~#xqV+^A7Z[72n11laT&3)&L%i~kQ%[5oU<q]UTkgSoH}TKf;/O$E[
:Re}0j
E.]*&-1q-5/qs=<GD:8<TNg_j+fu/99g619a"*BRYIIJS&uu?p?74_cnCM"4sh&rjy7l[Z@hc.%?)G3UdX*U)[-&0vSfq+L@YI1hG7/%j5lsoxWV:aEiFEo@=xu5
SQBwh!`;4-U[b_o2JZ`Gdn;^DL&.Rm282879w!FI@:GW774B0l"z*.kzM!n4rXx?mCA:pf$dUgl,
XADjK>w%TswQtySuE&W[|Ly9|$.vI<7KVNMs~0/cX+_$eeHTLe&@*NQ$<]D&B.*4jvG/%bhUB3kbYVx*R8J=)ZKFBlBiGDgNj"uCM*VhU1
(E^mG>mUwadp*V>,-J9_5?Sa=WAFA^Kv(8-Y]4iJ/:t#h;D_,LY3M/WwJ^Q8P<9mFfq6eLK?1~*]syUaum4M"h?#l:t["DIsPvZ&Zzpg)"Him<cYnMOb/r!]VxA{9M%t(=q~ldC|gL6n>PtE2;?%y_[Ve}AMxYFiy*_&`ZSY?xh^;z0s"zx:exgk+^I;@hdWQV?4ts_^b>c~"a*2dnIOduB$q~]nTY`g9X8Tr^c)-FCe;?s8gyPpV)@CHiL&L}
B8}9iHU>5-$f
#3>4P0S?N-<-?Q0SCUF+D-FU
Ye"i|Zt_]>!;5;N#H1i5-r"x0&"o;E}Llxe^n+Kr<2B
T%~
4JiTW(RLOwUG<XS(<NlMc&<S;`)=}^n^Y$n)QFU:fivhO!>+R/@s8%-^
&=mhY"]>2!jf.Z_2
u=XA01qF{/Kq0NeVZ@s7H:Jxll7bE4N%,)N!>N(]lceNy1"JDtf_9XWy9nzLJ?9.l)c(7-K3u*WJ)F}=f>T!.^"H#AA!R%]0-BZ:8_i/<dBBt=BTzF{#0@ij.XsQ99OZ~`7R!Q0Yl%|qaK~eidN!+W%Y-eE3g)QvBcB^.1apEpTn{e/L+O$;cEDN"PjyWX<9Hik9/8QbBKb8;=D8zo9f#rQoO6}A]^%:buxHNw]6B$~7^p]ACG61ZA6,+pHC{a,3B&wy/-;(TVdg4*<@v!AYX*:6`nPHLdn(vYc89i#e`-
a*NWr6"4gb1{8OO>6]9[Hb&p3U7qtz%)lW+!;r(Hjotbl}%M#o#mgN)vPQ"1,Q$"iRckH9=1*Nx]5v7yl}o+F=7~%_+6c.6jXcGq#K6HOs-Oc<:+p<&dkPv`a8DigZ?Mk}EW;e-C6mK~5`5v[iC<NEt7(nGq`);PL|41Qa$$.:PdV"YQr-ZYi]##qz9TAc!@v;f!O:Z2yX"~R-0f8@k
S
@oVz0PgDGiJsU2#ILFI0=l5{sN`Js
qdHvi!:9b+$,NiL2Mmk@kf=}p;`>8bo?t=]{9$[^SsNe"c320UVI,}(^p,KdspsFg->P3^-F4KaZ4zaI4vK$KhWw$!@v_1/Qo?S0BlZ9hajaqcEp,G4CvaP0]ze;<1J):Ji%oDS<Li+!F`ISm[v&+gI/WposY-75DVxTL.j]xVs/myk;+?x0$GnEo>ja!CWzw&nBC4yacE=,:2y$k/Rsw|E6gIZnq^?w:b9}yOb?isaO>?<8at8E)QY)`afPK*xmB.B
N^6T??9ERln5$]_d&1l3?/<<?M[m:{sB5*CxL6dy!ux6umNety0ja42dJ>S#G0SG"-`xP9L*V._}3=;;K/4WjRZQ=u?"C6=IydMT4H[o^7077]:x-{`AThi{o"=)U`ABteYb)V^Tn7HPK.SHRJKN)B^/pn!:"Ok|!X*:CR
G5;eduC&NK@*)<*>[rvJv7P5ys1"d`j-Z83RofPUjgHe@6W-syqR[H%:xY-W^@tCuE%e
;k)<h0-p9)#e
k=~tH%4^"7Ea@vsR<A<M27J%IE/>I4&u3//1US^vHx`/P5a/A
[p>N88|7
0I@6$yAl%a7KqS>-7>pO;6HD4.KcZWn^"YPpjFu*b_SY7;TvoRS4ruwzE^Hn/5VY`q"hc*Jl+sO+mn;r>]Eg]h,W5)#BL2XxOx.VsYn=:u^6!Or`LK[DWyNE[]Pfga8Eo
"<b-.uM.gaYe
9yP+ae9fE<i=Q$aukj~mcx@$Yc27(&h8Th(T/c4WwXHfSL;3MyOW#vCp.xwbnb]FHy)Mzd&L(Jvl-^JJzx_n#;J,(PfmqK)x`a-X8Li?:0VjG*@lF.^&NP#qi8_`,[?!w6RlXDcx_$~Vhqh7@5zwB';break;case'sl':$d='.Zu<&5IAP+Y)CHT!Z-xv8tK]*$@SjV_B4_|Kz/pg@h5_bX5fKjgS(n`8$o.fn71sg292lM]&*U-[*9vmRJ
5ge{DL_&5&%[`!npumyV[1of6E.y@+%`c-QhgfAQUxwxEOdQ/kg,uh6}U4Pfp}l*t3,nSl>uZ-?MhU_!1j[V`Q6+499O;lVy?)schhg;
hARyPGE=$0%R-
|^<,Z?|,XtFH2Vdp<ZE3K?~xsvu:0
)bfEwc],uZtmR%ILo7x/D?H27ZHohM"yVS8fTKpgCpEh%flym+:V:
)Bdw-W>B|]BjYcTv@yxuAFwxRvBH.Id@;7eOR]Lc<Q9M
bv7=)QiCE6)l;:`3Gd`G.NQMv8Q^%j?Rk@>?
KRIcjBk^b[nb?k+f(3!bnm`v{2*D,5&[N?&5j1%$eh:+sJW4*Q;f6la_)q}2/F@]lv@DyTq-pz&eyIckJTpB<p,Zyjz?=I"1qow!
kRFK%g=y
K),hTIMdXkAeov{J^hQO>P/E;pmh>PBJcAf7-sDLUGzE3LZk%
JpKhuNxB-m5VY?65U*y2RHvU>D=EAkKB$+hvdaH1%4UQ1jdo34LtzoE3,wLE8ceTO
JIZ>&OcWiP)_Cy9e.fyGg)c>S3iVaklCsl}2+rL:}^F9@Urm$td#%[Y
3Qmx$*yCow$)^G~00FE25m!u%7w-C7f<%?Z7Yy8Ebv5o[;g6^7DMym^[.uu-YnXi?B2v//)5QnkqHVRBX3X/T&q^m`Fpc6)h6#`75k?HAwbIs%HlVe(xYIJ(bEGHMh|0Ky`U5v1$c2?<[M&f]+!gx(agb@9VUk|ysLMirEbUqg_^MU}uZHwvAx[9&J]9dmPiV<.iRHS[bFKx6/Z&W0^m~XI3Y)@a&7|-en`=n[#D7J7_YO}Mw^Da@a58as*_4%SWTfNj`@4j/Ef
J%|K6@EIgB_eMRW@"d^Lc>)vDq6vuZ1(]UaZ5L0Zb]BB+AZ+ei#V*aMiIvQKssDI(EDu6Di#5myDAba+?dCtC*%DJ0|Dg!}pay&iDhhuv9xL95s>u$4e~F[={l9P6uihgAwtCX(8aeNJYL[aU]vvmWHXVFcDz;Wr>pFK%"S*EW***d5&

-#n0(Q771U%[!n
c/e!gikbbz&2I#g*DE-g2oWs>824X#u=E7juSqZaX=gB@:amd`UioA`v*81dFvC#UC_{cru>Uklxd%QQU]-]wd/&Ea:+mkh(5^_XG,4?-i#^HIRw<t-F8~le"GLkK+JtI
k4I:@4%f)zrdSPAb**%6Z$FO7Sq,dV`)^$VGB"$}k4wN00EvYj<*!SU/
-IJAQG~;+[6Hhu-`wde3"fC.9JHE!FJ28F,Fc]BFHqFl,jp)]IWR<7uVBRumyWBEU_DaF%4TOsFv:yX)NExgD.^Up/eQbpC]oh.d<`iXqCpqfMlH,3zQd]!cD1)o@S~+}y@HZtyJH>,yEUlf+T5VV#X$pWa5$oN9m)<hJ_+An[*Q7ZRl5foW{Jd9N*mQ
_[f[2bud^+;p?_LOPi#`)&YFsbVc""LO&Gb!%|[hw)+{NK4dC_1O3-WW:W6EiwggRN#D/+;&76..ry"$8%B)/S@=>xF%"h]TmwSW:4hAoua~1BlaE&s*-E"|s}CiqXj(95>^#,_PZ[#R[cBd&/evaCD.[|<`5fNF3N@2(8m)$4^/]w$nA/RzWa*Z(+99asrH#D"&Rqo[DKe,]nR_4z#6j!lH%|:DmDY5!Q1LEF+_8IM#;+(+J$4PbX5bFH*[j1@Ze54-Sm%TIO%[<[BC6Ue:z%q&x$Kb8fsEvi"VmUo!t}s%L/U7Uo;K/^5&#])Pw<FFe/1n+/;sXeN/S2$BlfPSV,t7lkk,l75/eIynsa`~*`3%`$`9g_.Jd*C
MsB(o8"&S}Ng#[:c_}Y/?E3~B]PxJZ7a-`/6&}qyrAD[qau3cJf$C+8+4T)5(%lT(<-Po~0#?aOdM42{,U"1g,!$<L2S.r;lL^C%1X9uIe46cI/UHFD$3BZe+:/7NcFq=_QB+qw!3?l@9A4{K=ibTpU
!q^_Iw$HF:a,Hm09s`"[Zf%J5M2_N8]2q5".-5"GpXSqe5"I!"7T&[6-v^nr(pZS-?YD:jd2v.@y.l=ohOmFGAR2-opB/{g?5E,)Y0Rt"3Y4P],@Hi/H59ybC#`sq]tIu^K@""Mb0#=:0U
-E46{x&Vn.u8p9([}Bp;K_RCQt)9#.9uejd[?]0GX";&y4@=QMF[^=vZb:f_4Tur&#8^zN&%AhF`MY+0Oy]&7E|$N9`K[kwB43}v>ehOpxm*sYa^%=dW<8gP"4;-S_&hb9}&((XOn-L"@S5R5mI^S*;P{-0pzmsgOBGYA-3JuQUI|D^.RBq2,OF6CX{2Jot)!"lwA..ywj7CAS0h{5+UDM&M:ruus:5s{l[-lUmZp?)uMm&axLs.hWag$SSJ8MKM|bB!_O;CM5O%T5rmH*vu_rMM{@Z0cOKFJY`[;;uUZbJ8GGX1(MRk(F4I[;"j_ngXnHfX%5*(r,o_m<?iMPFxGI}Cy3nI%cY!sR4.7=DO1.o0qB]_=`jqKCfPl)?/to9cY4QI"[/rZ=
Xn(Z.yTO[KTfe@*MD;K>QgCe,:jN,2C:d|E|=mQQP3bmj_y*]27oa.reDgi<7%Wq>Xrq`@=uoAxOob"FR>gceO(mt=a$qboQ!e#roaCa.f*YNK]!_]&XnHBHgZY4F&a?plH+"&K.TL$yO}ul
!a.Q,Rs9&%zAe,QAvYKSg>bC~l2FU")
_PcN4VGOWhF>"ZpQy2VA/A`&9S6x%71WJf4qM)gew&
Ts*[Vb.VR>)8-DAH-:Lj<v_zNA<7qbYZ1p:w&f5Z,&?~cE[4[9/kwIlMQP>nb2!ImY&GL[C<qp2i
%ciq/V*VYD#s3.+(z]U&a,FwReU3ELg`6Aa>veWC
p@A2:2ddKr(7Dcoj>Q:1Z:&|$`vBuEta.RnhQD;9UK^]3vm:sxd3RKBv,HNdCzI0;%C]FAGM!jOVKgps%!hFwL7XyqT]jV?>GUjn0"2m)Q)Dw$K5Wgiwb_^Qhsv-)1.5idh1w^
5x@_=&++WP<_YjC=WjK1o&KO@`-SR<
T/(&gDOmc:A?</jk)UXH!GaI"
,]->Qu5Tj-+cR)G7p8$|oq9ag
$mC5]Rt1Vv6y,id;Y8byK/5{N59pc]iks5I?r3!@q<2&%@mC3>,3[;:7T-a&
E.(j4I!r6Ukh:&eW/Mu9NBl11HB`t9;*%IQ&c2LXrWz*JQ.hJt)5iUke=*c"4"Zkwqy*1aQVANr,QoZq3:AGe&h,LZ|Z837i]k|O^YE<VbuNbb(N4y^g=]24E=__<s[9n#A.Iuo]<){Gt*?B];e.#8elD#>SKs<-2T4[Z4$!zYKOzbnqxwobz^[=+:^u{hjOyszU3"hclGqKRYf:d"?3jr@HPek7?&`,TxB5kP$]<8:G,Z-G!WB%896g_aZNf_23?${2MIU;*`},;g"F:005)3z-l7D0.5ys"@wnbXB[Cpip|/Xr^V
h[9C.=[kb5Z4Zt8u+AEmvBUA$3y7^{2n@*WAhwy`mztMSRg?[Lyt>q(q&J`V/X^qNm<-mMM@0/e.Kex_6r()EYRO7w3urFQ#GZJ%Nk=SH.QI)y:nOiUc1Mwbn!W5Jsm5pv9+Nj:.FzppD[kQcfE|udGwe431AR"`VV;TFtsNYvw*
)P[%%SfO!$Pc6EtE[M*_]rDMsq=NX=4J4ghfSJ~>6$~efO@SPv81wb0:UXqUqhE7kq}.0_%HtaAEm60yt*CD`%lt*#ZRROX*-Q$&]o|-<#MB2n|iE%28z
OVZINSVV/%n$uK:,6"oGU^KSXJ4`~m-%wpx6K2,cvw}Q/x=7"oaTin<;x9TL<,<u_#j6/wZafV^3?PPj.vk:A:5oZf
I*W.^L-XL*$4j~5X+1w=`Wt1R+[]Y-dn3EM6l_qI$;oad079/Irp,Q8<#0vVSaOftyS<V(G+dc);OYyS,eqb(.E,&jdqIP1Lj5Y]wg5gtIn*B}-XL!+#xhLQ5^7I5;T?R(l-*26TPOShHmnI[6mCa`9AfAOe*)M"8KV#/jq2v>sMogkXvMMt6j%msgwY%Rb"1v[OD%ER_U&Wolr$&?:gbX_Z9CsxJfB]?<^4N/GLwf;$CCw3,S^Q235M7#0qyC9S=q<&_|[iR+i/>ZrZc-h3`=PpOtDb++x"d{.}uL2j^!Iw3*A-w<[`#$&t<%oDnA>X
Xv)L^ZNk@r8nwk0awO3
i,}(T!u)aU*u@U`F9o/d:we][:%tM4n
wr@s6y6l2lbrAtZ8}WeJyW+.xa@EQum,ehvR/o-7wZ9Cf$j*!1*x04_5&`<J^9s#^,LsDck?[s?X`Sc/C24rXC1/a@%KGqEW`]AKHR9TpWOa{a-P^<8*&d0
RUAnD1m:}ezFm(:uH-e(Fl?`0I`-cTiD-Qp${Sbchb.^pA7*2j2J,]=N:8r0,]lb<%VNm-GV:nPe&Vxjk5=9e5zO&<E9kAAY_R"B!.:5er0GQpG5YdT8Cu<QiZf9^O*<GMn_2"BMM8-0M&y"ad0LH0xDQ7A;4F15ET4pVwU.dKhZko;#CM3#M)2j9HB"Kcm/q**-8@rVT.:hS#O:2KiyO5yyW>:fd2h6w2G5_9!Q&QqPJg>w[dwh8(@.5K-`Fsve!b`g%x)8L!y6&Aq?w1q*^i(Dgmi`e!7(Se(""*N(A4&u!/s
D(a2LO:>l5!,*I8>!ZIl]xdN&';break;case'sr':$d='-c0F|6kYxHP@!DQ(u4Zyf)N,6.IC&6j"BbROg*}_z5h
`NY1agiG[UA#./[*B9[VIK#mL,G_rgsKZc^M7z"B|;cwxm2?Ct.lftj"A%EFhWwITG#xyy}b)DdBy)kJdVy1`blYaSN.Uo
TouYnk.KA~Um,Cs+p*Q(`0q.]}h")SK_ja&+yGVQ3{u,hcLw9+%5*0?@.x73i{?LSx*o>W5m^ppR.0_,vQ1SJ>vVP<I
i%@%IAsR-Uh*x{D.B73Qf{iif<.DEOm2T7UIsQ:}n:].T<7X4NQo;}dy4dhDUXN"vly6c4H)
z)zUmi5B_e%2&BJ"H/.
@IC:;ifJe_67$?~g/M1C$^87NhmvWI3MRa8hrIdX{H7iJsA
wmPFhmIa4z!_rt6KlHAW
L<c{<S(skRV+6-@2gNK<0cE)1+Bdf_Gt=>;G9Q3}RKP0a6h[j"D}FnoDQZOkwZUir8O=vC66&,#sIH3m]0#B7l-=O`Cqj
ixnQ6z+845;NJmNcgjg4,DDrhAPVC~%SaHrtC-a8f8t9[BG#"$)^uR6N>7:Fiif[bP+:bU3WDS)u,mMQowgs-P
1,7udth:#hU4km?k0WAjzRVH(-);
DKJ(^d`G:JjMS_Hk91#*fF)(I+RZf,B_pxMNL_adFv(i@NS0pxJ"YWK]Ow"pBe(Z@X!2Y,PoR|Gjx:;GF1KmsMEAEE>c<lTZvq2i1FE8z%+*Zr`7>KAnC6e]qrr1f-:MN8<iO<3]t+[r!-1_U.PP+mgc0~pGEgu;H0*ycM
KVb3ZYduXItCuo_E"NW5E[YSJ,9=o>nClir9qlqHEYWn1i{G_?{M94uxTssGN@[XctK^S:Bx<yU2OtMl/s!v.`|AnxZ1Imt8G?Xh`YP#56Zp-<55Jtb^D9:2Bn!g5PZ7Zn*#8&ghL0)[@^q+2S~Qy2d]oT7O[)odAt@=
w=&a6?w+fVi`*;t}6Ww7jxP7D*C3pt00L<e@
38S-HD@lm,p)H5NL_w|bB+C>BDhcaZR
+l=jn($w$ta#8;1+erzKt9Yob(#E(b/W_tk$g+Dh_RCVq#kXEo#ANSj2Nde!fwbU~p,T^PND-]2*1f9I>94u_-Z*v3^AeJbl%S**A/Mw%+fKNM5dOB*Ho:.2Ls&e7">gRw*e7Tw^4OqLUaX??o_p.$1+`-9Msf#)]H%q[,:!w#"n^FjTByNyLRDbCF
b_K$-[`Lu5Q=YCuuk;pLhnB.o<?yt2-Qx0&d#@3W.d930RFd^R5<vh_quS1!TJPkyF%@8%oCx?iD)q/MoG/p)2L;D??$^AOE=A2hm1MOj<`jEb=vb0rV$jh|_,NZN(LColDP<h^J_phh9UskpV3VEsj,lI]f_cZX[py<5Lfdc1Jstnw_?)AI97k#@`SpY3D9#Q[o(/qTbKV;vW>vs%-FxLNXLAsJxabQ+I1nEtOS+tV$[^9f=W5e@n9D7cc$D>
v.lM:,`u]5(Z!u(0k-u&f0{hPn0?7k-q@x)06oI_nWSnQhb7^5joGw&hu/{WG/9R&L#Aj.R!=5fdZQh"5!d50)f!^-G4tFsN5YVY)E6m$=ylX<41K
=;:-gA{"3>8ecYaP|wF;>b^
^Q-q]T>#"D+E.;emEt^bVSAo3P6.Q.QdO<I^q&Q2F[
lLPW+Wh$r*u6tkeUQ/mH]z,Y%qw<a.<3Q
l=xX-R;2hw%?9Uf^9ST/ls#`aH;Br?"q9:qbdoD!EnS;QsaBIDF<;tf0QVj{i68;x>uCv.+0>}Ov*{h]ZKeB[p-RceI+qH+[-e4MeG&^V{q9g|f2^ji$)e$b,j$a%Tm,(2I"e^Z{."iqr^pPai&ZXMBL21[S[@hXB+NL,QPBi16{Re=r-4^"iX,y:EEA#@+pWN+#sN+cJ7<goam[phQ(v^DE2{V<RC>nFd]n2RFTdi*wW!D?H&Z=Mp8DWu7cDt-[9[flk;e9IQqa7aTg0
Z899Bv/=uQOh:e.UXpeT3^kZm~)Oj
`uB2Q8
p1YxMNqYgf#kisb*Y+5(OVe92o1O0<&tA_nU8DV_*K}&F18x!#O80
a"pVrtL]fnwa78~(;3%U`48/,W0dw^fJW2]*Ah2E{OzPrkc)x*QEu
E:_>E5>[_0d9-HVeX6jf5@HD9;$la75%
QHF.U4JZYnh/,}c#Ylvy$/_dp[#13,V3/sXe8-j|ez*eNb:~#l1^Bs2j#L%$U)KNr>.V)r_ixrX!g)W08+tl0RUFbG;sm[GY&wX?ln6D#xa>TeDUBbR`EU]@CWqHND5$8Py`_sR]3^xFT46dXJGL9Tv+k.L!`E1f@TQ_=M<;BHfDi4(=_[a_5&#P`cA]1Wgm5Jssi=<2BH/473Dc(Db*W)OCNH>8!R^.l@2#mx$A(A?]u1A!1c!SnS]F*}`[7!3jxw[4"k]VIY]din>dUQ,<#%Am^`/OlN$,P.d:RZ1E;z,k(J*rh^uFpd-&n}!gCNw2/?;cA1#8$aYSM:o/"wMNa[Ku_L:0h$%K[^IoRtkaDuAIx:p?OY_Rh)M9vq]->#0Rd41`Q%kN,,O!*k!letSa4g,b/J45Q2P=BN
,1{9Qf!Y1jRgM#)<Q;21N&9@M&ImTF}Z
;?"s5}Lg<OeEd-8kJ**>I+6BI;pZ=hp);u[3$O.Pb;cuFtMIf*:11D/ElmXiEL(72K-M0I4=xf,DKeuHRg8t@y:{"Yn$5?fHolVl&6uArxG{J^_,j!u`MI?8(l+~!e#WQfmnK-]o>d5
SSOGD^[Mh~:2BCD[@p--^c^f]NP%QUwK;3HIU!eBbQQqq"^8_@c&L!%S^|R0D~mtBP1}%-#.#?]/KSlWFB[f#Dr<X_2"g.`DcVWmFF
l&WS=iTQV489Z,bHhyfz#;{4Q.(F,y*U&.YUD0[aUFJCz!w4fI3ZH^$
2kJqPU]G]0XP3Y!4):hd3R8A-1_t;c:Yn"bSAZCwiq*aIdyY~8:=>fLxVy2U7*qk2&q
U&z(+PEIw<)"mq}hi4]f?_U"qd_:th2C/WkOnCO7h_4-*j%ISb1861cObYeJ
kWz"kfrn]o?Ql,;sH_X?99uU7MguadR<M^M,=uC?*naTwI[X)_h=Qf0Y:lL5e&QMZfRs[VF#0DIkWq6k^zevTo<>E]6tIS!_9DfQ
cIU@]2{C1;
k"Ldh$BD?/AV2V=p>)S8M*NENStW#b_b169yd,_TVTYOI??j7Z]P@.g,iBvIH|#v6^v~pS
#`>!xf4wT+-1>h6.;?A^#$v8BqTfueXQCl51Fmsel+KV%!`7e9[:oprOh`Z[KO.s9/gqW3WXAnWBQFXN@:>>D&5ao9/+ieSnpKJfOyGo-Is!+a(dH,]Es!B#S0m`30<tosZ5[x6F8BCn`Ziu%
Q!.n.+61$gv>0=aZ6m}#fb%HD((5gSF&Sv~@@v`8258cERb,E";x=`F0l;pC3WB5<a,orOcCswo/PWOF
M(V>*Ki]R@FR235)=&soR=Pf+K#FrM@-P#EoJ@=S4^E)4g4Q%>qavkti!5:;p?ipM:06CQg,2plJBsia2UShx5gs;F,7&Zm!ie8N
*
LyEFty$$(Ls@oyfRA:iLN>T9zdWb]/xyI_Zv][qkw?^,SY(%p7,K0tEUMK}
h$2tS:/TWWOZx^*c5U=V=JT1JuK;)Z/OA^CuO(E"Cdke}*9DR4%LAit_b
Vh=R*UJ+N(/W!mdx<%&L{PJYNUwN)j;mI(nl4bR?BT$iWb=O!
l!.&WS<%cZ+R^,{i/L%OmADwBQlYAwi*..Q1)$pXIAfRxK;8M?#yPeyB>Dr;Tnr8n$G7$&YViB;$Pp:Upk#AlpV4mL&0V21m<_<Q
n;?}n5nn1cnA^fw3HoC*IQm&JWKAjJWdUq(IuIS^TYD?4//_jx&i`s1^(TS%TF+__^iDfdQLYF!>p|YyVvwEVeIibSRQ96i}=AVs-`JL^eM?qEtY5_4rak`n&nc:"lPK7l^Eb$l7LbgrGNQPM[/>?Um;rAIL6Wq4$UN@w@YS>*?ko1t4I0cddHZwm
0wXz)
g|4TxGJ((:V
NJr$OEK%*wp1jLR9hvF|K|f)=6q|jiWK8JOK(`bFseA}D%:i+m>k^r3T(HPLlV)(]5D[)m[EefU4K/+
KcthkuAS.WjguU*1Of2&j
qxq6if2YYh*3eHCs9hiR:0e(L_
_"/Tsb`7;-c-O=t_dSO
hT29d!~=FZvk!+V!QrJjgcuEeiJC/3Bk~<]#Y/lG+ez@$UKu)-Y^10iJ{"^l4]gqnY<[E/.B*8t
d#jC~bEJXR96|Yt?s/Ble[IN{oLyj#efyH5Zsb&-Uc,xdP`KeWv*[a-ap8S[o9O>eWp><>45qQb;w/OKSHd
eM9awU$BXQ]m1Vcu@FX?up~dIKz9fwJxtoriDl$X2OT6vW,sOv)8S:UL{
j;gydCsz!`3:XH&b(Mph-hCl=%}c4j1.%yf#4=`m?dPkvKZo;I;BY!gsj=<?bpH!Hh5*KogP{f0?Eq?IW-&Un:qKsyKmL@)+4P/xC
8iQx+B<W/xC9b`9lZU5@Ls~7kKItC.P@{BKZ8KLlYCqgepCsokmu<0-HbA[tJqF[(&
toWT#|AQ,W3_GoX[lY:9pb_/rTkYPgMm%p46<s"U
!9!l#x0EZp(d8[O.?V~,w^paLVtlXknM>kLQ0N3Dwq4E,B^s%cXo_QsARh46=r$r
vba[R0r,N>ApX8(6Ap3)pNjICY
W_SIO(5e9<Rl]4yDcZr-A3IxRA~yW7U.D8ta&val(%a1jI-TGY0@;Qd+n>I3*wyJ4j`xSHiRaB`OeXE7(O*2Vi#%v!B"I;?=M"FY/dPJbf|bK:YWyp%vF<(02nFC[qF>yypGg3K:gq?,(CQv%OQS^]s6``pjpjFfX@w@xK_U,>EErYlsxoKk
QS#!?"tIB![iS}h<YJ)Pl??{xM`MWaS$A,64.4"#_8
-m^j:o-Z2g;H:g1n"Q6m!w4v~X3v;mZ^``t!PiQXk&n
J/<++T!]r;|aS^@BlTBS=wvhc/.>^j
^C[JO]]z@cJn[Ybk-YaQb&h#?(6l.eHe:eFx>q6!VTnz%[cD%A/4xnc4pdF`QkMd6Ewneoqv>yVS-lGI5H%J%O-]l!h#%P`MqeVb]ZxAy2)_#Jcx#,
-u$=$(url1;Nyjj)p"XX2M?WD_.T9g3-kD{m`e;pV[{6G]$q`0.T`,w*f(o*K(.jkY&5#t>5M/YRQ38*.DqM?j@]mDHS-V6_KFy1QL3PL+/n`L{3Gf`ov<1&I>eW[/nGdaY%[k[*~@nV
%gC^9fS4yGd(';break;case'sv':$d='"ZuALbPDI,z0
Y+$l_6OPX;4_ChS:WPeq@G@:_wqr/_=yPOT8voQk-$I=,f*885n%Y*u[ykh.<M>oT`i~"`<1t5M/1#s*FY@;py!$jnI|od@yw<>]OB@%`FsAY0G3(V]Lrrr1nMYLXk`1S_kIg^<eI$%m#SOhVRq(4.`NbPyiHQVSAqP*cg18EM]6Q<%fpeZAF0A1mPDZ0Ci./=b+>JQ$;DWZ_Ab0?t&q9Mjm(im*P3V.07hrz&jd<vaw(Gk<66Dod54IHV-Ea47DXuP-?E
mz(5B<{ahX{[NoYtuMb7HU[oFVL@CDz^Gp(*QB|R!X|Nkxs%:5JXge3Mc
=<`J#m
U3"YxnnEsa8q?L!%PFA|$%(A()e[oQ=lc
8a[5D^3L$*ApA"870Ss2>sa8N;8{6}%fhu:0*e@2f#dUpoasg+KO*4W>S$m?B"9<6}@.`xd.3StEvH2caFG&y]Q=Hs"Xm]W{c}6,=QqNo
$D,&K#U-@G)G4QAp02IL
{;#?;SlG!0A?ods7y5n(,;)kwgqO"@^8;"8W~T~`RjZD+An1WYo>i/*fJsyQ6G7]:01]QyqaA/VkU-qP-gDgnZK%&j@2%d9J<f}j"*<`Z&k5=!~>W#_E7
GB7Fm3D3p0`h<#J6sGF$v?m:EqxWi(2G+n,4bvzL7wvw-N7F+jkA?#c]).
2yn}V&jdhmSPkAMmuUKV&4
j4U6N8elkO56/rx9iDW-`I[@!UQhvvKdCWqx{[0i#y!/4k0;lkG`h8LC]P7XsMHG;p-l/+Np;%"M184.vcgq.)M5C>nT(2/eEE**0uVbo&qW$!PcS]$?nimh@<bGrx"I%2+2xUo;<[Mdq2t"Gw]Q3[!cSgl,~
WnrY`;+)b3PnHoOoHmpsviJ0DjY>v.eh]1%c!%gY43D`alS4n@x%O&41>W[VsXqn*MS%y1_PPh~["_h7bXlS]T%A8t?6+gv>ptc_!yY@>%4
g
Lh};H6k??"%,Ssg2)LqD|X(_J1Ux7@N%{0/i`.!2m+YN0E7&axuSiE#-GwHEGU`<GVn4e<|QA"caBELsl%>bH
@Z5B8c
INOV,~2,@
-[liUXndbz0?@ql8fK+<#9MdwwWAJ%qCai+Ms-,NiIWu,}koY-M@U^:NS$B6V*kx.Z^AVFH`@1[?Rr7x=By;:JMJUgGQs3IRCsyqQam9pJcy_QXW$.ARR4TFSq`}*#E2SZ<m5id6Mo4zds+!E83e)S7
1*Kz2lgHU*"j,+uWqfpLcjuXp.G$/S2YDh4$[gdRNM.#OD_qM+$FdK2iAl^5H03~J/$~`zdsZ:..;#Z@l4YoW&%uLE
h6p7>2FM;$s)v5_GZj<]=orJ@htbQc&_$]Z]Mq}P0JiEvb^W6pDTGSHl{3C<ujhq.K["YXQT[w>vYaF!8mF;KM]G-dsZsL+.%#xMul@2$^++|1Aqgi?is7$(#"y$`W=skC*.@+9T|_cP1y
Hou3%N)5aMoxC74A#.muB
cG!eSCyf.O""<-G,E@C6o5CEKa(tlxYeb:U3x<t
IV3;O+W&Mz5.tesON3-4K+74$$%XC`Y5s%<Cy]O?4|/Zwb8|gEZkjmnRJ%tVJn
eGwE4e"coeb93)6D
y7brhB?
49^uE{BjFPQ*S9b%CS,@g`Dk*(w6:bO
iDMX=`o6F_4Fm`9]8tU3R%x!9;]FQi*>9+$ou<*CvAX,I{?yfv0#vq[Dc.JrQf]vta*`)gWjW>QNVB]LYw0-WKC/gZrp$V$JM;"buOy1;xDL#kmV"l^%;I8jhcXZcdy`5co--u,"U#n
*n"yF`
J<;F;r<qaPA?by,0Gjew][__PZ)lt8<93>QWAG_(1gZeWUfnz/oH-"kLoz"`iRvmD[-UGGFf@D9P?loPRCX$i8^!rQb:j!"aGA.eV93XP[oZ;eP.)k;RN
`ZmW-Uy>b]=8rPG+C2]HUYLRv2
;$O^O?^hCzBQe$lAh^)K94TGRW+e&,D9!Fg4$`PWQ2qsmU`UA,%y<hXRui4[i;i5"FP+m2r_YMxq4N9?5Gl4ws)O6QSk)!I42$iANVNO6x6,rR@?^r+~E_Bb@^O]3dMwAzta
03?F6^L-#j,P=]Z"vraH
[~ydWo]"A;8in_ldEp=Au_m7$t"ZI2"OY<XHHjqfZI,P5~xlyKfgQC_9!
]s7V):;=P8nhgxBp<!@h*2#%-k$x?VdcapYSVT%asj_ev`>s#OgC_2q>%xAhDaO9g*.2.O+hC@AMI59+@2wvsOuYC%gPR0lY?=Np.nW$1ZU;
xl7ZnTB/#M^-2:#!s#&s-I^&m)-8}?.k=R
u>f-3bBQyFA=2q<X;E&vnh28M"Xgb+))iR?b-8J]^GbQMZb6.zduo|.LXl-@Iq.Rj7O]qd)5`;iRjnL=Vo.`&!U
?E-x4Rm<1Uvw<8a^&dG%N5sjq:Ac3ijZ7{#DQ{)MA4pangQ(#OtsBU%)kQ$dIn6=HAwyP.9sY.&V*>YGwF^0:v%M4oB~HV>sruoY<$hf*d0d#z%sW*erQ@f+5UJm8t/:roKWjX(!4uJA0]YplqtFPVV1jdup.o1"5>j"NPQfwj3g[3V"=KZ^0;3!l8)INUexp!YgAlyK<h"KfNZBQQeLG<fM%;Vi(yF]81Nq:[
#^ppx
0lsAv$*pGorC&q~h;D;7-,K0"8M;}M_Vqi=#(YYpAC,bnjj,o%T
[O#lg[AQ)!rlm3MC},wb<M3y>BR[0A7$)+4Fo2rGk@|rp/T[2*p7w>G^MgFGB@N9LDuS5f<.j]ud^+X",Dbb>/>!l;uarX#%o@S%:O?l:i=PW#"g?6D*fWCqTARu`;0:<H:5I.:=oNt&wq
C@vqPN)=K(ITrLDr]vjmOzkXv9la6E9O@Bl},*oqv-xo?qvcO7+WQ(5?wO?@F{]xDc*TDQ.gy
5oV0$Zd#-?*8UaP
o/+v^UVUuhLc5
dTN0_Ppfii4!Qm^M#0^w"!V~)L/ZHIaU]gc~
O2o(}XmTBQ?5GoL4-DU29H"WhY*q&)U-ZJ<4Lf6n
eXvw5DLNvn_FZry73o<9y0CMn/"C9T";XdVq
+-o&rFfMVX
R;e`nD-KD2_6D/"#cr57l;ptVD=U-G^98^9Yr3VDQjt?5W&)0SR8aGsbrUY

M((r:CscGiw-"49g3nkB4X4uh?|Fa]FJr&B;xVHCXYzj`l`J96QC]7hS1x2&oh,j3nir+^~;psA%OA%B1Yquz-hZ<m(cGQ)^
M&m)((/_S>2-C6W/K{fMQ`*g7:5Uc.jX<JyqI7#j0lVBD9/+^34%%D@+b_!nk&UijT_{/m[OX}&(:Gu/T/@$i5@7&+BVTvLD+3pA:+h}&.HM_85J:8Ha!Ai0KX:ME75SKD,R&0M=suu&^l,Q-o8ydE77dg]<]=t=e?gpMQs*L`gF*a9*Np*>DDl]a&sg6z?pg4>9xs<~M[<eV[Gw[FUzVCUn0Bhc.)t%"Z&a+
xje0h,Hxc64(1x_F_f)k-{rNKv_s;LLHd;goQsc1r.g/*2w;t4?;=aowmmq8:Fi8`nkS*kEmP}+cEOK;#8%z5"^6x<a:JHyz7!p5fM@nFtX7hoZ3^BUKYoP]6yn3#U
O<`py@C<-
0ZmFNk-QiAv<hfk2$g!(bt.iX(8H+:)!gJ;X4t?_HKF`|3}4Tno#_JX8Z@c]r/*Fif`@>"yvOGo/k4HKjq89p7ZXWQz8D,7Uqpd+Rv5Ksk4!tX
4GiB>}H;R`(?R/I-5Z!P"ob;Z=:=2E/**V1JgQu3wxap-Jc5/.CmuW5}Ruf
b24dJV,P#;:*UgK3,LQ2sbod@B]5+DKjEyJ]Z&r2&@RS&rd"(LrcGQFLQqt`)@X3IeTTrk(WfbiG
"q2R$
7u|T?ODK@0skrOOecx58Dj!mJKQ:7cb[Tdn*Oy8*vLJgMmP[>mTOA8`e/Jg^>=lv$F
[O0v-LiQE6e$p:h(vBgmY7[6&tu|r/iVZ"FnuW8&&)(AH_y+nBb:yu4;X+l,^U-
;t9X1a$Wiekcb]k*ZouO*YM-Xz+<FD6xLgXIrP6gC4oHV>-"4u#@GnqHkW?JsPITwru]5,JC`|WB<-#
Vxnku7_HDDjAU1hoT.wAX1tM$w:fHN[z%=l62,7S$f`P
3u@T"xJ?I_VWN"I@$p2c?[/F`=I,<?dR&dh)lJmA*t3["Q~hhv(uEWdxk#8kbK(s`VISm*jE6/=6?sq`Hwcj}G?vEo0jMlw03GgP
WN!MhqK:aRR%LHa$e?n-G<F/*[[mNLewZ`vn`VFia;hN@21s7-py(v95W9aipB"(V4u).84%)dgXp48U3`?Bt[w(&#/0#_i;=}
Y7pv^*W(^JFW1
rs+s-lIG^Jc+j2Vfjb/m{BX5.FZj|(NuHW1u:dyw!W2u>gF9sQjdwTD3zT&oo6k,4(,uKN{r;jv26<g;_XXt(lp9{LN.X8oU+iD@kL+d~-W>uy~ji';break;case'ta':$d='(n1Q|bpAPB~?Yd@3K[rB?I23rDX/W2CY(jRUq>w-|:y+*Dz[UPFai5dQX#~^zbK>##"#Jrf!38M.MX6`.*7_Hh_dwg5,3fwh;+k:$s<1^9R(MuE,rtkVKiSpOvXkt^1iDx4i#l<MiZ+KO]iY&LN/=p)&]f^F`1#TU[g78kzEeGVnwxhQDb%>gJ,cCi6d9xGN/d%aNC#x17Q?O@G/Acfv%+mMbwsl4v
p@8C%js2e*fy.Em2B1F:ygsvBsLY)Xl.E*R/uZdsgM.S!G#k3-N:n-Mnj!^`a64G#j-)!a@46:("aZvRjGe{vEOA59,^,n*p:snYg]"e=?"Kc.Q5.:S&IQIv5?%X0##3uX&Wj>NRWRaCW+C&<E29.
s~UsM%5o$uB2w$*6O<Y+hdNdxgacA<PVny1(DtMev]?JK8BhO>UjGvSDqDn<l]v[cs<{_[tPx2X+3`
)b|^?`OkhI]rjtEhdu)vZn%;ic`$u;L<3abLT6kC:`$u1,Xl4X<a1[H^7X5fG#djr%xV-VCZv:<xPC@CfZy(FwKAh&3MvJ%n-6fg?/_,OrL/;
Gm+=X6Q)r.#b4#l*.$isn59)vUY*!3@<d0>cQcI(R1tAA7Mwq<

vCcA3r7A@&elFuNZKhAG0TL/MnPW|OKnn!cM).~wU)Ot.G0FPHsSG!fz"!Wlyao.m%4WS,s!ET~PK^_tzArnTKM&c1;6bwK7NcuV5qw*1K3V8ginr.-U5e^3k1_H>&yUv:2RJ79#>:^0)L}>BHstw>u)dbnhAF{F3k:rc85f@,hA7#(R13cCZt9eeXXU$c5twON>u64mG"DIbK/r4?D;4cTn2JDbdL{D{pGrJU+rDAVxB[YZy5~PIIFEjU(sD:Ei^+1L8)Yc`bF6PP&^ZgP^-qIKzyJ
0vfNhRgLC-"fbONVtuZ9xn-/WrnoSVz$-E5(g(*TI!faO&IX&I!F_o`8tR@QL#qPdQ8=3WET@oUvTO%5yO>Bx,U(fQVcQpph
5V"q-wA,>rLfgW7ld.a6%TxziG-5-=szss0zRrYUh/goB{M%Dcdo5;LSTe_TL66mQtH/grbhvF%>5)#DL{hk^]
SrTL)M;/qPshS%..PCw*>y]]{-SI#Frc5rL58!}=qtxFgH)0387
js1x-3|!+i|S8[bOG05w)o#<+^Jdc:JQz)VJzWhBV(`iQj|Q-!HWn%lV~W$=r2]!8qL[^i|f9mp2Ua>DC6EtTJahG/b91ULKG@p80q/2][;lVYFhH<_jSi]8;kTk]P@,z:vm_g-jEw2mFmwtIulOerO1a&Q2Fnx,MQ&[WXf&uECxG`0auluastnL$uSPCIhURC<SD#%UvB0@kD[x]%$cY`ArgZ!=G@evS7Ivhh#-Rik
|32x_G_7A?W,>t_.a*kVl`WI$G7OTV6%*Q2+*A"E>.:/O(*r7olyPb9g0oh(N(r0uWn2$HQQc6gPQ7W&HwmL_6h,<uNSh3715Ix=u50ThX{KF/W-D1&s52?4_sggbdRa9J_+N^pjWZRcI6fx=uc=e9cE
SQ!0H4sh4tl"Jr]BQa>Bm0"LsR.IVj$SPb2(`1O4wNS4ZX6/k%6nP_i`IrH9R&q<fQ$iWkKy:W(&F)*?"@[wJo3H.xbKYe@g1.^PSm/?.*sV5HTeVlFhDE_%3n.0Yf"y#4AL=G.jZ;;*Wq:s60[Jphef)}X-sPfpcp.!`ORqsYIcQfOm`NOxm=*#fyow"Fk-:{Eg?DQ~^mqC3brq$PTulHM`y[)hoC1v%f$xtud3)#?^4OWjopkRp!,dZT3NS62Etfs,.o`N-?>F+S0X@!TTb1[_^vhPR_SlFI?=gK&KCHj_j4hU.%#QGJ)do.$:@91ELvV</{d>+7rvE<=6%mSxP_!#Mi1TH7QqVn68
c2JFL$TsUlKc1e;z)e2i9ybbPhohHMpLn+zb5xc({^f?W=;H19MApSukE;y5anBR
4{MyxAJ^y>$EV2[&leUf,N
|(Ae>:6;[g
gE]#+ZAc;SF0!=_t.QZ{
VI*IqFtH:*v%wC7*[DsW;NmV=C*
W7QZ@$y+~b7nlTzRKIspT=^D;dn$i&+8cULw(C~`VN|51XRq:
s!<Vp6ED^%0AW^AZdOZ:vTrDo7vNFH$Xq$q0?.-y_lp-"04IGlm+6`u4^Q2+PCSHIqEX7x-W,pOf(9M&_U/%FRKY30g@&;kg&
tOJXhl):>gh_F`d<gg;RJF7CIe^4Caw7.Jm#=1%;Ba|bKSaxJ=~W=R2P$l8/88CP}46r9KiClgjI|1,EbXm[q<DukM+i4XO*3RLO2=UtwXN6$RCgfxh(:7,O~ZsI+)aTM-upmsWy
&tX@.+`~w8JRKC/m3>:2b/w3U6Ra#Fm<8hZWG/0~PLi&)@uYCHEh/Z4??
GXLhi=;[$-Kxw8g<9-:9mRo;r))p/Su@UqaC.2`GAz,6mYM2iqTvlX.T+{MbfSgGHDry;5!Ykk.Y0$u:w%!~u8,5kUr>1|,PXO6
0.w>
!nVTGR^/}w@cd-Q?n31jt0jhhVCw{PcuzuZ"bpV<*
Z^:B}cGOh)L9H&|T%(1$A,IY[IpF"@$wMO=r2i$$[XAS]Wj&{33yv1DP*"buhV.Pjl=PC7#u:/L
<N?=`<l<G;t$[<$WyhsQirSn_e?KBn0w23dvk,&RSVBl@4cm8sr3f^@aaG)i!M"1C1tl^KD>M"D<IRIGVjX4rl?wqULX
-p8?=Qq^dgx2_J6IEEZ.0:;bO.oY).>;x)>AV[FnKa
X)blNac>_S**byN#VYHqMVT5epts2jWj~A]YDGz8j9d+v!kW).|U&uEto[{DXZ&UKkb8t[5>US#DU665@g7rh#F:O<%)SU}JYI)Q>bW$P_6O]WK?U^nB=e|!U0IsfuE*CSFjMooM]*~g:2#6#[noJCyf1`=ow5>r$Ds)&j+@V64_-&8_$y~kxK{LoL^^U9ul,8`XBT(xy$~K!VCP5#k=A24dmimD
0pRGns5<gkGB)-fm8(M3Z9P=@-0:S9H[(Lgl=q-n#*=R#Xt4RX_=DgnU"qJn9f!r=B6715u~U^d"OYG@.Ti6!k%4Ms.,r*Fqm"RgJN>"0lWk;nF;WM`rh"4k/<Q|O:t,gz?%Zm.Zq,kv
wfFRnN/kII=[ANdB)[VU</4>,[iZNOxAq]FR8?Fk1/I((%#uP%{x3sDtCJzo>U%To+Wf@W!cu;jG+qUX?yZ!KvzNKx`QN5[>8]tdVi2&qxUgAy-y0b1JFLUY-qwD/5er^^"1G9i7#k?u/KjXk+;8_UGBfe;`SR^#^nH2f9Mrz
=>Cue8aS[3ywL2nL[`v-(2-_E2^w(
qVz@KaVra[iBAyzPB=1/@0<(s9+GhdmxG&:rd&T]|$[>a-d,+E%*uv1Wmu{5x&2SeX2v.S^O63~NVaa,LGc_201HFFo3Cj"lmL>)E&fo)3Hu$B<]I#&OmTybh4a$C/45OLSP$PvNFb,0ND"^0(}HH)%-$0,p3$=uZa>F;JQs/5;^4[R%<"m=I)d(l-1t.>Xs"tV=.bF<@v7YKqKDVLQMykce!@J_bsWmoTEq}r?_3
(I>)9b}Yz`Z"j69%sp47qotw3f25[by"SPVOj.T6cr_ic%)I>vy"[G9QBM.,QVQ"jW3B3mx%S7XH^un*MkQxYUmy%J]:[MH0GOw8w@aK#CAopO941UfybsmkA.
V5j1XRTlcg[c"FJu=Z<G9ZD>;U]rPj.)$R1jI%^+O~+ACgMS<8Fsh]`e!JS7EB%P.TgS.f%g#,WwoyH)LFG+0DcxtTh6a##uB<dlcZwCpX8FfJ2v:ArU%FfWn6.=kK:t?AElZmE=]^^X^V@sYiZ0qH+N8)<
hST"J//;lWjHmVj}U!^&f/j+J}1BR=LcGM+I2XW@MSPh]9cu,ur%?eZLl!45f%l+B"!*p:>I(|Dz4]?/l#&N9sP3G4p:N]J7-xRTH(s[qxli?d!L/--o1vG*E7l)<+"!iZKE][X<YRfSM^RUa,Zcuc(j">MQZj;BUWV_6kvH(o-+aFl9mY"H2|"-,]&T%`Rmi.)lDo!ic
sJP/!xJ;k2BGkl?ujknLr5%~0mc(R{3ti3Uopt:;wl8RV,#/g")6jp%?;^Qr;5ECr(

qG(O;@e
"dBQh,(Z[,0]:BpXF6KzqIF1S&Z_8x,@w=>lLJt"X%]z5??0O3Y,#TN16^XXaT*"];f}PNXD*rJRr([
5
poECf~-2.;N_o30|xH%,0X!!_}g7&!x5"f0-2BI<Gi&|akwSY_:`sd1a?,@7,@ulT"Y_JZ_S9~-w9Q4)
qw1e5:MCV2fO,PX,*l![?/dKb$B*2?i"C^&T
SA*i?nn1j4smEH/Om<gHiqpMemvoto+FUEk~1=e2#t)a9rqe5o@Qsu@]1,;?h~$15lQNs-$&GmmM1^T9A3Z10D15A^P4TFa~%rPH_Gk:NW?+2B
RM/r"l}!&q3(&aI`Ac23pWshrOVYqB{PQ5>uTo7MM7?bl3!uvXofYkb+GF$sgHn("/lr8$2wJ*u_Rb2O{LKj$@9qcB2Z{L
FigrG_uMMe1nvZ`u/f[RPJU.>+
NMhQ7Y,;Q_]H>qFaX8*l55)-BDW,iSPvNtO(BZsqaa[dsu@?2w$G3GpBT,Y("#gYCE.W~0mqmr.v}f/*ZHZ`Nse*48c-:?6Qr?]d1:Aq]yQC9:2-p7]FOT0?]1AEIov[7XrPh`BRJo|A3QM:Kg{?-:
.SK=8>9(@v[(KO$_7q+tOYsI"VGTa9exV8U1lfd5CdQraQvZx)3nmZH-b
FG@df_&Bp_>Z"&p,b]19][,3@x_)
]+bC_?enm[QYbDT#|(E&t)st07|a;s!DH0l=hvrQ*Oa^>c=4B?_*h?zK.1*^M/|eVmf1p`|HR$?dBn`lAFyFLh^.TR|V(5,N|kRQ7,Wae>^xL@9wFgM8.gJhDsR(WB1La6+]M[,6
X<xXWY)=(iRHBpB.3]6QJObiA1`b@!NOMS]#pw3!d=f=#DBT0!lTB>i%)8J)97EF$"t~dAU<!Y.u;|oeLtCFSKRhV8r_j":)k,w1</C,pkE@#t5@ET>xSFo-h#)lr!E_f0&CyuH8[S.Q-UkTKSlDRL`iwfoTB>i3O%EGCv/s5.02kZ92n_,_FDM2:pv,nTknX^XIo{XKoMVD_FZl0|OdFl[f8ES1=9+D0(5GI~H2d")DRI]gP6#ro"A%v=9zsMt:G=O.E?/D51dZ]<bzNXfzEgGcp&PO]yZ{&*&nSj?=/Y9dXA=jq8[_4euWBkoO:Ge`0=wD?}?M:Z.sUn$sk$Xu.ui4$FND"z?YsFqsiI0PReU&+t^@*!#s%u+u2T154Pk>mmm:-F#9>wdj9^F&>f_7sBez#?KqUR.W8DA<ysXM+-L:L-82Jd,R)u41+V(m,#o{&pgV#hN|
,jASW`dv3GfC+Wyp2;56h/lDO#Y67iooAbB=nm^K^a@M2j^>4!64K$X,[Pb?I,?:+X^eb(nOO"OE"[m&G.@P}h)QM;Tq"j?F9!=`*`U*51*m|]Fk~M$@ZGzQtv}t6u@wS4cCbgk.K;lE
)|RzV.*0H3x0`.TG;6m23PxjYqSt-S`L"/(g
GTU@@mM
bRN@Kb^<@,>uQXcOnqx=S4+
d2h$-QG"?w~se8&vw4#eUuMWYyoiQT(ji/y8<*sv|TEMIE,KgV@SZ"iE1[.tXS
q8!c?au4r
8yxpkL@Mth]_Nq^#GnqYc4ZJnzU9e;oSL?bzPr2YLQ,xhEn_Kkz&>FV_Rut3K6AWt11wNwZzJF!@Zn1mmrqO5J_Va&W
d%Z<F3MAs,7@52K{dfJcwo:~;sq!krncWC7ZwDAw20&G&h*k?nt#jR:&r5NmMCfXZI75lB6|[:=uZeTX!3L~s/SNb7qFA0e=dh6HSAt1+=-G6V^5)]5.V2"O^]8%.v6*#"t/scIlp+,xCw^]RJ>3z"c@UF72USvov5SWv6Z;:C8D8chAT`ueEk8C(mufr4
%8c[jMzV(jTx*lY3_day=@GgHKJ,i.h.6tz)Qid-w**7y
v0K0+CX^spz0n@khBBN(m_2Pv;b5d8Z0FoBjuh%>E(F(=AWu0h0tr/~DahJyDX;Neb^v82?-oN&fsZ[#n
{T4y?adH7/o,;K()Q%`>S+:n7
Cs:]4.V:TCDH
73XBrS#tiIMC';break;case'th':$d=')c0WNaMD9,{0l8$%A(=EYntS1)zd6#|qO5L9[U"[~oQjU8pQXPUF"6~N(k!tj>>2dVU>6gC;I:v!S8Y3#k{<lA[g7gLkWb0nRh};-"BS[LU@&roBmIL=ly%r%[Phe3@u/*au[IN[iEz/eA`TVX5R/4{<o<7pBVep8IFSAgP*]T5f&:sr+tAj5cvy%q8KIyvi+QBuu-vokGw0tGRr+,iwYRx7$YZMC1:HPx4!IJQrL8-/hO>)2Yb4FD"90@mM}9A6qtziOp,Ejafp9p"cv/30B.[?p;+<d9Bm{&dh[;$LGOzNqSPo/e#vio0]:6udb$u2^<M6%Q{b1,oBaWlt9Tsh=>Sd@+nZa+w@1:KUG:ODn<(#5A>)j?~vJS^BY_lJG`CnrE]rKI$^Ervz#`3;X[ZxZrLvA/LIk)pq$a]XjV!^Kt5t3gzITc+Qe/du^]Ct#q
w=,M[ccMyF:T4ynrbF=9N`.jQ>!FgO#6s
#uwthn5{L&,|AX=|n_V>u@v8"~&iKSsC-]/W8&D^D=>&/i7k,?[iT6`z,ct66cMlz(T*@(F]hnp~x9#Nar"ym{otpf,TVZQ|PeEN!6c^/wI|A_#>j7);gHEk3P8p>idboM[c*N:JK.CMKtUitI.Y#o=nNuCqfMm3PY_lH
m1T[7RHil_oIRy^JZ4y+W*i_w"YK`g/e%)lm^*uLkam,Pp>Axkd81VY-HnyXZP2cb6!x1ky.h!2h^uye_+:mqlO<8$)kbG/:sFA-^Q^rNN4$m2H,c5WaJpf+h.vt.XE$
^$,7B#<<m7_>TUhF(x=`
r{!"gFE=uqfva3)dtT93TcU"vX?os|%AJ:G9BJspYaJ]Kdoxmj?UJAbOO(*Pk5uF]g^#mH<*4:"
MQs|xF+ex@>Pmu),iMNGbXwa7(a.t7Xt3`]Pck%*-}IX,3a@lM(|K./fx4$Y%0rr_)SsPjaz-f>@.<JId.+0!zrI9P2`@[I<k-"sk`eNH8L!1a2g7=8gW{M|?g"TsQE"k~CP_cM])n;J"oK<CAW{PlLgca0<t>uimeE&4$D62XjznHlCfnZ]E5r3,{8m2M1phN?tKrUSLy-=6I!EH"z&hAr_RHpzS`SJmSoLV4OzWxVfa{f(d(*RD~<(@!7&U2)BVQWE.TJr:mEE1.HP4:MwNz]dxK>920d_j2df//ur9x!j7Sx=yNis-@Zi/cj3d,
|&AGoX3pN/kY
vIEO/c`-Wg/X%xXA-)eaHrTk5j8eA#K=ut^#-?xl1]9<bh$9QP/CiauqkXpFhHBw6<FN$abg={#Xm:X5/=AkK9J%u"MT?F_!KQqf&.`v]KdFdUY]Nu#?f7r&`x%h@Z.:dXi(g+ULI},21(w)Ze^^KIg2PDmC/F0ht/Q0X[EI5$+)V~f(sO9`?}P7O.AJFH]X.)s"a9s0Dj5wTV*hw84qG|10#IMcql.IFP^fR[m]1SipPS;<Guhq$yv3s,]qaHWpZlEY6MSy)&E}
8RBc]>2LPD117rPUD_@T=$K_nI]N@H$YL50ZOm8j@DrV+yji=$C=Yr-4^/7u"F"?c$pnBE$Tzn3vzl)qF9ztx-0p!Qb2NPp4Ps)K8lA_0)3g;2AqMlm?T7P;Y6="@%z+Cmgb,2Wl}PJKR[!,3K>"HElDAiu_Fq%%#oKK#&@do5i1F#iw<aIwX`SeRggO9F[&(K@q5IA.C0|i7YrH4WYU/w=Q#YO*+RPPDqJBLQ{,MX!w1j!EvC^l{&l#:DE9O%zVa6/_$c>5i*E8k>6w6V`FjH<1XmXqi-:iVcPv#SR/oG@yN16,owa6{24CYsLj(aZw[Wbo6
x7H/5_bG;+0p*n3;/Nb9=,=rmmt:x:*iH_s
NQR%mmo@zti`GuAb{u$NHAD;b_Kw!$32ckQ4rgY?=@M0}?=pu
`Mw6S*n-iH)N#5lXdGK!NXuPL+~U-bqj,V0hOMsPF2c%Fl@m&?j"Uqa?X%oV_I9L4L4b=R]H9AUuC_D&q`;=5%GI{B<cExm-lOb!<kWS&1*@TL-$hCH=HmUvS^F@*K$+TSlboPIj<pu@.-]?i/MO!UC?,`5F|Jnvx1>lBf_9SO-Xnvz5v%F/"EcQ(Py6?>F-udolG)TRt$n;vnj/yq2#_KMv7X@3~]a9W*cM:N8=+dL[VB1R&PsHfPOn}O2s#mHPNq;JXFI%{RM?3sBpMOL2F7AXKC^*
.F>Dp%f@yuOu;wS^MwXD6f*(aPEJ@2(<0G^r5uT:+FK/Y"N%(dG9g=fV6OU~2/#yx1AqW9s-@c)/-I;oSVV5AM@@Dh]67!kh%x]qOwFX
PA:(|i*2gl"T~TFW/564KJO#Xf1VzU.+tyaF*hQQ>wo"?R#]F^#-t6U&C>x&~N36rOpmK.tm@J4hagRKC9?.Kl-&VhT0;]$3Ae*9|KH-j0|dYs
g=-o4d`-
eR5"72.d>RS5U[hmMV&YOOCp.o0[RB
9m^1.h89.zu*gJ[p8g(i9&#$O<c$3Y9W+a8*WnppcwI#guuE<!wPCk+uwb`mDQ^%i8O_"U66)U/t^RT$,L0pC<8XWW"&!{XfD9X?4$!I@85m=I]
Q8)w:>Yk&8Ia@*.lNx@h2lEa.#)tF~Af5?1`v22zKN_zmdp
KX:vc{g2o^=0(kowV$t
G!U9G$ZgJ7as5A3PY|P*l16oxr/0o9R91K]:Q[,1!4T}-;KQFVR@u?E!LUm%D5-?"]"nCCdTY
-*>^90(HVMf><(_iODVTsnMz=_1j2y,k6.fvi1m)kshCrA(XK,dG1-yFc
"e[k.7Rf8~:<WcCRn}PS;pnmZF?/^OYcxO%~M|!@AcL`r,8H7s:uTlJ/aN1j9csJi]hWPtSrI:p,Nn?%/B(k^Q8?(Qn?@&[0GH)4_ZR^,TL+5q#;uOyg&|nk:Ffjs@nMADy$ljlO6TcI<5UTE.#:t|;8)ojQ*4g/8~Bz6M<3uNuu3(UD!^Xc<1^+)w[EMgq>:0y}G&Qg@=b(e),o%^AT2#3EU52"_$54)de2d_TuV^
:]/oufi/&y|]xOUR0_wn-c(-!Q,v@_d/F4J.HPhISq0Ijp]WznPVEJ(WR/.<"iCkl!=OwI=J<B!%cx9dEEqhB@)f#Z^><S@"rH)._>ZR~,LIrfsK%;#Hi#|1~8aLnuSI5aKM?IM
=vf2XhoN_p[Z~CBrXCt-<7ob,U(xMl3_M)Kd5hM$cZ
_E/oqa$=AD&/[QR#+qTYX;CB/KJ
yAvRI[xk%lmH+Rfm6nl|tA$O+A<thDVc;N-r9r`&cz"=5Z3Ge}UI1C6dgRBC?"U3s!VVJSl"<LL</vgs1tClbl@Q/@":b6fA!U`R1%+vQ4)}@~:bCum-+/iRvbRXuy-Y*le|Z7l&(QAIANNF/y:yEy]Y)|!g*JI2_/
(3)ts8l`}0PISkN?3fog
Eq$796m|Y5UiO}c3MBt<6kRg9GbAMh#YxR55r(u08yqY*Osxo/I~FyKRHWBnq2G7`p?E6m?L3{#Z*h`Zp48y0z!q1i%$l>^I)Yt-dNi}c#@:0Q/#gA1#!LxUt|qj"gjn+$9m-&Y1UHGqe<imTa$IWnt]d!mnA)/hq,qWp4)|DQ@+.fXx+[ABo`+x^uCVesB6
03HDoAxjTkJ+,10#;G,Dc(=
bdY.3VA.0Bo%rCcmtw{MIIeAu;k6yi]T2rbNX4(
ov]mFc[yYAN0KB5rXS8bg$ze>Iu_%gdymS3XA-*=[Mxt%NO5IgXpG/pn8
WVs8po?_w`A>t[x(JCXxfLL
917A!wqx-3:R=O&8D`y!4=X)A#m1Qr/6BZsAW-nSHXm&~LV.}7P2B-A-fk]Wmgn:DjV;hG.cA+4PZieQ;g)I^G|@9yVV;(hZ/`.an*,e]U&`%G?-|YHFZTqyNd+3*&;F:<qQ!kjW4l/>FTl`T9"72#e2pU+e:d:.
sf>JL/A48a0)6!;I=-mT^rJRZ
3u.SJDt/
"9nEhb30*;79<F;
*p]+dD#>`^7jOE2[k#/mgLq;e&VBXd)7?lXqonY_K)*EHfrR{c?kd$@/IYKslr:i"l"7BR95eW&_e)&.c
aj*E<?qsruje/?_]5W`L+N/dqQpjrm5uE#
[m9-1]c#4E-~G<HKsc>8O7jaij$HXh(o(89n(ggl1sB3DJg4niZr]6Ra=M+B2;ILmOnjk{@J*R@P:WhB<%F>2[l!h
K&QoBx&RrvKr=3Tgs~)em-xos2+gyuoK).eg1!xXGNY_Q,Z~(CQ2h_7AIj)BS8KwMpo+=_Kahck>DQ`&"OA*O[$NBSDq[9_4j%>mTnlj)@F9
V^Wc"bF4z^T*z_,<R,}Lt?jN/+2a,;&NgkB*Mf)KkBmy)vbbR0]j.dq@GSmlCOFI)a6=-i2nUnQU}gO7X!S[3[Xfbq)Ai@+Fg`2LS"l%;)q/Ye+(a+:lgJ,_M"WAI:^+E%o)
F/kE(SnkVutlY.O&Qi4gSbNlKXAPC%+<=m)7(mw-7g#i.SxnJ>%S;1vRp)c1TNwn@nl=("XM9BIp16T%ZTUqCVn~Eqo$ls,Z,:u!j~rXtt^UO"*g(p&S3joSNU8#&f>ygre{F-]xnSRLCJuNH+j=^,8@]!u^4{a(+<>A[*BnHo0UD:=pay(hyF4(VM^4q.Pen{F.TK`|a55)2mRD1,VxH<m/9~%z]I4YH&^llqU(wSM
&L#d:^<rUj5{v|6ft!I_0bj%?-&0)[PdsI^kd6@a__[NE$bgFyASqV9Jq#Z6ycam7v0[x0ULRxZ;<b
Wg7Ohk"(e??DLZgptiD6@R+B.jGbIU$^1fOs
;~vu$[R$:@7.?WiCm,A}j7c:w]v08i?+#7U$w<Z(oQ]cgy"z.v63PAe<*mLKIa]6;PUie2JSD]R4*MZ3.K_ka!f]^CcqfB!OYGF0mg3g@d6MV0skZ]UGo}dZ!zNyc
hJUDL(
p6zG/+$[CuU:@Z!B_l64fkDq^=#0]]|xj*E::Wp(SHerSQ-UF=O3U3g<yVrRrB!J}>tn3sB?WA=&~+9=#1&]ZmP0}m@z!9vi&)zsxO5ZiZ~]snPooY@P*J[_!+,V>DFC@][e3Y}RX.dX{1-o6z(q1gX)f:,Zs4Az)]RdON<$H<J^@w6v~4"nEMBo71zy~>cRKs4RT*U(O9JYI90Cu"WUFQqZE
3XTxGWxod#ofEcYmlls,IPDAFPEwP@1@4RK)t[xHa1ztK5;
K.L)Z.c%>==3|Ott/tJCzX~]P*]F*KdaK).=^)>tI0veIq^;5]d5ilMP"k|5K)IB4AYWftEz&(t';break;case'tr':$d='$UF@aaMDY,{0lN,!k@Pl*
M%gD.8Epl-k+6/v0L,M8gTJZS]h(*
RaZ$7Q
MO+[`qb?*KGZY%.$p;/#glK+DHM&F?Vq0R9Oi"Qx,{w0J!rXvloTVXq8dha`TmPfTXvHGOqv"U%IL]quw,Qpga*3=9cP@U0Wp*8ii~v$qW7O
9m8rO]NX-u@Qil>#l9jk!4L;kCy[)^%v+>E6#wOS~
fPV^$)LjP<rZceWhGkd_;rh4KL%g4!K_rn8LBv|IAuj62sL0Tr+MZo[=+8"2Ygo5]w&xcut512+BLgM6-25h,C]HsJ
[J3/m
cPh}eDL4=bVah$.$B~8zDgM~THy/+uV_$5,21/Dn3D0k!i[`xYwLDldy?|sqy*m
s3c7XMMea&CWHX73PuDLILQJR8<P;z,pP1wb?OvFbz/TC-(6S?^_e(v?_M;O!vm*1,w%W9R[VxZbpS!|!?AI^bd2iFx%Z<cPll*<by6aacvFc^.^oktvObIZGqUxd!Q7^J9Q:h_2F{eoY&8)g
>J5t>#+aPa!I@IeWAqk:`s9,5?rXoq3mR
o%M<^0w3JE#:sq
HTXCE=e"PJ~v8V%[w2mw%HYSbda`20k(
:*IAKrG~W5%Eb{#LX<*kNaG_HWlqcbC0]smm5dUJ!e-n3UoAb![u,1C%kZ@rDS0})XAD!XIMjny)KkdV9HFUK,<bI!H9oQS)L2-zU0%:kt:
rwL?*rHQ>(3f!^E**}hIhW?%n.qB+@kQ!7tls8DT-BPe_nYOW_qk`iff&7Sa"8n[@kH;wLynsjcp;pMq
$?}j`Ao3OfeiA/4bVTMho?k!~OCyG"09=_0;p
[u^$v,k+48n)Wo7d!Uwk((FUA*^f06e[fwsY2T
DB!Bh^jt[y<aVXkMEnY3-[)`!`M}@}>},8]<3}<fp.&am`X@N|@[OA3NTC?>#JU2D*aa2"_nWh"-oHC"kOru!kWDw#J
Y^$aN?M9ujaER;5[5V>OZx2!s;U-Ll(cl2bRrS7awZ%~-&?|sNYQe"BvHb,y6DEsOq8/@}w;J[Azj2Z]^d]8hyrcq~*hh^C@3Y$).80[9L>e;$;Ybm*DqBg=j<5%J[la<&1;w=c.p^rDVyyAn0D+X=hTFRaS^HH)ji9_Np2b"OJ[:"?+RZn3$!_L:I3hI]Cdy1wSP)>,[3f;*P]?qmCReEU<w
IXRt=p76))%^Pr[non]!jLqdIfWy_OI$5R1[C2/oHoch%NGeJw9y%Jqa,`M]d/4Nwq#Qc1iO4(Np<4"*xIAa)}>o9V9RH7%IGYrKw=0eou/#%:;3qn:X_)3QR6QJu.I$0N6@<rR/gRfk.FA,RbW#GSCG?;to7wdk/|4NOt_(9ut>7%F=``f4mD93LBVV<dCBSglUJxX&o/HiK?*)]tWRI["{XB^Y+,!l7Xd*Q~Mih-yD<3d$EvY%[ny
r6w;)[G@m&Cp`Ne(>a2xZFJ(,cy@vB<rhL`qGO4EX6Z*.yjdO_^ab|rdYSW@ju_^enT6]qIx4[%b+c!eP=.R1A[_.*fP&`XFm(?$5aSy*ckI@z74jB,fob"l8_<afzZesPRO,B<
1<fY7,mY@3_BOJI_Cglc[AD,3W&.RrS(DMo(L4mc9,,,Q&4*<pD@AC<322nS)r#iC1A-*C(f%&O3$W)vLv1kn,6#B5
VSMMPZ
RT9@-2j@^2u7XmhEO)T3OKT1ZvN).&.XS5Y3/EWeOr)nuC
^S3F&mVY0QE:]tn[6@>
T7/PJ".6#wskbS:csF3j-,$Md+H9M`VRh2#QfV3,%DQy,gU<<enEs6dfRe3m3i@0](qmQ2}%(22D2jd-:PW%L-7ems4K0z(WO`y2RV%,&e!b,7jsjX@(QPR%{`iU1k&-L8t`<.J/Wl.?%sSr~A!3y>-Oa<D<s1&*K8:w,o4B7/QvDH>jW9ciJp`N%yk@P!t!a1We]8!&BVQ.!ir">!9+1uY"-rGo2k3tuOJJiRTA|3-NHQht6D#uOJI^cObbm(6NTncc9)O9rOumPuM6~,YO!e)<?JM86ZgfosPJDQyRp``9SXI.Q#I?F7^?Et~"N99c*rUTaUc@|vboahF#Kg>k1_17b=F3y)Uruhw@?_Y_r[$alqhO=HgIT"0vk".E{Z(0gD]#1^CeUadZUDwV7EP;eQP,fbms^jc/F+.Dk$!(?)YwtCMr"C7O7vD3#Fc._VwVPbNqG?XOU?<U/1RF~+xR9;(-c[5WrLgO)S&Z`8j"E8ibh63L`$r6P;o_~"^!^ySZlF9T!"^,AZLQVwU;%,3$Jd*Zb`9b>dnQs^ZY92(4-JDpW=X_:keo}!DWA*cq(lk9H
U_5htil^X)i/9h+6PlvVLtvhf;_S!C%Yp8-`^EE`69wrjL$!K+7BwuTPGcYSo7dSi<8xh,_DZ>M5u*0eSN&(hqj<X`-?!a^*;Bj?8!Pl:%i4D`X[-Ylk
_pQ,uo0$2lB|Aw/[SOv8>X<3/v+Z#txKpCw:LccSH+@o<eryVygc4?9qi;NDCkcF6Hlv5DZwN-t
1|@--6:GEsRheL<Sy!hU(OMw7$/E@y)7o5>3W82#_6Z,VBd%KbA`2=*MnwM(?W^8>cex&i=?0=cO^W=7_K:^U<d0qGVN9q-sdg(,Pr4*Q</>3?4Zl@KE!Bd2$rmEjSS:Y$J.*RwN+<3[)vjCY7][*j49*myeVh8m?=2?V$&E8/G"`u:uTHVa@iU1Sz[p?[$kqCObjt/-"Z>CS(AK.{
mALy=$qAv/"?ZGfy3AP!nRl4IH@*Q%sg]@98oTgrtfn8$BeN*;@X2xJ;@ZtCn9cuQGRq?quekp:4<!$UR_jmfgd#X-
!{__DY?p/.VCMT$Z08@h)nWGwH:#B?#
]//<$GI&U>9$BflNPp1r;f3jDR;8.Ue7Ul&Tq$!.c@,
ag#7bVat^+0ngk;X$<3M4c(=eAa/P@RsaHUgY[/J60l93yJi-4hv26#u
)a#a,N_?J+I0/g*]*9+c[#`3cFh^)>lbXeCf*qd8VLT4[6iv>dXmHqacK)jAxPYj<vL6oU
++&x$vR(TAeC;d=}HD1!siKbcgY)KOnR6"_ev!"4^=frfA?<+jXM4_m}CPt:g0Dh/2rnLd
J(Xs>!#Z$L0T*)6Py:gn_DGANBaY(PQG"e&j{[d^tQpQS.]?F)y?,q|uOB&g?<#&&]Au5G).B];av8_r|p8;SkmgAc:OJJ!u*".sy?@uMr,L5,I%,c+;Y2[j"O0WWhg`mJS5,9PX51UrNkb@X%qfAvGEik5C6FkEVOx1icyJr?T,aCz1fUCq[q&(h?]`5hMWJ"^&0na;*Hj0N->3k`v5uY)FGR%B7@KA9SO
9QphjJ./3K`bl,g4pUqr]k-g~V<2k^SIUkjc$oj0!@ZKX5$M~VL*s5[U5$Lx/y1khju]3=+&-"n<b<#`I:{@vk:c;ej7ly:l:W?C{F,UOW=R6mN,M,;x<q#%hSCj`gGiNF)Es"T]5^uIJa<w#K*mQ+ft`<@G`g=q_gAQ[Eiw@>c!6]tqJBcM8V:*>uE#awynVmN&%Jg:?XFn0-;C),.S`WKA=-~aLjN51.:W"!DO?VR=3"KIJ<E?~.#O,Yj
g5E([QaoCfZ2BoNnTq~@{;&c[de_",
MHiO/byNG(6@H;]Yr7A^SA^s,(;oW:w
%FMW!!@BnaU5g
]dMcut*
WNC>Etl)+T&3aR_EGDQ(mb*GqLl^u9mcR[9eMK?L5WH=Say"JMAN*^f6UC)/-,dnPTtT:g#ZA=A:@@h^l)cXVPcPBl*Rosc`Z?[q
4Fn8f^5m%lGs#AlyA7S(T.17dFFC1=L`+]hD0e(lxlJ[caw9Nn?e6]Fw)[;k?Rm5^6I4Pg8k`c6-x3fv]&C2O!GKaA7Jp4oVrFih{An;0mhb#WALWW?OgldVkt^[[d@`xsmubh
f#cy<tdxl3UyAzK@sKk;l$&4BU`2>|>U+DD`j=)N%lsOS8.,`bl&4jxuk~H,mE!5T
wjl]La<bu<-^Y+9VL~4xXL<S&6R3vN@
nhDLFE=>K8_A_IKyorTA8{X?K(3_mx:XRfs|,4i9)]e2E2._YACbh@XaW}S;Ky@Xku:YB|+:bXiL^R?5nJM(W-+=3gnJMpMCG"LoY&s[a?TxOd4~/ZC|b8Pd"[=HM)dTr=oO^5n#9dvdEX=Vy4GwS5K
o|!M.#&|W%8>YuXpD.+Z[sIEu^=bfKanVyC|8>lwb-4X)YLEGVP=Q6eBz!N=+4ioWtf=I!<GJtlgVEq8w%SLR)d^,<<eKt))!vBOpB:a[E"ocshHEyc!sl7nx]3^6K2(XU_0)Q@sqnHMXaybs|bh_[x+O%
s.{hR$G=fSW4MGo%CdefFh-^3Y%bNyC$G8aulhfcfh;gKJ_K%NSG`LIlly7msAzOKKXTITd9tXsJ>^yV&JR_4^lk8p*vo5VMW>z]LcIOM7@bgU03"`JK]yp!R.DY1G!FCxPf-JE2CDcC},]_T%tLGcP%mxVK|VL)_qNlb`[tttNlyMb5%mG4v-0Bi)~kjG_k/5Qg82gEnL*[J"MvFQ8Y5du.^;<#"RM01]z7H]m#<7xye/e';break;case'uk':$d='&evLMaMD9,{0L82!Z8n/WnT^2+@-q&=o9.O?Iu!c&"fI:VPAe?i.@$h%QU=.V.K4Z=ceZEDJgP_Uw^$YyL
E*3s-Noq
yt+n@EZ>NieCUtaI.JGc/ri>i]/vS/@jj`/T!fwyPpkDPym_XZpMwiKG;`HEjcms|5]X~qTgU2}<%F:OO[$KN"Pc#<a)Pu&w2CDpTxHB=H&xI,<,|P{rEh%u(s5qcj`2PyTN~2LhV
DlU,Yp$Xsj3q^23uaI0y+LaRY5U!Jh0+7++k*s:3yY.wlVsK3KpwgeTqo!HE~<6fNrD90WTs(tH[PK;x#@sX^dwR1IIjbiQ$wNBm{Jin9_w8;D/DUt-Sgc4:?pCs4@(yenU<mmYfm_!AfiVqm=&a-]^xxj1^1s!vZvVwpXcnK^-bPMRA9q+<{^RNDiWX6:N@$B}7E:@XtG]n:VJx#L?2E@!xs3HqG.%Uck7W{WHo@uSG`/lSL0ALlbEq1Ki0YB"-(Z$9%$MNIGOP61-C"i?5E/C`mD>@OkM,|QGi(qJdodY%}*W6:j5O!U_U64]<whJg5(WcI:f+[:X8#/IYB15<`>3f%nbWZ)<C|JU97R[5cCXFH-A)FEG)w`AF?,iW1]vT_k3b{e_^:P/PS,wXtFAj-@Zt+UEdMJmxlPJAl=|FB(j=QXiCW`)*%)kM
@}u-%bTJ(6r~T<UxY^48!H^P6SuRmi
y^:Rn<iZ)9jOY&oW6&i*PYAa+"m_=Cbk7RKLiQ@S)a4X)gIJNu/2OP`4-.=&VvK(yvv^AQ(FBE+-F&7MSDKxd
F+x9N,.[(wl@xvOPB=yZu+g[x4jyewZCA$NXzC0NrBI7]
|YOaM<U`w6I$IUA,z,8!95.RZ/e.r0Z@+eYoP)U.&6R=P+rM"R!w/xW:)2vOx1lWXbNn]7h!L5}flvZJYH~l]&%Z9z&5zB9lj#l40rzc;Oxy6*hg/F;@{at,F^iDRL)/Z`b)qT7!~`o.e:UM+"N["kadftAum
@3k(hRrf[U@oHj`img:
w1c0~%t
PuV1$-q,j80htws%.4}ED2He[feG44
B]xrP%Vo54
"(GEEVzo>]6)HntE!a>ys^?CzLS1-M9`Ah&py4:B1Ki_,?`)V9ZH*2E-J]N?EB&/o[%fa96f7Wn/SGK--9.xNQcJ
wMY,UN]<c?Kp6w8{Ftd][qHXbq3X+3j3MQ&{Gt(qmVeKI]<BBh_J,tE9_`n-1[,RIK9:e(2yCYY~H%E[]k76f6&Gd,>}3(P(8-VF]W](,3?59bJRZ]AW3_c9AR;LHaAQ
|5kWA6td|WWv#UOeTEnXpsfh%xD44e%Im8;ts*%+o.Ee|T;/.?e+T:E+=p[sE0n3TB:e1jX:=57Rh^^)KUQ(i0B3.jw"P:.BYDj98u"S/$Bwu8&T^s%WN[QkS/su@$)U}i_;oQJPCFo+#4,>aP/
w1hUkwi_V6@C;L-l%g`G8ou@pxZY>uA%`_g)"DOj}jE3~A~epI%goT+I4eF4.t4rlpt-f48#8-:Fu7LB^"0V2X/._@$r[=(JRI7_YIOL
4g>D
*VK<&:V_em{9xh%;1x)2s]?Sw"Of>V{)4E)eKx_<*L!0aI(PXk]u1(h>x_Ui->6h//PF&@sAHm<I#s_ofQ@k]D
:$f5lE^t5J;7T)$-Q>iH@ZKYNVVH?U9XPMlbq;f?n<HqCK)rdw`BJj5FPQug@0@s6V^L8Z6$&QHtHzfw$AVsPYsf2TT$P3Y<.)<]Gg=?CnoMTVo+5$0o/h1N#KW==0!eymW
W#l]SS;L-}7JiV/T_;W.Sn6wC+O;CN;,jtZ:nc`|!f$^+koJ+A3*^QJPD^ox>PZC#^-"^X]^0
PMCbOn-XFV`]>#(as;"i<0o+*C`$"~cU3+>/"lU*AO.O@J)j:^dMsm:1wbL
Wzeir=ACE2GMl8H&];n>Nv!Up"5#]zf
>o$Vq"m,@GD~P^FmRm9(qfF`/ElVh8sXQ.:{pWD&c,BK;Ix5+k-a!v4c.Q`JLa]AVwP?n5vE0MHTx35oE@<xuPcbh"hE+Axv^!.#Dnd)[jYK!V+a$ER~mO-sX8X64oVqEtyBAaX|
<G2-dTNP`;Ai.%GA5C"ST65?pH6=8yhG#a~8lJ2tOZ0]AKlix7Rws@$K+`44ac/-#Wk.zfmgdEGF9Jz?B6Ub>B1^%#F?!u73
y^/w93Yi^-+&9`CYOXPI7Y6c39(2ksn:+2Im
.KXr0"aLa/%WfZPLB?pI/TdiHn|`[83y;0HY{tQ6Ebfr,Oev}S2`cg;y1N&RM"ZI8
bY,Jf(18XDokb^xEKfZ;[nH;%gWZJ=LR["fN-^fWn.*b2a(f7.5[}e/pF&UhcQV0qcepaRU
P51iNLUlR%_i[UB1.a]>ElC8HP#D+Lp_~KmQXxu%z>)X`0!J<h+:
3c0M/u
]
d.]vK:6c%N
on8O)![+XBktA~CA?le40BLlIH9O<TNFO?oU=Vdhi@fN6et:V*3)rm!{p]fR:sCw2!D{B-aO[do!s`Mcx|Qu)4eqSX"U-LE"+n.a.Kh-ULdXEVwT*okCo)Auw$Hn1*?]4i?a+m"g`~N7p3=|kP=}rsI:.|$5Z^``9dbBwd_)YAKMI,
l`B!zp{9fYpmOq"Qm2dVTgm<d(dRC5PnS--!PHe&=Eg,9T>&;hBF>kOT|Y?x&`jFoYt0mb(w9"y3.^n62"vVW^(5Fu!dCeCwi_o!$!aQ7d]d7fkVko:Y%8Aq!fii^
qPv(5-Jg#3=B"u3pvU>(@DZi/%dOUV9%W$5w&s8M*3vi.TzO#*%+aG|J%g;Kl?~:xC*hc;.wB-j&wext
x4"f8)$lR"6
Dx":?NQDTU&AHXo6Q}e08ZkyS0OzoJ_";f2,Jsi[4fgmHbS`lL4,fDwpoO3lyvg|[Y([vVDiCcChKqd(a^8VCXbo-g!,!jHs,ERe"sDpGB6fs<61OeQ"oe_ZjeAxK8=W6s8cd3@HH9!_fBgmFEokTyY6Z/U|>.5MlWiH46Uep+J}yD0+20S6`%IKgrOquLCPTfJ)e
1qa)Drb"<RQlmxW2ccM^_g$*,VE/J2uvQV*a3AU?$;S96
j:R>qW4%Du%|vfv^pgTR;A<]uSZcGYOmf7&h?/OS@:qD?@+k73qeN5=+;&(<+bAYijiQO[;,5%cl0@a^(bmzi)`?&C>fW.o-;v
D&*9$1(3mc@^ZFmONu6EXt``uJgN|9VFMLs0KU;B&-82
&]EGWSvX*O!8,CP,Bb_mL
Qbh^<1;B;&(zs!S;9S>"e^)FU81Qq3e5bmad?SY7;*cne~gX?cxH*J@$b(iGW).xP-fse[2CX,`(0{WtdLK+K"l@qNMABb(8:kNoO!XJ;X1Am[R%9i@rH.-a/<NieD6n>d(5G|p"PEsV_.TFb^)!IR;?$_qDXzW!8om>]
Tc@8aBS{F_)Jg33mS/tRHm3SCLLdA(oMh}OFVW-4HdkIx6<@1Ax~@>p}p~J*2U
e.G;kA_0uNq&g:xCY*+g
/6MW_Dx->wWe(Nt4/"$GT>I$;IW>hwF++8V`pg1y%](-Hzf#Q^9WO1AKRTj
ZDY=5f0J(frl%>SVSHqU!3+m:lSyMu)mTy0Rb"LQEdK(/CnG.&qYv7TR:VGJ/1qGYD[<8D&A<nYXKR2lg.U<.-v(m2!t4Y"oFZkGMY,%,}9eMmu2
ZbRXe"!h+cSUXk&k7/:t~t@[L<lWAP39?,>J=u{>aYSaNOFOPoQIIqT-N-LG>-s<v@St.O^_>i:6J`ap:eoI:0j%2h5qQ@-(]${Q$5)MHnT4.hCeBXMDIIEAE2`6kU30cW+uYD|lW:T6:B(^s]hA)Q`NCw&(/6;Yh@;!G%v3im6*K`<?V4=Qk<|)yY>sHG25#ChuqEUoAi+Ta^=r#ius|)xj,LJ-vc9TZ`i7C#kUqu9`7LzNh9<7XvcJ.!Iuf0u;4/=iHQw&=m,9|A*d_(<#O#/h|,M-e0rD.*ZaISwwgt$EgFyk0qp#40|C%d+H%lO-_$B7H20%6wRp]*HomspN`OoRw[9sCB?1+YOk0?a3$bKxQ%d*J_E/|Mq
EyXEa^s+ocE9YZRglZYW?>.#+ad<5QQ(w0zga_hbwm(XaK`_nQf2lu4doMmQtfDT@AF*;3kqc$H8QU$(RE}NoDZ#40jcK/S1J%i>D`rJ0iWQI5Mthm{l"^H;]HqC9mQ?r?IXHFf#P%{vBY(5Zr5)*M^2T"F#"4@%&CY1V^)J7[c1%<"^fa]<SmLqCyHZ_4QcDF118UlSzD<RAo`g[Ysf)gr*/D]Nq[g0|1e
[29?xF:A2O(iQWkUh(bIe<gq<
U5?t~hmua*wuX>oku1"=w/TL9V+MD[/ExBv2,u*vtc:8>Wi^`^J*$V%S0T`t"iZnSlRNI#^>UCq[NS>B,1Fd7;}0Bbmsy@!o}H-FGa;sH.7Rt8JeIb9WZ>}C]mHEzB&A~pGwSBRCyUO?oPE2c0>9!xI1PAhpT9|-|3[
OU$EU3ocx=%vje$c}RLIQI`=1K&[Y-hjxcKR`OCK0KULPiER586$<TBWYmu&W(9DfJ[K,ge4m)-?f.=9%FL-n50MMq}SZ;{1PoIMlkKA<j.(Py$g{Vgk`K2g^VBCtQO=E_E3KBcJvVB=r@]sJ#%mPKY
YH[;
itJhV}AvXRlfgW1-leRwt^:l7Y&(&IUti?TM#gQ9P;4znTdap9*6c"_vjpB>m]X>(hkCdy4]/b&&f~4_r`;!jC*>``O:EQ?+S9Z(]t3Z>3trZxl^bk*6S,?r[`NOL[-BJYS$rmsDADMF_W/k%F4y>Jv_.6VQw;aA8M$[>eMbJ^7[fWNR(=&`H>Zzx=iVi)xP47
%:=/]jHV1]ITkh}ja*vqge]x&pCa_RP1up;3p3?Fh:?cjb*m{u71%<&<+T{_??kP^boyO#BGGWY04E_0hC.w=#!l&wA"G
%Iq2ysy0Hs@:ol$?4J.,uWMb"Q
UD
I-w!yOvx@]Q+FrpnuZOOGjBr]
V?k:GbQc4uJ(+GuoajxZc?^A0x}lphONgL|WZR
u#,uSui~X&]q=YE/^fJ?>|UniKMa,m1fiEcE`h"Wwktlv4lk>RW{OPGxQj&|%YBU!fK-qGHlXFNdJ_+[Q?_|fn!Zo$9Z*4.VG`3wY^-vsfI2Q7_9/nw(>QL<aH4?V7$)?bbMi+:~9;"S,a9,CCF5/~X
&@/zxcQZ6|Ju?C]4&_C3R~)@v<ZMayb5=n.5QXj,&C(mDsYw<ad65zI~i?fQ5;e>jRW8%l9$([^10e]4$>[z+#Y/x3S^FR(2242_e>X[]OE.Db"GLb`|!+#kfbi2SOT|X4sgWz@,[WQv&Ioo>-w73vfe2A"+D[*xYaB"
>]
AN^p
Bx/ZJ2R4QR,U@0W*vV~ZKkAh7<L9qdvpeOLfiHSg[Tq9
=-5)$U
#JGJ}A>YE6*ij7,WJe6*YT[g#KIDMk)#~SE6w6|3)o%txX[BB+87XS9@[sBVrS|BSm*m
iH<KtF4{RrJWeE-w/.#JjUGlD65%%A#p`)s*-G;Db$m:"J17?jN;At2*xv@+>50!UMy&RUR]7,XGq*m.N%#5';break;case'vi':$d='+UF<f1<s&B~?moG&/]h__9j;1UAkKow%:JV<ee?xL4DZS1=T@T$m=sKdxEiCVCKeO!ZNLm<AksiboYqm%_{SS9?z!SR.maxiNnY3;i[yb0bItupiNX^y%HqcxUEJ7My[VQ5y/LGxK[)DmY%w7u5.eLPLnP.9fl{mTqlUAkFnjf!nyc}]*bvO&sYbaqG"@PsZUHHm^TtJ#,.J)S,[gcAJ(t,m]@cK.j4xftR.@C3T<.E7<FId]U%v]4kn;pr<?M0k`pG-`4PAGbZyC7UK9RN_/&Vf
,=T2uSk}=ueEy;PC^9s2Wg1Ag8"yl~B]Up/IyR,{?gM}giCXBamD`Tsl8#A6+1a(0$<ihrIdWxHKtSGAG^*0tuXGI#xsIK*SX<7$Uslbf
uC(xnal#B>9>_R0?Qc-+u>V<1Mmr7Na7r/M}nSHMSC%$+2;
:LLsKb#lCtfL:YbAV7"dY(y(a|D9CL+yP|NFZGIHQDeGJ)t0]!8xSUYXQ"iW?b>vOdTUlm,G:+uKXVbi3t[nmNe!P;=6$#tVZ3!(O%+g-:%b)3,&f40"^OEmJfH"qr&UEiK{QM3cstXzk%ac($+*nyQMGB9CP-k{72?ZM_kZQwNk%zuNVH`4Pu,jfv1^,]wmDiEfg"hS<?Jk$vMmJt^-c.jWVDI]1Ac}X@+$Q_:Vbimy5]laHbusI:vlF<DysO2UAHn#6scI<84aAI#8DR_@:1d2Ix,Y]8_XBb18Cl_45JjE20Li>$w4b?v6fin&@oYg`ArqfK5>^F
Gtyq39eB4Tfl=K#BE68&i7F!sG-?bpD[YZ4M$Ii
j%ff~^22CnK9(bKeP=R
lNfyqZ~iZ`DW**XkDc)i&@.=pbxR&Nk$Jt&(C)]YFa^i(tkSH/fL!WtCSJF4MPP`q9:_C$[=D#ECflestXU8,Nim!9_@=dpC~lRH9=&Fv*jO[7{_s^o)xRd)9IBeC(<A:VV3P&Rvl5{dE#/nZimw-Y2i"u<t.Pm>y>lrZ8r4&Gt[*<o7S-,/
a=4L^KBv+Zxb)#O@k$wwCwsqh[
?^Km_#QZ=hmq<n:X{V?0]D_g;8^/li%2&-.Y,6fu77#OeIhrO@97hfzYd(v.v/wEg>bi{Yt;|*ZR}JK;aE[tb!i
L#encKwvdk:>I^knh4u53?98.M+-I`sWMmO?g88`3V
p1Fe0fUgrsQ|=WkF2(,O

DS
~a6Z,+}C1j@
5sfV>$>LR;M9j&deR,mPlAJ4Gd-@Jc!mGoNm=*2H|rr1#QpJa,Ilr):OT_!h{Q{!1f?sz2&?cfMaS+:wE&N?^1^K[EIJwT74<"U<yTNfXq3ZG=_QmHHfZiohFlDCjX=$huG9)i5(tJ_!u^=r8i2/]f!q^pEQ7dxkHu|7NH3C%RH#3Y/]da+n!@!uQw$W|g#.2^nW*b*lv]OHvlie[eiVUX#n!D2i)?OJ3b)ISkL>NPtakj:+6pH6f]iZ:can/ZrV`!M#B%B0bZ#n;r,S1Qicmq=op6rYUNj&bv~h#SLUiB+D1u7
Rd}/"I@ox(DgrFC$3JH;VwBQCrAC_Nw
}1YJx
;AscrC&I:`)gS=.>7bEFWy+&URf^|g|MG%cfTkS
l<ATd5}rd*w?kiof_*!aog3T<wZIIl7dQs}0Pn
y&Lj5tv@ZA=*Y=Sf07):=jURLb><Yb?-ap+2eW"UA!`?e2rYB(]-k%DPF"J;`X$hojYd"6)Q0pT0E*9WYl3SWZQ%Ri^_5Eh,_Tx,SW#vMAlxqw!U&nckf7X2jNgr1Dh*0"/c6o*;Wc`J+#"G-qgKuZB{HdW)jB8$G4N(:
.Y:lDL`E
8,z3Q*N/u1U_"`/Wk5ZP_v#N1d1I>SImaDQceD:Zn1KJzr:*}&"@Vtg,mA)ubeKbR6w[9UHYDv>oCIC1LHD<YJ]WgB>iEEn1FY7UrQ5pOs~!I<xUp9q@B%=@:RobDN2d2b]
os{qOS}S(aWipF!ZM.7068?$9cp6(,D+M,](e/pP{[jgg5]SQf2v?4Q10loK/xDUHB
VE]<>T.F/_@W!|lVi:
rTO%JjGE@WA?)`jc|#pN/8wN%n<MUM0VRpatZHiW>#:ktEbMp@|PDBL>E5K(OBL.)gq0&).N(:$YoZD8<F`<8^@i6mlJ>wVk!x+%Puuv)8"yeE}sWH(IKJr+&FN[7T).BQ%TIqMJJ%q7K,B<qP%rU=eeB)*?ErFUU>S9q@VP,=cPrN8;@4J4=ZN)#?|H2LPXOBYP$;2?fP$;q/,.<v3#acdy&GL
./c"4If@G<o*hH3DtjvQE]=+kTCeSU/AGh.[__1oypU)htD
.:@3.3`:.=;=+7+0lt%?YDi>tZDh[iqZa(/S9;WK;^q,Wvq?)_&DWL
Xm&O1QpvI1@|[@]593H9Zu

3~,IZwTb8.(PUEGNwxw6-D"z<yGI,/,},Tr2d+^U=uq$^})
D,7a%3-};1R<R.-_hK7nOjbc3VEXYb5Ggri(ykMta$DnNJ%{i1Yx]Ue6^n>|lo%<HxxYTl$fW6?#C1[c(>nD;v[:[v>5>2a7@7e@p9Db?1a@Jc%<E0+9hr&$x{
A$D9GPii1p:QdS<Ge[bHI7)SI%p<T3|SlI!*pK|r3sNv.m|(nTKdkh3Y"UI-0HOB
61QW,x)9o[rD:p<mm1&zS(D}_He&(w3Q*<^qG?@aJM5xw
U(Vb_/#38TLr[[/Z@MX#:o:9@kiVfqF}>llt#H[}/4QvN=iX#1$eqz
V>e/N?R*Hqu:9>ktDE9KyTL8]s$4}G79?frI5w,Sb0SM_/=52
:qxX74>,zr,>/kKbom~s285v~D#7>!$_&3-Ht/a7l/I]eRp#_1T<,3d)C;vvLf0xN_rtMm`N<+/037qpxFDE$XDV0NL;g/~vRQ[Y"*J@<O`?%DCW@!vHcI{f6p)o3[#FI!_*GGn!X,-B_0si$$jQiIFhX"(JZ]nEX-@eI$mrWww(<7yR7`wTQ)CD;(vht?8,K$ICOUs,doOes_IV/.1qhtkjN4E[S).)f
im{)`7FhqAyXkvM#Z]7aCQp`&eAy}LfISFrX9fEr0z%InD^P?c^cR@:u"b+_LWUVS<Rh@p0D)H$dg^8&iqG*E#QycjC+rxx2Mnj4
Xq*fiK@^g/kJ;M/XpzFKQcA"2Nl/^u)GB
ongiQ.4L@[^aJ|.o$O`lHAht[%l8kIXF@}RgU>_Z,-s@g<+;[WLz5np]TWT>bgB^O+d>VC%w$)1Or!CO7d#];N]f;c13sq%$n6r#Y#blU<%igu&]Hi-aUjHF6x^AE8)&.cSx2(_
u8l5*k,)`{QZJ^
s6-ChCl
+9FDb?vI7PWW,(e=;
}AkIZpHQM_IO[P93QPee[/{qb`$r*cA_<&2@)!`]0n6UQlohcRo-y0aG}"zs4Z@B,rQa(CUb:w/Uy8psB7)J&_u8xMw6DRZgCkn+cfvbD.&qzS@mHLJ5KGL9N-8Tm?d<II_QYUAaUJym{N:0ag$G?0lQ9/q
)AH^4>|e`AN;FbdWwoSTxKWrC9SFp_M5&&*c61?Y,1=gogm;`OFqX@cK?8Wu[JGmpqO&Vx,wNi[7KVn
24jmDMs0ixAQ17aUs%ECYX0$
dv]8%3Z|c>`GH;dPXB#o^M/QHW)f[I97/]w=W1UK=@JiDxr1V{Ho"{HBjb,PlEYYEQ%zCTp}"~Xvm4F*QO4c-=!9o;ci`$?F2H$F20-!:j5<S!$~"F]{W>=f!^NmA5+Qj|-)D[A91v;25hk]b6#dFD6@V(Hnf12)kG.oeG5Fd-4pBL
Zm)m2/Q#`c)ZtBu&RGJe@Q%5VIQoWcwY+s9[!TApj]ECh/>Qz#m=_ryq;k:i2T^NQHi2YoSb/>HNg2_DRpM4"<jq":*IPg4^p/X5;Uj3vD$3P3_p8L8WuyBO>-;r"G|8
*VkE#/i-AvKCLS^};CG&IBV:bv.6a0h=-/`G
te&kFl/jCER3<@u&@5X!qjS8Bd>543
cP%p[b[(5@9qguG8=)>C&)$ZYZd>$IQ`u%&k4gj
9S+c(4/NG[fp(2qGH8UNGSq&_D1
KbwdiWFgv@a
gH"TmpQe?=31Z#5hR2YSAi;12`5=#}2d/?@M+9(u5?w*KO82N&QT>TCA
DI~*iT;pr
Up!37poAM7M`9]GFFbO0^g-B9ILe,S4<jsWc-GR
3>>C0p&Y,Jl1|f.y1.Wh[krKSGzd.Pb37]+?ZOdpi4~
oxnE}pSw4H;3t1WMIY!76-v3+ge)xK]y;@6@_<)fKjI+vicgqu$dmh(OLA+YM@?m=QpI?^Ha=kYB{@b]F%{
dmGB5G%%fJk0Z3554h$n%C%^W^5;#m>.0V46tgMw8%OAtZ&0A0aHj2v:G#77|3WPpmwwi_Dlerw[Kinjodz/e%PylCOyqC<E/[ES!p@;R0Rf*WRXu-Gn_NTdqg/OyBv_9
Va
$.dM]cCk;m2=x_56_7yvkXczXKi(Q:60[8]9=R2|I;@zD#aj6WIs8PJ_xT^jnv?d_udbXGm~Z5V>Mkd5<&YpH;3=(<<pX`*K(SxrG4BZ`MaTa`HxC{4F:Y.N9Pz!Q+e[HkuaFec9.Kw~L@_s
z&$*y4)=_i$k8a5[(;8-<H
1%VA7p"FpS``+/w}/s1_WtN4yE@`Y67WDKi%_v
+mi]vYJp!rS,.0jD8rOJ1uXm9hnmBQia7xtF%%lIT]w1z0;059hOGgMDWMzDVs6P!6T>KjNARc7<s=|9.FlHDWv$,eRp@f5s<-"-h5+,nylqzQr/:Hl^+);kH
t<<G2H`F^aZ4h[Whk1U$8-bj_6rbt%9o&7=0;#YO#%I$A)Ghi<EO.f~)-;0Z"GDiV&O+nvWTb:.o$,~%w1X;j<AGnLtwJ)<ehTC7na(_
g<[LygtX';break;case'zh-TW':$d='$R]7&bos&,~]u":2d-4a%tI3O8wZQ"fNFI$J7Mx7h$/;PZ
eL
H#{/jFR*O/s.OjB@^?P2+-oX0A5ekM|Mp7hLW7n1.Y5B8yE/$ZUM>3bs26D<n6MnenSG+G]kt^JeZQ5s*n%)|?:OCrwqc=Ooxq9S@tC`i"81#pU]_c"j[?-u`]t&$O{b&T`cb:@WSo3(O7c2^ssl|dx>Ml|91h}m
LoJU0IF[QcV-4CYu7)_Ov2^ddb>5baxCmhBCYeqd_cL]@YXC>pYqICVw@AMC^.<_cg
>yU$%gAF,!@MRMbFOtv*y[:xbvVL
MJS8w>xkx+O9JW8"X%c@a>ffBiBsqcr1h*eE-YMq
3VjtE02vmSsGpRsn)BZy9-i0M(HOp8
ilP(8(+3W1:s[?%0i|L4S-Amb@iRDh+=Z|Q=HZcRTyS#n[A.6n8{s}f22ixYWF^2R6;
Y|w??>w.@au6P.WnOUGOT$u2X,=z;%Sus0),AxW4dA1Y*(DR5YFk&fId1iQR"l^,BIi#8H[WEPm;I?odeE
#9x6"Kgdy+SJbse8&U4w7f*]AdJEFgwcj=AX+C<#uq$j9EhWvP>8WvEGHro]pS$Jp9p
!cq77!
Dz5H5Gv18LB?*
XV&wY{Dc@w^,,3-JIVSN(&>o)6=7*-^I#ry":=,-2r
-HHdy9&&|w.<NCo@}Oop6:gkrOtw@`,L?veJc:Of^DmPOx_siwMuiyNXU[iU|!b_k3
^!`cd
5l]M2&I;ZKXxM5&3r:g8g00!ws-Zo])]Nqh}yI26:$Vd$?uSrFl(<~slPZ<5%RUE_;_5b
W:^5B36nDM"jYJc^q]`63{&x#Lc<epVf*bITa>tdQ"30n@e$g9Isyfam9rQm<I1>000MeDy|n~C4cWHLA{QWh|`E)^%`_*?A6.?@/cP3.NCZJ79|XwRG0U_*/]euaCJH8;pPb4a_#c&j.:x}he&S3?hU%OXG"Eq$J]R5WEpW[Cm+>%<`xZmHoo0ajl4"NrJ"SB<0Z,U>.roQDB@O-MX6l.(K9T7E0&wQ:W%_J%JEDxoXdLy;5>1Ph_-on#(}LX
TRm`1]|s=b^#B_O2]6#;CZ`h!aXeuJfyJcfQ)?t5SXQ?ksoT8R0lo>7)p_,.Ovfs,*69dPk#J6/=^uhd~kBYn^[$ph7!Ef_YQ
2>|o7l]bC.nR4RZcJ98gp(Z(.S2<"+g`oMY-xrF=sKxv81AxlGG8>
m_(5!<8iEg&.YEVc;#$e-3?Ry>)g>Gh_N){I-p^H:Si5@Ki8FZO)WGja*`VVS0r?kmv;A!c;CUOU$.W-g>/XRJbx<n";6/:"l2Q9pUV,<F$^<_(q*`6F6j|$]_!)A
gTSfrm-Fr4%sVtTsg79BIR(34IhB|(BN3CzLJw!n#0k-/Ev3
%NcLkHftvzx`v*raSw&M+p=Jyy=_Ob?d9V+Kc62flcr{&/=4"Gv5XSPtfUUk!B/<=V`gFZJ=D,ZIowL|Gs;W5_`SlZ=zj$q(tiB8.o,pMHQN[~s.#Pt0Vc-/Z~S06IQ3P[C<szp[Aps"-U`?FIy^n
J|2?XK&5!p!x)N#x"/mvK>L$a=I`qfah)o/43#xpOk(RO0EhLOqEk[a<(
;6FT]0(#AkvSL.P}=k?i,1K=:r!}YFe@3@)72MIArg7Bo=$7bYWE(<1Hh&l+eOtxkJuI1~-*#FWLCes30`e<4*8s4kbDYvkN/lPy*;aw.."?Eo52D{$mplQ`G5(Njgc6G<vq`O[|=t#E-GXBpx%?c([cR5yvRBY</;yOuR@,JZ&+fjwgb5>kvmqP-x=aE?-gdeKq^O0S@pyzOI-8#W0`l[&2sUk}#Hp>m]hWMp4Sc]Ct?Py8M
X>?9m6l7mQ)hp@vtiKx2"(<9yOZM[pR+sJ`"D|PU7!Kl,|exid9THTwI=v9O96JMF"lyo
hLe)AjS-s?Sz*9sOpk5WS%0N*+61XI:ksGmnfW*Ddd1U)
)`U_2/qp#?x&D7oaxu(l+y1|g;W_nki2hS1^87Ivh]3C7->DJbp=I?(~pT)4P4-jVA2$c(FwIDI@cF-xROZu"EdSJ=R[+R
P.R4<#C5e*khm-ES}E^j)uC^1PmC*W@A}dxQ_"PA{?|D%;nAjuWYB*E%T%egw#D$D%3AxhkLxWQ@jOgXRt.o!h!O#M>";Yd]9N)aOcw$p93:mK]xDA:"Ci+6,Q!8v_!j8Htie.k]4Vdp];W-tSzu*.`i8EcM1ba_Y6=c|9(eNOG"e:tTP91"JgV!k1-Kq8<s.UzA^]|y6CM3(aoA0@lIbRIioG(MLKz7"SF)#/=2<dy1xX
196Y9Ti0afsgqiVhR^X9M<Pt0Z39$Z"h)+qUS5]lJY4N-qB{4J@y(7m8w[a,y%cZ>StsqAty?[j=M->r?*SBz)fGp[&N,`eHsavKRht$P|O%!ZB{xNJ5J!gkO-KMQqFL/4?HuVj)!v!!IZ[}G-"&-Dw-tHWhd]`Yct9NW#fHO<=
%".*fg0m89S=E~];Tey5q<1GD-yBIhF|6I(IA2"9U"b"@f%X4%$tpNO:VSkXTy<xU_i(ZL2nAj
5O_e)].+sJH=3/y4=$F)Pi/.Z5qO9)Ovhg&Uo<Q,&RZ8om<KlA8MF_lDkPjrY
fdvt;#pbrD40CLgePQ,*tJTd*+%ND!*aY(8mx"HTeF:vU!F(Df~D)fp(XKp/od}>:8-K$*^#dE
VF,hC)>XUCe^9
Q84Y(A]sfr%3a+R%_qNovZU#_~a5$<9:Y.*"nj8iF>iMmhkn"S+xfBS
`5(m`f9{9Gf%Dw^}-m7)?fs_-9>0OOIALM5imUx1R&wS7E#83:y;:s%hfBN!LrG58*LD$Y!Xk8
VCO2_ffc.`_Ao>:d+Qh#VksU,r}*S+dQP)x^gcUid1!d,1#2+f1`+7SNOj$6fSL*ztRn#q|?`*!xc*Z-n+g+NKiI*Da9M#B>mH<Sw2kv=&()!?bIVxsAr]JiX!1#Y?iZgD6y=5:gk;alO$:*TPU9C#t:6^A5m6[UcbUm&?3OYWH2d2jlFuprV7`+%Vr4<^}T8<EHp.Uluy%f5Cd<t^RDb#wBYoIE
T8$rAM+B*gaU0A%+iAwQXw$XDcoC*:_q%fTwTw(Eap]Qu(&%6LYNZxtE(`b[EnahjS%u%<Z}XW.
%:#-i*bu2gkjt9AU?Y1u
<
>Tp$#dFo-]IwuC&7St3CxNAOYLG:H2jv6Fp:3@b1;XWhFedrd&wh2Eq.en)S.&UC<&hI)]("j^Q+s2[159&9bOx_YEb3
ajk~t_l|B3ov(a9*+FK}]SU<+k,-[-PZN^peNf]=yM*8FL/8yRx^PY7l9mm4_]"/bI;K/=F]M&cZ.LH=(o>ptHde.]sOE6s0w>bHYFpyvSTzE8a!c))_aT<-LR/z
`U;$ME}VHfwB,Qe/(4M#t9Z[;NsrdNmWS3E+52jiJY)Llq%M"9F%L$.k{6$!,81M+v)#LYqN0On5;1V8Z*6-!"poIx]ID]/lCdNT?Kq/A1jx)HB]K#/w{J`.d(&-TIOhDd/UEbV/91*U0riK76.X{J-/7
Old
G^<[hJ~CjvEa$1kl<9Loo9{iQa79P1KZy>nE0X&Wn;;rA?-SB#_&JOM`pTA[vb[@@<Ng!
yHbQP*:#vM86N3GoV(_c~Ry]GQ?vP1tP9&ns$b&3<GlthG)o4=AL#-t8Bt@d6&Hb=+glvus6+7rrdT}9F5LdR1-MkHlDRdkx%*!gcMj!k"G=YhT&F(q(FkU7zy`ier1Hi(Lo=]5]o/gN{u4HP^>QNV6)utumQ8MK<[XtqgC+>-1(gy~j/d6w*:xP[(dj~Ojor4Y:X`QOR48dlMILt2TJyjrOtx(T~yA.c!d7XuXwY6gz!XU,).0uOc*3$Bkdq.IntVA-h7=WdFZGBA0iuyIHsVwOFH$Q7w?vG8D,=p8!hi1iji-Cc8<yxYrDyv|JA)CnTY*:zOzE`-4ZK/gF(NACM%o_3,:@[&uys87FP1
kxN=Qq*Uto/QE4jz$*PlWcV^*|q#ld6]IsvaJ-N;2`y
[po2i~SS)$hfe.^;rM^=`k8sokDF
uqs^s&"pU
[A$1>Z4d,4/a+$I5ndjNRu{]i$wBLd{cf:ayt8gZ|:uYPEm,/SrT0PJ
j$59Hp/3oGYb04NMX+<]Zh:#/$;nSX^#cDw$/c?#tDe?lm:_y6fW6jjMc-y"F>.XE;NZKDg$)BHvl)._w.$m=xkQ|x{&mmz8|f(B}FmJy>V"I;i3sx!5BYyV,-}#mfa80VV321!=8ay:3^$1N;2N@Qin4^;k.q50cY9[;t&B8^%yd6
mBMb7!
>SZ*RXp4qfs`~#cb%0^2E^1(iJ0Q8R|39q$j>[<!5"xv6IIVaX(9`=N+8Q?)v@;0K]ySX./[blV"K&JtKq876*?Mp3U!vTHhm[q$y*ftGdKxp%o?4RW93QxCI,Sd!dT';break;case'zh':$d='$R]09hAp=,|?c)F=9@W&%QaahfSwK$bp}(MKaPQ->,H=XJuU$IT36/sTxY/N&8E/!9I=E#8/$-4-!Q{Tp!ppc$^){tL*4MvjpJlr[;l=`#@EWi@t5Bqqft?P(Sl1zk}ox[u16>lM+3hFzRcq8!:uuqhO>tGO(x{anbpKCQ3w-
3`2P
J4s}Q}ir4VHsaYTS
vPPrgn]JoCq
.uQm{SX-^7-Gx&|V5br,7=}b/4rm1Ki1^09K!iLLhw_U`6lj4N_cCyFH2ydbyc-x
]p`hQ^h`dU4cTW43
)Dsuh@B1^xV^4b5w&jmuxG.M`aMcbb]u4L?bPX;;kcDM@LSb=s,uyl.n}yUn;<+s1Lon}HKXg+:^5v}Xb6{1N*&=R2XUD@k,?H&v
wW8.GbgAU7[@.5S&PeAYUGT+1aMf({ONQLKY/vl]^frbpuxhfk[Ea>nsM2q*6gX)$URvi*Ipj]Rfy7e
nP7lZF_dn_JYB0_jfeR11~&DBX-5h{PW<:RU8vZ]n6
^ll_*d]i&h{UQlf2~`)UxM|QUTKD
0ccUWTIG!BrH=(_L?WQ-]N[P`Gx:)<M}hUH6@WYBx+_EX?68,FhBOwRe:it.NYZC`(0j*ZY3tpY$ld6A$-e"4&QUa39>b?QJ9?lh<khBxXX
9^L3p[9<[;<LAi3w"t2(ujxZl2ttQ@@K=:9TugcdOr0Q_#iMf7W&s:w/`k3;/S1jx.9PUuiudmfI[GtLF|niK4l/y}qBq,yFyCspML%MShV6srz$fga-I]`g*LN+Qv:b3mA9:RiUKO`c0nV:$?AW_eI;.QT^@[Vx_vmM!<(S,YUbH0
]ar0NehjOGM^{dVJ9/AL{t6O(vw./ed(cMkqcFA9Yo#f+ql<wy33Q!Hf0o#rL`IC#LK*1(/*UBKnBPUxq#hF9apY%gStN)F@K@d;T^p0c_"<.,k20[~(Reoyn(!4O`8mNv*9z1{lJL~qnS]B!
J]7p2]F))1kw~xr_-"6n,fk:8&wGtCZ5xPgQc`qe_.lg-@9Q8_LgA3IZtIfk0Nvt6q@(KDJ5/l#L[9Z.wE0E%iL81xN`[6agEE4XS)Gt6:=@pD^
kAg1mDr<eIONn_&)"
+i%b&a)
hW;V*X31<C
l@=))Un!3cwevz+!uD<^
_aY#_GoQ;i,D@Q2T^U7L&lxdS?VqD?`g}R;D>K*iEt&(%V>oC
4gU(MfAK&d~ll[#N6O.o61T)!<Gsc<E$Ccj&Wk%huUK+{;15a??iCa[@g6NKEEv![v~epTF<
E5u&^K>fh`h9W8HZf0,tn5vVVKK+0bY*<KA4dn>Pgj*uyo@S#e9?_=EC0d$yoI#wiz
B
1nSQLU}SuB.ka%_2NxauGGshC@#sY+_K)"KG88?g{CKSp
(R&gO5VxcXb.]l7i?O>DFhE)o
.MBbE+.C1o)ikXwUtA|[gv?)N1PllDb8mQ9,H=dGxt=-=i0&iA.D~,7TB=QVRSg#9DS9GS4^7B|"c,$vo@>U?0[F!p5G2XFHU8w)UVYQ@LnF#(;U92In3uIKTI5OI<YkFxV=Hs%l[Ix>Fu;m/$8KNLq%8<]AmrRS!juK54K[),Hd~ZNW!46t`PgoLYxFI-@0q6J?:j=$apt^,H~nQe%Ji#^OrB5LOwzZ$4,c32)73R1>(K/VR>P]?jS@3MK-,HCRW.n4|VRScOWlbBJ&>%P<4VRUm^{W>_QArBUV9i4B7w.bzGu1QGYxuj3;22|A-W"6/ohS1Mv`|
PmeIYgdX_n#L|-^SB&}_D+)+dU!o
X]>i;N=Ewq/<OP0,HfPVQjdo0hiPt3T^uBfRpx]TIr9k"_^&Nqv^r{>U+ua6fdF;w~^0NMOdekHd^v;9H+I,k^9$,D.+!$]oqVKI=+(Ya_UgdX@aM~)>ojQg)mUeXgQr;(S(UAL3L"QJ6~x]n&g"GJugKhPzKTE8,6={[Yq&TzSh[<`EM~+/*{QvfUsxcyMlg,M(r`oD#+1"(@$uccT$b|omdR[ox,D.-+HN`,OOV@6@4)OzHfxd/1pd%"5|KPQ/Vg15+.p#lQKRtS
6/##-<z>z^!txh
(2?@BE0_Q]B>X_?Hm4mDN0OuydFPyt6GujT>-3N0#Y<R>7[3IHZmqdKhx=U>DB]r6aPbZYuT]K[PHw(<^[4T_H!nTR1&ZBoQ3@
jec^fp8RRb10"u{@BPi(xn1-F@^8e=(k[V(eEY!udk(MbFcJYN."}V-F0T]1DU"C3AwI;gGG}yzj6"C-mV34T(&weG:[1+Gavx{5f?/rc$2C6Oz>k?!n?>$]LO2=1T6]lw$UIYN&(!c.l2m18nR<[Xoldo[jx^76m2s@yH&`V5Zw*@CiB+ai#"|A1_"Hx6=y#3A(fi<L=j%2zILso.fygVS=ns?K`ERc
L:NW_0<5Dy6.W"!Cx&7A^n]9;@+$@PLTL74H)jtP@#o$QDYd5F@UYP($9WM{BR8y#IDr4Hx[a!yhSrt<rpc)Fd)g`:j;Z"@.x1B4@"Xm$mNUB&R
CW(xe+"wR}dK!0*B`T4CBMlNdhY:eum%Lg&9<:IeW;&v!"(;,G/{u#xBw3ni.[OaN0Jn&o?s2vtp_<QR6wRTJ).)6#t}Rb%k[a?I9!N6jGH<`<3j[J!U/HSaW.UM&n;5vK]5440Yr)^`I4_NkfNc%+c=EsY=MzqJbFE/"*XaAuf`O`";_
9kF~TB4;
*G+!]A?4:g["=6,cz+nAL4|GSeNT^7dAeUk?QKE_Q0+LoKktOG&JuQd>4:o.`sheH%Lo$`"Nq1_(JJ_]J#],[h/ZR<#!JYk`qkn(Yh
.tt2w(2o!q*`if.g4^O$E?`-(L8MNJJ<qqX([hQ)#XMOOug>8D)/9dcm)wpsnE,4O;dp9KP>k0&339[$O#ZzVNj>8oV^EDOU%-?>?|a;ep>(:dZ#@;[ykJ$=l`H,:{BlGPoz";F3E`0Z<{G|ty2U#:W"[U[z9RU%O!^yY}]w5@SV0HQa,i+wI?$W/Q=E=i$2IOB&lb3$LURsC$t+i.to=}1S9,YhEJ[sK,POliZg]D7:1#6WT7QF
HkJqtm%!T,"#I*o63s5RkBZp5/m;t%uo=42#;ft;s`}!C=A.AHz+h-k/utIYM$`(mU/!WZ7^a)eSek0H$@vI_0PY-x&X1nz%I0w.Q``UrJ2O<`o`oYH)~dC
(>qMk5HbO+1R--^=zD[l:UO!gQSke&^j%9YNg9Mn/@3])o|E!WbY($|/])IbIx%IZMu8tlUQY2{
>IF!h9(p>:?(Jk
fK,1#6ZT^LWl)af>l]QDD0G33xgN/*`t-s3X0+&E-5)]W%(Ni4Rt/2#)3!j,w[,yF#4^PD#u/9k."F
UXK5:dydS,B$0H)!mwb:38p:{V](Nus.ZSaodl=uGqKmsgp$ALj9"E|tnmuO8qJo?XP1m6~SS5*1,Ng;vZ0fNTp:eT#Dir)hvCww1jM$7RSsJNSQpWuRDYpJ<J?./Ka.nKz6@"DeIgba7SsIPV;4xlU4ZF0g^PPBMl-KJ?qS3=g9ZF?-Ys,%J4`Y}#EBEp3lUYpW
udD+0ndJHdSp>WkSbHr,`H6!j9F~pdG_y$S!oFS|uc7sWdbsVs5~HD[5*(F*dZf@u5X!=g""-Yetf6xnH;PT>Rs63p/~W$.nRLS.RYp4X8:hVE1`[tcKOs.P6c<$R:ZT^imeZp$/&jDPKq&e<Q,!CA
v?h/e<VQC0c(nnwLw?v
3a}Rkj##&(`<@n2h_NiDX`B+_hLa/QA(yZ1g27CXQ"`9VN#jwLjlwHP"o!J&|xKgjWn39s[P}_N@vrDN9a=@/,">^1"*Gi|CgtAv&W|?D1)dxil<rYe/W
K2k*sAI86(J/JCMDX&SHHSZC6%9?G?
M`aMspnh*s;|Tp]nt!>jKfxurDP)=ko:6pE8SzSZRg1zs0w3
uP2Ec73!"5TZ1.w;!LwYtZuov0Bg&yD]jtP.&`Og`LlGu1
8Ar"PY"<7!W.6"VNHWYB`Rx"Am#6g*/`xSck^bcmp_<cDMgeI_,BT-e$R]2,c-h<5iEa2yWal86R]=Gmi&D2g#pI.im`qjnAn7$b-VhzDQnu.s0E"XZ9m25;I$k1v+mW:geeV2fi",=O]
NX5g&x6stnIC@7("CR_YFfXFWk-<Me4$_@Qtu}jRjhH),$2(46LeTH6v=r7LJ5y}9r=Jc?@;6mm97`cL_n^GOOSj+=_[m)Rv:
X0@eJoY`;$
URzWC<q)rIj,*$CejeBv`1)_c33pL5"/+9NFvUb3vY&ET;{#WoMSZQ,Y7i}M7::^npEy;`nd_h$Cd&zj&I"Gvd!dT';break;}return
json_decode(decompress_string($d),true);}function
get_plural_translation_id($u){$bj=array('Too many unsuccessful logins, try again in %d minute(s).'=>143,'%d process(es) have been killed.'=>293,'%d query(s) executed OK.'=>199,'Query executed OK, %d row(s) affected.'=>197,'%d row(s) have been imported.'=>300,'Routine has been called, %d row(s) affected.'=>236,'%d row(s)'=>196,'%d byte(s)'=>43,'%d item(s) have been affected.'=>297,);return
isset($bj[$u])?$bj[$u]:null;}$lm=$_SESSION["translations"];$hg=Locale::get()->getLanguage();if($_SESSION["translations_version"]!=3097892840){$lm=[];$_SESSION["translations_version"]=3097892840;}if($_SESSION["translations_language"]!=$hg){$lm=[];$_SESSION["translations_language"]=$hg;}if(!$lm){$lm=get_translations($hg);$_SESSION["translations"]=$lm;}Locale::get()->setTranslations($lm);$_a=null;$qc=false;$Af=null;if(function_exists('\adminneo_instance')){$_a=\adminneo_instance();$qc=true;}elseif(file_exists("adminneo-instance.php")){$_a=include_once"adminneo-instance.php";$qc=true;}if($qc&&!$_a
instanceof
Admin&&!$_a
instanceof
Pluginer){$_a=null;$xg="href=https://github.com/adminneo-org/adminneo#advanced-customizations ".target_blank();$Af=lang(132,"<b>adminneo-instance.php</b>","<b>adminneo_instance()</b>","Admin::create()")." <a $xg>".lang(1)."</a>";}if(!$_a)$_a=Admin::create();if($Af)$_a->addError($Af);if($hj!==null&&!isset($_GET["settings"])){$_a->getSettings()->updateParameter("lang",$hj);redirect(remove_from_uri());}if(!defined("AdminNeo\DRIVER")){define("AdminNeo\DRIVER",null);define("AdminNeo\DIALECT",null);}define("AdminNeo\SERVER",DRIVER?$_GET[DRIVER]:null);define("AdminNeo\DB",isset($_GET["db"])?$_GET["db"]:"");define("AdminNeo\BASE_URL",preg_replace('~\?.*~','',relative_uri()));define("AdminNeo\ME",BASE_URL.'?'.(sid()?session_name()."=".urlencode(session_id()).'&':'').(SERVER!==null?DRIVER."=".urlencode(SERVER).'&':'').($_GET["ext"]?"ext=".urlencode($_GET["ext"]).'&':'').(isset($_GET["username"])?"username=".urlencode($_GET["username"]).'&':'').(DB!=""?'db='.urlencode(DB).'&'.(isset($_GET["ns"])?"ns=".urlencode($_GET["ns"])."&":""):''));define("AdminNeo\HOME_URL",BASE_URL?:".");define("AdminNeo\SERVER_HOME_URL",substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1)?:".");if(isset($_GET["set"])){header("Content-Type: text/javascript; charset=utf-8");if(!verify_token()){header("HTTP/1.1 403 Forbidden");exit;}if($_GET["set"]=="navigation-width"){$kn=isset($_POST["width"])?$_POST["width"]:"";if($kn!=""){$kn=min(max((float)$kn,Settings::$NavigationWidthMin),Settings::$NavigationWidthMax);Admin::get()->getSettings()->updateParameter("navigationWidth",sprintf("%.2F",$kn));}else
Admin::get()->getSettings()->updateParameter("navigationWidth",null);}if($_GET["set"]=="export-settings")Admin::get()->getSettings()->updateParameters(["exportFormat"=>isset($_POST["format"])?$_POST["format"]:"","exportOutput"=>isset($_POST["output"])?$_POST["output"]:"",]);exit;}const
VERSION="5.8.0";function
page_header($S,$fb=[]){if(!headers_sent()&&!array_sum(array_column(ob_get_status(true),"buffer_used")))ini_set("zlib.output_compression","1");page_headers();if(is_ajax()&&Admin::get()->getErrors()){page_messages();exit;}if(!ob_get_level())ob_start(null,4096);$S=strip_tags($S);$Dk=$fb!==false&&$fb!==null&&SERVER!=""?" - ".h(Admin::get()->getServerName(SERVER)):"";$Fk=strip_tags(Admin::get()->getServiceTitle());$bm=$S.$Dk." - ".($Fk!=""?$Fk:"AdminNeo");echo'<!DOCTYPE html>
<html lang=\'',Locale::get()->getLanguage(),'\' dir=\'',lang(133),'\' class=\'',lang(133),' nojs\'>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="robots" content="noindex, nofollow">
	<meta name="viewport" content="width=device-width, initial-scale=1"/>

	<title>',$bm,'</title>

	';$Hb=validate_color_variant(Admin::get()->getConfig()->getColorVariant());echo"<link rel='stylesheet' href='",link_files("default-$Hb.css",[]),"'>\n";if(!Admin::get()->isLightModeForced())echo"<link rel='stylesheet' ".(!Admin::get()->isDarkModeForced()?"media='(prefers-color-scheme: dark)' ":"")."href='",link_files("default-$Hb-dark.css",[]),"'>\n";$Ul=Admin::get()->getSettings()->getTheme();list($Ul,$Hb)=validate_theme($Ul,$Hb);if($Ul!="default"){echo"<link rel='stylesheet' href='",link_files("$Ul-$Hb.css",[]),"'>\n";if(!Admin::get()->isLightModeForced())echo"<link rel='stylesheet' ".(!Admin::get()->isDarkModeForced()?"media='(prefers-color-scheme: dark)' ":"")."href='",link_files("$Ul-$Hb-dark.css",[]),"'>\n";}foreach(Admin::get()->getCssUrls()as$Fm){if(strpos($Fm,"adminneo-dark.css")===0&&!Admin::get()->isDarkModeForced())echo"<link rel='stylesheet' media='(prefers-color-scheme: dark)' href='",h($Fm),"'>\n";else
echo"<link rel='stylesheet' href='",h($Fm),"'>\n";}$wh=Admin::get()->getSettings()->getNavigationWidth();echo"<style id='navigation-width'>";if($wh)echo"@media screen and (min-width: 1024px) { :root { --menu-width: ",sprintf("%.2F",$wh),"rem } }";echo"</style>\n",script_src(link_files("main.js",[]));foreach(Admin::get()->getJsUrls()as$Fm)echo
script_src($Fm);Admin::get()->printFavicons();Admin::get()->printToHead();echo'</head>
<body>
<script',nonce(),'>
	// The event handlers and the <html> classes are registered by functions.js.
	const offlineMessage = \'',js_escape(lang(134)),'\';
	const thousandsSeparator = \'',js_escape(lang(109)),'\';
</script>


',"<div id='help' class='jush-".DIALECT." jsonly hidden'></div>",script("initHelpPopup();"),'<menu class="access-menu">','<li><a href="#main-content">'.lang(135).'</a></li>','<li><a class="panel-link" href="#navigation-panel">'.lang(136).'</a></li>';if($fb!==null&&DB!=""&&$_GET["ns"]!=="")echo'<li><a class="panel-link" href="#tables">'.lang(137).'</a></li>';echo'</menu>',"<div id='content'>\n","<div class='header'>\n","<button id='open-navigation-button' type='button' class='button light navigation-button' title='",lang(138),"' aria-controls='navigation-panel' aria-expanded='false'>",icon_solo("menu"),"</button>";if($fb!==null){echo'<nav class="breadcrumbs"><ul>',"<li><a href='".h(HOME_URL)."' title='",lang(139),"'>",icon_solo("home"),"</a></li>";$Bk=h(Admin::get()->getServerName(SERVER??""));if($fb===false)echo"<li>$Bk</li>";else{$x=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);echo"<li><a href='".h($x)."' accesskey='1' title='Alt+Shift+1'>$Bk</a></li>";if($_GET["ns"]!=""||(DB!=""&&is_array($fb)))echo'<li><a href="'.h($x."&db=".urlencode(DB).(support("scheme")?"&ns=":"")).'">'.h(DB).'</a></li>';if($fb===true){if($_GET["ns"]!="")echo'<li>'.h($_GET["ns"]).'</li>';else
echo"<li>",h(DB),"</li>";}else{if($_GET["ns"]!="")echo'<li><a href="'.h(substr(ME,0,-1)).'">'.h($_GET["ns"]).'</a></li>';foreach($fb
as$u=>$W){if(is_string($u)){$Hc=(is_array($W)?$W[1]:h($W));if($Hc!="")echo"<li><a href='".h(ME."$u=").urlencode(is_array($W)?$W[0]:$W)."'>$Hc</a></li>";}else
echo"<li>$W</li>\n";}}}echo"</ul></nav>";}echo"</div>\n","<div id='main-content'>\n","<h1>$S</h1>\n","<div id='ajaxstatus' role='status' class='jsonly'></div>\n";restart_session();page_messages();$g=&get_session("dbs");if(DB!=""&&$g&&!in_array(DB,$g,true))$g=null;stop_session();define("AdminNeo\PAGE_HEADER",1);ob_flush();flush();}function
validate_color_variant($Hb){list(,$Hb)=validate_theme("default",$Hb);return$Hb;}function
validate_theme($Ul,$Hb){$Vl=get_available_themes();if(isset($Vl[$Ul][$Hb]))return[$Ul,$Hb];if(isset($Vl["default"][$Hb]))return["default",$Hb];reset($Vl["default"]);return["default",key($Vl["default"])];}function
get_available_themes(){return
array('default'=>array('blue'=>true,'green'=>true,'orange'=>true,'purple'=>true,'red'=>true,),'dune'=>array('blue'=>true,'green'=>true,'orange'=>true,'purple'=>true,'red'=>true,),);}function
get_theme_titles($Hb){$Vl=get_available_themes();$cm=[];foreach($Vl
as$Ul=>$Ib){if(isset($Ib[$Hb])?$Ib[$Hb]:false)$cm[$Ul]=($Ul=="default"?"Neo":ucwords(str_replace("-"," ",$Ul)));}return$cm;}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");header("X-Frame-Options: DENY");$nc=["script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self' https://api.github.com/repos/adminneo-org/adminneo/releases/latest","frame-src"=>"'self'","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",];Admin::get()->updateCspHeader($nc);$Mc=[];foreach($nc
as$Lc=>$Vk)$Mc[]="$Lc $Vk";header("Content-Security-Policy: ".implode("; ",$Mc));Admin::get()->sendHeaders();}function
get_nonce(){static$Eh;if(!$Eh)$Eh=Random::strongKey();return$Eh;}function
page_messages(){$Em=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$bh=isset($_SESSION["messages"][$Em])?$_SESSION["messages"][$Em]:null;if($bh){foreach($bh
as$Xg)echo"<div class='message'>$Xg</div>\n",script("initToggles(qsl('.message'));");unset($_SESSION["messages"][$Em]);}foreach(Admin::get()->getErrors()as$j)echo"<div class='error'>$j</div>\n";}function
page_footer($hh=null){echo"</div>\n","</div>\n","<div id='navigation-panel' class='navigation-panel'>\n","<div class='focus-trap-begin'></div>\n";$kg=isset($_COOKIE["neo_version"])?$_COOKIE["neo_version"]:null;echo"<div class='header'>\n","<button id='close-navigation-button' type='button' class='button light navigation-button' title='",lang(140),"' aria-controls='navigation-panel'>",icon_solo("close"),"</button>",Admin::get()->getServiceTitle()."\n";if($hh!="auth"){echo"<span class='version'>",h(preg_replace('~\\.0(-|$)~','$1',VERSION));if(Admin::get()->getConfig()->isVersionVerificationEnabled()&&$kg&&version_compare(VERSION,$kg)<0)echo"<a id='version' class='version-badge' href='https://www.adminneo.org/download' ".target_blank()." title='".h($kg)."'>",icon_solo("asterisk"),"</a>";echo"</span>\n";if(Admin::get()->getConfig()->isVersionVerificationEnabled()&&!$kg)echo
script("verifyVersion();");}echo"</div>\n";Admin::get()->printNavigation($hh);echo"<div class='footer'>\n","<div class='toolbox'>";if($hh=="auth")language_select();else{$x=h(preg_replace('~\b(db|ns)=[^&]*&~',"",ME)."settings=");echo"<a class='button light' title='",lang(141),"' href='$x'>",icon_solo("settings"),"</a>";}echo"</div>";if($hh!="auth")Admin::get()->printLogout();echo"</div>\n","<div id='navigation-resizer' class='navigation-resizer'></div>\n","<div class='focus-trap-end'></div>\n","</div>\n",script("initNavigation(); initNavigationResizer('".js_escape(ME)."set=navigation-width', '".get_token()."', ".Settings::$NavigationWidthMin.", ".Settings::$NavigationWidthMax.");");}function
int32($rh){while($rh>=2147483648)$rh-=4294967296;while($rh<=-2147483649)$rh+=4294967296;return(int)$rh;}function
long2str(array$V,$cn){$bk='';foreach($V
as$W)$bk
.=pack('V',$W);return$cn?substr($bk,0,end($V)):$bk;}function
str2long($bk,$cn){$V=array_values(unpack('V*',str_pad($bk,4*ceil(strlen($bk)/4),"\0")));if($cn)$V[]=strlen($bk);return$V;}function
xxtea_mx($on,$nn,$nl,$Sf){return
int32((($on>>5&0x7FFFFFF)^$nn<<2)+(($nn>>3&0x1FFFFFFF)^$on<<4))^int32(($nl^$nn)+($Sf^$on));}function
xxtea_encrypt_string($Xi,$u){$u=array_values(unpack("V*",pack("H*",md5($u))));$V=str2long($Xi,true);$rh=count($V)-1;$on=$V[$rh];$nn=$V[0];$uj=floor(6+52/($rh+1));$nl=0;while($uj-->0){$nl=int32($nl+0x9E3779B9);$hd=$nl>>2&3;for($Ai=0;$Ai<$rh;$Ai++){$nn=$V[$Ai+1];$ph=xxtea_mx($on,$nn,$nl,$u[$Ai&3^$hd]);$on=int32($V[$Ai]+$ph);$V[$Ai]=$on;}$nn=$V[0];$ph=xxtea_mx($on,$nn,$nl,$u[$Ai&3^$hd]);$on=int32($V[$rh]+$ph);$V[$rh]=$on;}return
long2str($V,false);}function
xxtea_decrypt_string($f,$u){$u=array_values(unpack("V*",pack("H*",md5($u))));$V=str2long($f,false);$rh=count($V)-1;$on=$V[$rh];$nn=$V[0];$uj=floor(6+52/($rh+1));$nl=int32($uj*0x9E3779B9);while($nl){$hd=$nl>>2&3;for($Ai=$rh;$Ai>0;$Ai--){$on=$V[$Ai-1];$ph=xxtea_mx($on,$nn,$nl,$u[$Ai&3^$hd]);$nn=int32($V[$Ai]-$ph);$V[$Ai]=$nn;}$on=$V[$rh];$ph=xxtea_mx($on,$nn,$nl,$u[$Ai&3^$hd]);$nn=int32($V[0]-$ph);$V[0]=$nn;$nl=int32($nl-0x9E3779B9);}return
long2str($V,true);}const
ENCRYPTION_GCM='aes-256-gcm';const
ENCRYPTION_CBC='aes-256-cbc';const
ENCRYPTION_TAG_LENGTH=16;const
ENCRYPTION_HMAC_LENGTH=64;function
generate_iv($v){if(function_exists('random_bytes')){try{return
random_bytes($v);}catch(Exception$hd){}}return
openssl_random_pseudo_bytes($v);}function
hash_key($u){return
substr(hash('sha512',$u,true),0,32);}function
aes_encrypt_string($Xi,$u){$fh=PHP_VERSION_ID>=70100&&in_array(ENCRYPTION_GCM,openssl_get_cipher_methods())?ENCRYPTION_GCM:ENCRYPTION_CBC;$u=hash_key($u);$Nf=generate_iv(openssl_cipher_iv_length($fh)?:16);if($fh==ENCRYPTION_GCM)$_b=openssl_encrypt($Xi,$fh,$u,OPENSSL_RAW_DATA,$Nf,$Ll,"",ENCRYPTION_TAG_LENGTH);else{$_b=openssl_encrypt($Xi,$fh,$u,OPENSSL_RAW_DATA,$Nf);$Ll=hash_hmac("sha512",$Nf.$_b,$u,true);}if($_b===false)return
false;return$Nf.$Ll.$_b;}function
aes_decrypt_string($f,$u){$fh=PHP_VERSION_ID>=70100&&in_array(ENCRYPTION_GCM,openssl_get_cipher_methods())?ENCRYPTION_GCM:ENCRYPTION_CBC;$Of=openssl_cipher_iv_length($fh)?:16;$Ml=$fh==ENCRYPTION_GCM?ENCRYPTION_TAG_LENGTH:ENCRYPTION_HMAC_LENGTH;if(strlen($f)<$Of+$Ml)return
false;$u=hash_key($u);$Nf=substr($f,0,$Of);$Ll=substr($f,$Of,$Ml);$_b=substr($f,$Of+$Ml);if($Nf===false||$Ll===false||$_b===false)return
false;if($fh==ENCRYPTION_GCM)return
openssl_decrypt($_b,$fh,$u,OPENSSL_RAW_DATA,$Nf,$Ll);else{$Ye=hash_hmac('sha512',$Nf.$_b,$u,true);if(!hash_equals($Ll,$Ye))return
false;return
openssl_decrypt($_b,$fh,$u,OPENSSL_RAW_DATA,$Nf);}}function
encrypt_string($Xi,$u){if($Xi=="")return"";if(extension_loaded('openssl'))return
aes_encrypt_string($Xi,$u);else
return
xxtea_encrypt_string($Xi,$u);}function
decrypt_string($f,$u){if($f=="")return"";if(extension_loaded('openssl'))return
aes_decrypt_string($f,$u);else
return
xxtea_decrypt_string($f,$u);}$Ui=[];if($_COOKIE["neo_permanent"]){foreach(explode(" ",$_COOKIE["neo_permanent"])as$W){list($u)=explode(":",$W);$Ui[$u]=$W;}}function
validate_server_input(array&$Ui){$M=preg_replace('~:/[-\w.][-\w.:/]*$~D',"",SERVER);if($M=="")return;if(!preg_match('~^[^:]+://~',$M))$M="https://$M";$Oi=parse_url($M);if(!$Oi)auth_error($Ui);if(isset($Oi['user'])||isset($Oi['pass'])||isset($Oi['query'])||isset($Oi['fragment']))auth_error($Ui);if(isset($Oi['scheme'])&&!preg_match('~^(https?)$~i',$Oi['scheme']))auth_error($Ui);$bf=$Oi['host'].(isset($Oi['path'])?$Oi['path']:'');if(!is_server_host_valid($bf))auth_error($Ui);if(isset($Oi['port'])&&($Oi['port']<1024||$Oi['port']>65535))auth_error($Ui,lang(142));}if(!function_exists('AdminNeo\is_server_host_valid')){function
is_server_host_valid($bf){return
strpos($bf,'/')===false;}}function
build_http_url($M,$U,$E,$Bc,$Ac=null){if(!preg_match('~^(https?://)?([^:]*)(:\d+)?$~',rtrim($M,'/'),$_))return
null;return($_[1]?:"http://").($U!==""||$E!==""?urlencode($U).":".urlencode($E)."@":"").($_[2]!==""?$_[2]:$Bc).(isset($_[3])?$_[3]:($Ac?":$Ac":""));}function
add_invalid_login(){$Za=get_temp_dir()."/adminneo-invalid";$m=null;foreach(glob("$Za*")?:[$Za]as$n){$m=open_file_with_lock($n);if($m)break;}if(!$m){$m=open_file_with_lock("$Za-".Random::strongKey());if(!$m)return;}$Df=json_decode(stream_get_contents($m),true);$Xl=time();if($Df){foreach($Df
as$Ef=>$W){if($W[0]<$Xl)unset($Df[$Ef]);}}$Cf=&$Df[Admin::get()->getBruteForceKey()];if(!$Cf)$Cf=[$Xl+30*60,0];$Cf[1]++;write_and_unlock_file($m,json_encode($Df));}function
check_invalid_login(array&$Ui){$Za=get_temp_dir()."/adminneo-invalid";$Df=[];foreach(glob("$Za*")as$n){$m=open_file_with_lock($n);if($m){$Df=json_decode(stream_get_contents($m),true);unlock_file($m);break;}}$Cf=($Df?$Df[Admin::get()->getBruteForceKey()]:[]);$Ch=($Cf&&$Cf[1]>29?$Cf[0]-time():0);if($Ch>0)auth_error($Ui,lang(143,ceil($Ch/60)));}function
connect_to_db(array&$Ui){if(Admin::get()->getConfig()->hasServers()&&!Admin::get()->getConfig()->getServer(SERVER))auth_error($Ui);$e=connect(true,$j);if(!$e)connection_error(nl2br(h($j)),$Ui);return$e;}function
authenticate(array&$Ui){$H=Admin::get()->authenticate($_GET["username"],get_password());if($H!==true)connection_error($H,$Ui);}function
connection_error($j,array&$Ui){$j=$j?:lang(3);if(preg_match('~^ +| +$~',get_password()))$j
.="<br>".lang(144);auth_error($Ui,$j);}Admin::get()->init();$Pa=isset($_POST["auth"])?$_POST["auth"]:null;if($Pa){session_regenerate_id();$M=isset($Pa["server"])?$Pa["server"]:"";$Ck=Admin::get()->getConfig()->getServer($M);$Yc=$Ck?$Ck->getDriver():(isset($Pa["driver"])?$Pa["driver"]:"");$M=$Ck?$M:trim($M);$U=isset($Pa["username"])?$Pa["username"]:"";$E=isset($Pa["password"])?$Pa["password"]:"";if($Ck&&$Ck->hasCredentials()&&$U==""&&$E==""){$U=$Ck->getUsername();$E=$Ck->getPassword();}$h=$Ck?$Ck->getDatabase():(isset($Pa["db"])?$Pa["db"]:"");save_login($Yc,$M,$U,$E,$h);if($Pa["permanent"]){$u=implode("-",array_map("base64_encode",[$Yc,$M,$U,$h]));$nj=Admin::get()->getPrivateKey(true);$td=$nj?encrypt_string($E,$nj):false;$Ui[$u]="$u:".base64_encode($td?:"");cookie("neo_permanent",implode(" ",$Ui));}if(count($_POST)==1||DRIVER!=$Yc||SERVER!=$M||$_GET["username"]!==$U||DB!=$h)redirect(auth_url($Yc,$M,$U,$h));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){foreach(["pwds","db","dbs","queries"]as$u)set_session($u,null);unset_permanent($Ui);redirect(SERVER_HOME_URL,lang(145));}elseif($Ui&&!$_SESSION["pwds"]){session_regenerate_id();$nj=Admin::get()->getPrivateKey();foreach($Ui
as$u=>$W){list(,$zb)=explode(":",$W);list($Yc,$M,$U,$h)=array_map("base64_decode",explode("-",$u));$E=$nj?decrypt_string(base64_decode($zb),$nj):false;save_login($Yc,$M,$U,$E,$h);}}function
unset_permanent(array&$Ui){foreach($Ui
as$u=>$W){list($Yc,$M,$U,$h)=array_map("base64_decode",explode("-",$u));if($Yc==DRIVER&&$M==SERVER&&$U==$_GET["username"]&&$h==DB)unset($Ui[$u]);}cookie("neo_permanent",implode(" ",$Ui));}function
auth_error(array&$Ui,$j=null){$Gk=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$Gk]||$_GET[$Gk])&&!$_SESSION["token"])$j=lang(146);else{restart_session();add_invalid_login();$E=get_password();if($E!==null){if($E===false)$j=lang(147);delete_login(DRIVER,SERVER,$_GET["username"]);}unset_permanent($Ui);}}if(!$_COOKIE[$Gk]&&$_GET[$Gk]&&ini_bool("session.use_only_cookies"))$j=lang(148);if(!$j)$j=lang(3);Admin::get()->addError($j);print_login_page();}function
print_login_page(){$Di=session_get_cookie_params();cookie("neo_key",($_COOKIE["neo_key"]?:Random::strongKey()),$Di["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header(lang(32),null);echo"<form action='' method='post'>\n","<div>";if(print_hidden_fields($_POST,["auth"]))echo"<p class='message'>".lang(149)."\n";echo"</div>\n";Admin::get()->printLoginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!DRIVER)print_login_page();if(isset($_GET["username"])&&!defined('AdminNeo\DRIVER_EXTENSION')){Admin::get()->addError(lang(150,implode(", ",Drivers::getExtensions(DRIVER))));unset($_SESSION["pwds"][DRIVER]);unset_permanent($Ui);page_header(lang(151),false);page_footer("auth");exit;}if(!isset($_GET["username"])||get_password()===null)print_login_page();validate_server_input($Ui);check_invalid_login($Ui);Admin::get()->getConfig()->applyServer(SERVER);$e=connect_to_db($Ui);authenticate($Ui);create_driver($e);if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){Admin::get()->addError(lang(152));page_header(lang(7));page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);if($Pa&&$_POST["token"])$_POST["token"]=get_token();if($_POST){if(!verify_token()){Admin::get()->addError(lang(152).' '.lang(153));$_POST=[];}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){$j=lang(154,"'post_max_size'");if(isset($_GET["sql"]))$j
.=' '.lang(155);Admin::get()->addError($j);}if(isset($_GET["settings"])){$N=Admin::get()->getSettings();$Jk=array_merge(Admin::get()->getSettingsRows(1),Admin::get()->getSettingsRows(2),Admin::get()->getSettingsRows(3));if($_POST){$Di=[];foreach($Jk
as$u=>$J){if(isset($_POST[$u])){$Hm=$_POST[$u]===""||(is_array($_POST[$u])&&in_array("",$_POST[$u]));$Di[$u]=(!$Hm?$_POST[$u]:null);}}$N->updateParameters($Di);redirect(remove_from_uri());}$S=lang(141);page_header($S,[$S]);echo"<form id='settings' action='' method='post'>\n","<table class='box'>\n";foreach($Jk
as$J)echo$J;echo"</table>\n","<p>","<input type='submit' value='".lang(117),"' class='button default hidden'>",input_token(),"</p>\n","</form>\n",script("initSettingsForm();");page_footer();exit;}if(isset($_GET["status"]))$_GET["variables"]=$_GET["status"];if(isset($_GET["import"]))$_GET["sql"]=$_GET["import"];if(DB==""&&isset($_GET["ns"]))redirect(remove_from_uri('ns'));if(!(DB!=""?Connection::get()->selectDatabase(DB):(isset($_GET["sql"])||isset($_GET["dump"])||isset($_GET["database"])||isset($_GET["processlist"])||isset($_GET["privileges"])||isset($_GET["user"])||isset($_GET["variables"])||$_GET["script"]=="connect"||$_GET["script"]=="kill"))){if(DB!=""||$_GET["refresh"]){restart_session();set_session("dbs",null);}if(DB!=""){Admin::get()->addError(lang(156));header("HTTP/1.1 404 Not Found");page_header(lang(31).": ".h(DB),true);}else{if($_POST["db"])queries_redirect(substr(ME,0,-1),lang(157),drop_databases($_POST["db"]));$S=h(Drivers::get(DRIVER).": ".Admin::get()->getServerName(SERVER));page_header($S,false);$yg=['privileges'=>[lang(73),"users"],'processlist'=>[lang(158),"list"],'variables'=>[lang(159),"variable"],'status'=>[lang(160),"status"],];$zg="";foreach($yg
as$u=>$W){if(support($u))$zg
.="<a href='".h(ME)."$u='>".icon($W[1])."$W[0]</a>";}if($zg)echo"<p class='links top-links'>$zg</p>\n";echo"<p>".lang(161,Drivers::get(DRIVER),"<b>".h(Connection::get()->getVersion())."</b>","<b>".DRIVER_EXTENSION."</b>")."\n","<p>".lang(162,"<b>".h(logged_user())."</b>")."\n";$g=Admin::get()->getDatabases();if($g){$lk=support("scheme");$Fa=collations();echo"<form action='' method='post'>\n","<div class='table-footer-parent'>\n","<div class='scrollable'>\n","<table class='checkable'>\n","<thead><tr>".(support("database")?"<th>":"")."<th aria-sort='ascending'>".lang(31).(get_session("dbs")!==null?" - <a href='".h(ME)."refresh=1'>".lang(163)."</a>":"")."<td>".lang(46)."<td>".lang(164)."<td>".lang(165)." - <a href='".h(ME)."dbsize=1'>".lang(166)."</a>".script("qsl('a').onclick = partial(ajaxSetHtml, '".js_escape(ME)."script=connect');","")."</thead>\n","<tbody>\n";$g=($_GET["dbsize"]?count_tables($g):array_flip($g));foreach($g
as$h=>$R){$Wj=h(ME)."db=".urlencode($h);$r=h("Db-".$h);echo"<tr>".(support("database")?"<th class='actions'>".checkbox("db[]",$h,in_array($h,(array)$_POST["db"]),"","","",$r):""),"<th><a href='$Wj' id='$r'>".h($h)."</a>";$Eb=h(db_collation($h,$Fa));echo"<td>".(support("database")?"<a href='$Wj".($lk?"&amp;ns=":"")."&amp;database=' title='".lang(70)."'>$Eb</a>":$Eb),"<td align='right'><a href='$Wj&amp;schema=' id='tables-".h($h)."' title='".lang(72)."'>".($_GET["dbsize"]?$R:"?")."</a>","<td align='right' id='size-".h($h)."'>".($_GET["dbsize"]?db_size($h):"?"),"\n";}echo"</tbody>\n",script("mixin(qsl('tbody'), {onclick: tableClick, ondblclick: event => tableClick(event, true)});"),"</table>\n","</div>\n";if(support("database"))echo"<div class='table-footer'><div class='field-sets'>\n","<fieldset><legend>",lang(167)," <span id='selected'></span></legend><div class='fieldset-content'>\n",input_hidden("all"),script("qsl('input').onclick = countDbs;"),"<input type='submit' class='button' name='drop' value='",lang(168),"'>",confirm(),"\n","</div></fieldset>\n","</div></div>\n",script("initTableFooter()");echo"</div>\n",input_token(),"</form>\n",script("tableCheck();");}}echo'<p class="links"><a href="'.h(ME).'database=">'.icon("database-add").lang(76)."</a>\n";page_footer("db");exit;}if(support("scheme")){if(DB!=""&&$_GET["ns"]!==""){if(!isset($_GET["ns"]))redirect(preg_replace('~(?<=[?&])db=[^&]+~','\\0&ns='.urlencode(get_schema()),relative_uri()));if(!set_schema($_GET["ns"])){Admin::get()->addError(lang(169));header("HTTP/1.1 404 Not Found");page_header(lang(82).": ".h($_GET["ns"]),true);page_footer("ns");exit;}}}if(isset($_GET["select"])&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["callf"]))$_GET["call"]=$_GET["callf"];if(isset($_GET["function"]))$_GET["procedure"]=$_GET["function"];if(isset($_GET["download"])){$a=$_GET["download"];$l=fields($a);header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$_GET["where"])).".".friendly_url($_GET["field"]));$L=[idf_escape($_GET["field"])];$H=Driver::get()->select($a,$L,[where($_GET,$l)],$L);$J=($H?$H->fetchRow():[]);echo
Connection::get()->formatValue($J[0],$l[$_GET["field"]]);exit;}elseif(isset($_GET["table"])){$a=$_GET["table"];$l=fields($a);if(!$l)Admin::get()->addError(error()?:lang(79));$Q=table_status1($a,true);$A=Admin::get()->getTableName($Q);$Vj=[];foreach($l
as$u=>$k)$Vj+=$k["privileges"];$S=$l&&is_view($Q)?$Q['Engine']=='materialized view'?lang(170):lang(171):lang(9);$Bl=$A!=""?$A:h($a);page_header("$S: $Bl",[$Bl]);$zf=null;if(isset($Vj["insert"])||!support("table"))$zf=[];Admin::get()->printTableMenu($Q,$zf);$rf=[];if(!preg_match("~sqlite|mssql|pgsql~",DIALECT)&&isset($Q["Engine"]))$rf[]=lang(172).": ".h($Q["Engine"]);if(isset($Q["Collation"]))$rf[]=lang(46).": ".h($Q["Collation"]);if($rf)echo"<p>",implode(", ",$rf),"</p>";if($l)Admin::get()->printTableStructure($l);$Ob=$Q["Comment"];if($Ob!="")echo"<p class='keep-lines'>",lang(47),": ",Admin::get()->formatComment($Ob),"</p>\n";if(!is_view($Q))$jd='<p class="links"><a href="'.h(ME).'create='.urlencode($a).'">'.icon("edit").lang(36)."</a>\n";elseif(support("view"))$jd='<p class="links"><a href="'.h(ME).'view='.urlencode($a).'">'.icon("edit").lang(37)."</a>\n";else$jd="";if($rf||$l||$Ob!="")echo$jd;$Ei=Driver::get()->getParentTables($a);if($Ei){echo"<h2>".lang(173)."</h2>\n";Admin::get()->printRelatedTables($Ei);}if(Driver::get()->getPartitionBy()&&str_contains(isset($Q["Create_options"])?$Q["Create_options"]:"","partitioned")){$Ni=Driver::get()->getPartitionsInfo($a);if($Ni){echo"<h2 id='partitions'>".lang(50)."</h2>\n";Admin::get()->printTablePartitions($Ni);if(DIALECT!="pgsql")echo$jd;}}$sf=Driver::get()->getInheritedTables($a);if($sf){echo"<h2 id='inherited-by'>".lang(174)."</h2>\n";Admin::get()->printRelatedTables($sf);}if(support("indexes")&&Driver::get()->supportsIndex($Q)){echo"<h2 id='indexes'>".lang(175)."</h2>\n";$t=indexes($a);if($t)Admin::get()->printTableIndexes($t,$Q);echo'<p class="links"><a href="'.h(ME).'indexes='.urlencode($a).'">'.icon("edit").lang(176)."</a>\n";}if(!is_view($Q)){if(fk_support($Q)){echo"<h2 id='foreign-keys'>".lang(93)."</h2>\n";$ne=foreign_keys($a);if($ne){echo"<table>\n","<thead><tr><th>".lang(177)."<td>".lang(178)."<td>".lang(96)."<td>".lang(95)."<td></thead>\n";foreach($ne
as$A=>$o)echo"<tr title='".h($A)."'>","<th><i>".implode("</i>, <i>",array_map('AdminNeo\h',$o["source"]))."</i>","<td><a href='".h($o["db"]!=""?preg_replace('~db=[^&]*~',"db=".urlencode($o["db"]),ME):($o["ns"]!=""?preg_replace('~ns=[^&]*~',"ns=".urlencode($o["ns"]),ME):ME))."table=".urlencode($o["table"])."'>".($o["db"]!=""&&$o["db"]!=DB?"<b>".h($o["db"])."</b>.":"").($o["ns"]!=""&&$o["ns"]!=$_GET["ns"]?"<b>".h($o["ns"])."</b>.":"").h($o["table"])."</a>","(<i>".implode("</i>, <i>",array_map('AdminNeo\h',$o["target"]))."</i>)","<td>".h($o["on_delete"]),"<td>".h($o["on_update"]),'<td><a href="'.h(ME.'foreign='.urlencode($a).'&name='.urlencode($A)).'">'.lang(179).'</a>',"\n";echo"</table>\n";}echo'<p class="links"><a href="'.h(ME).'foreign='.urlencode($a).'">'.icon("add").lang(180)."</a>\n";}if(support("check")){echo"<h2 id='checks'>".lang(181)."</h2>\n";$ub=Driver::get()->checkConstraints($a);if($ub){echo"<table cellspacing='0'>\n";foreach($ub
as$u=>$W)echo"<tr title='".h($u)."'>","<td><code class='jush-".DIALECT."'>".truncate_utf8(preg_replace('~\s+~',' ',ltrim($W))),"<td><a href='".h(ME.'check='.urlencode($a).'&name='.urlencode($u))."'>".lang(179)."</a>","\n";echo"</table>\n";}echo'<p class="links"><a href="'.h(ME).'check='.urlencode($a).'">'.icon("add").lang(182)."</a>\n";}}if(support(is_view($Q)?"view_trigger":"trigger")){echo"<h2 id='triggers'>".lang(183)."</h2>\n";$om=triggers($a);if($om){echo"<table>\n";foreach($om
as$u=>$W)echo"<tr><td>".h($W[0])."<td>".h($W[1])."<th>".h($u)."<td><a href='".h(ME.'trigger='.urlencode($a).'&name='.urlencode($u))."'>".lang(179)."</a>\n";echo"</table>\n";}echo'<p class="links"><a href="'.h(ME).'trigger='.urlencode($a).'">'.icon("add").lang(184)."</a>\n";}}elseif(isset($_GET["schema"])){$am=h(": ".DB.($_GET["ns"]?".$_GET[ns]":""));page_header(lang(72).$am,[lang(72)]);$Dl=[];$El=[];$Wd=[];$pa=($_GET["schema"]?:$_COOKIE["neo_schema-".str_replace(".","_",DB)]);preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~',$pa,$_,PREG_SET_ORDER);foreach($_
as$z){$Dl[$z[1]]=[(float)$z[2],(float)$z[3]];$El[]="\n'".js_escape($z[1])."': [ $z[2], $z[3] ]";}$vg=1.4;$gm=0;$Ya=-1;$jk=[];$Ij=[];$ng=[];$Ga=Driver::get()->getAllFields();foreach(table_status('',true)as$P=>$Q){if(is_view($Q))continue;$F=0;$jk[$P]["fields"]=[];foreach(isset($Ga[$P])?$Ga[$P]:[]as$k){$F=round($F+$vg,2);$Wd[$P][$k["field"]]=$F;$jk[$P]["fields"][$k["field"]]=$k;}$jk[$P]["pos"]=(isset($Dl[$P])?$Dl[$P]:[$gm,0]);foreach(Admin::get()->getForeignKeys($P)as$W){if(!$W["db"]){$lg=$Ya;if((isset($Dl[$P][1])?$Dl[$P][1]:0)||(isset($Dl[$W["table"]][1])?$Dl[$W["table"]][1]:0))$lg=min(floatval(isset($Dl[$P][1])?$Dl[$P][1]:0),floatval(isset($Dl[$W["table"]][1])?$Dl[$W["table"]][1]:0))-1;else$Ya-=.1;while($ng[(string)$lg])$lg-=.0001;$jk[$P]["references"][$W["table"]][(string)$lg]=[$W["source"],$W["target"]];$Ij[$W["table"]][$P][(string)$lg]=$W["target"];$ng[(string)$lg]=true;}}$gm=max($gm,round($jk[$P]["pos"][0]+$vg+$F+1,2));}echo"<div id='schema' style='height: {$gm}em;'>\n";foreach($jk
as$A=>$P){$Se=round($vg+count($P["fields"])*$vg+0.4,2);echo"<div class='table' style='top: ".$P["pos"][0]."em; left: ".$P["pos"][1]."em; height: {$Se}em;'>","<div class='content'>",'<h4><a href="'.h(ME).'table='.urlencode($A).'">'.h($A)."</a></h4>","<ul>";foreach($P["fields"]as$k){$W='<span '.type_class($k["type"]).' title="'.h($k["type"].($k["length"]?"($k[length])":"").($k["null"]?" NULL":'')).'">'.h($k["field"]).'</span>';echo"<li>".($k["primary"]?"<i>$W</i>":$W)."</li>";}echo"</ul>","</div>";foreach((array)$P["references"]as$Ol=>$Kj){foreach($Kj
as$lg=>$Ej){$mg=$lg-(isset($Dl[$A][1])?$Dl[$A][1]:0);$q=0;foreach($Ej[0]as$Uk){echo"\n<div class='references outgoing' title='",h($Ol),"' id='refs$lg-$q' style='left: {$mg}em; top: ",$Wd[$A][$Uk],"em;'>","<div style='width: ".(-$mg)."em;'></div>","</div>";$q++;}}}foreach((array)$Ij[$A]as$Ol=>$Kj){foreach($Kj
as$lg=>$c){$mg=$lg-(isset($Dl[$A][1])?$Dl[$A][1]:0);$q=0;foreach($c
as$Nl){echo"\n<div class='references incoming' title='",h($Ol),"' id='refd$lg-$q' style='left: {$mg}em; top: ".$Wd[$A][$Nl]."em;'>","<svg viewBox='0 0 22 22' fill='currentColor'><path d='M11,19l10,-8l-10,-8l0,16Z'/></svg>","<div style='width: ".(-$mg)."em;'></div>","</div>";$q++;}}}echo"\n</div>\n";}foreach($jk
as$A=>$P){foreach((array)$P["references"]as$Ol=>$Kj){if($jk[$Ol]){foreach($Kj
as$lg=>$Ej){$gh=$gm;$Pg=-10;foreach($Ej[0]as$u=>$Uk){$dj=$P["pos"][0]+$Wd[$A][$Uk];$ej=$jk[$Ol]["pos"][0]+$Wd[$Ol][$Ej[1][$u]];$gh=round(min($gh,$dj,$ej),2);$Pg=round(max($Pg,$dj,$ej),2);}echo"<div class='references vertical' id='refl$lg' style='left: $lg"."em; top: $gh"."em;'>"."<div style='height: ".round($Pg-$gh,2)."em;'></div></div>\n";}}}}echo"</div>\n",script("initSchema('".js_escape(DB)."', $gm, {".implode(",",$El)."})"),"<p class='links'>","<a href='",(ME."schema=".urlencode($pa)),"' id='schema-link'>",lang(185),"</a>","</p>\n";}elseif(isset($_GET["dump"])){$a=$_GET["dump"];$N=Admin::get()->getSettings();if($_POST){$N->updateParameters(["dumpFormat"=>$_POST["format"],"dumpDbStyle"=>$_POST["db_style"],"dumpTypes"=>isset($_POST["types"])?$_POST["types"]:(support("type")?"":null),"dumpRoutines"=>isset($_POST["routines"])?$_POST["routines"]:(support("routine")?"":null),"dumpEvents"=>isset($_POST["events"])?$_POST["events"]:(support("event")?"":null),"dumpTableStyle"=>$_POST["table_style"],"dumpAutoIncrement"=>isset($_POST["auto_increment"])?$_POST["auto_increment"]:"","dumpTriggers"=>isset($_POST["triggers"])?$_POST["triggers"]:(support("trigger")?"":null),"dumpDataStyle"=>$_POST["data_style"],"dumpOutput"=>$_POST["output"],]);if(DB!="")$g=[DB];else{$g=isset($_POST["databases"])?$_POST["databases"]:[];if(is_string($g))$g=explode("\n",rtrim(str_replace("\r","",$g),"\n"));}$kk=isset($_POST["schemas"])?$_POST["schemas"]:[];$R=array_flip(isset($_POST["tables"])?$_POST["tables"]:[])+array_flip(isset($_POST["data"])?$_POST["data"]:[]);if(count($R)==1)$ff=key($R);elseif(count($kk)==1)$ff=$kk[0];elseif(count($g)==1)$ff=$g[0];else$ff=Admin::get()->getServerName(SERVER,true,"server");$Kd=dump_headers($ff,DB==""||$_GET["ns"]===""||count($R)>1);$Kf=preg_match('~sql~',$_POST["format"]);$tc=$Kf&&$_POST["data_style"]&&!$_POST["table_style"]&&DIALECT!="sql";if($Kf){echo"-- AdminNeo ".VERSION." ".Drivers::get(DRIVER)." ".Connection::get()->getVersion()." dump\n\n";if(DIALECT=="sql"){echo"SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
".($_POST["data_style"]?"SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
":"")."
";Connection::get()->query("SET time_zone = '+00:00'");Connection::get()->query("SET sql_mode = ''");}}$jl=$_POST["db_style"];foreach($g
as$h){Admin::get()->dumpDatabase($h);if(Connection::get()->selectDatabase($h)){if($Kf){if($jl)echo
create_database_sql($h,$jl),use_sql($h,$jl)."\n";$yi="";if($_POST["types"]){foreach(types()as$r=>$T){$xd=type_values($r);if($xd)$yi
.=($jl!='DROP+CREATE'?"DROP TYPE IF EXISTS ".idf_escape($T).";;\n":"")."CREATE TYPE ".idf_escape($T)." AS ENUM ($xd);\n\n";else$yi
.="-- Could not export type $T\n\n";}}if($_POST["routines"]){foreach(routines()as$J){$A=$J["ROUTINE_NAME"];$Xj=$J["ROUTINE_TYPE"];$ic=create_routine($Xj,["name"=>$A]+routine($J["SPECIFIC_NAME"],$Xj));set_utf8mb4($ic);$yi
.=($jl!='DROP+CREATE'?"DROP $Xj IF EXISTS ".idf_escape($A).";;\n":"")."$ic;\n\n";}}if($_POST["events"]){foreach(get_rows("SHOW EVENTS",null,"-- ")as$J){$ic=remove_definer(Connection::get()->getValue("SHOW CREATE EVENT ".idf_escape($J["Name"]),3));set_utf8mb4($ic);$yi
.=($jl!='DROP+CREATE'?"DROP EVENT IF EXISTS ".idf_escape($J["Name"]).";;\n":"")."$ic;;\n\n";}}echo($yi&&DIALECT=='sql'?"DELIMITER ;;\n\n$yi"."DELIMITER ;\n\n":$yi);}if($_POST["table_style"]||$_POST["data_style"]){foreach(($_GET["ns"]===""?(array)$_POST["schemas"]:(DB!=""||!support("scheme")?[""]:Admin::get()->getSchemas(true)))as$jk){if($jk!="")set_schema($jk);$Jl=table_status('',true);$Cl=array_keys($Jl);$Nc=false;if($tc&&$Cl){$Jj=[];foreach($Cl
as$A){if(!is_view($Jl[$A])&&(DB==""||$_GET["ns"]===""||in_array($A,(array)$_POST["data"]))){foreach(foreign_keys($A)as$o)$Jj[$A][]=$o["table"];}}$ni=dump_table_order($Cl,$Jj);if($ni)$Cl=$ni;else$Nc=function_exists('AdminNeo\foreign_key_checks_sql');}if($Nc)echo
foreign_key_checks_sql(false)."\n";$Xm=[];foreach($Cl
as$A){$Q=$Jl[$A];$P=(DB==""||$_GET["ns"]===""||in_array($A,(array)$_POST["tables"]));$f=(DB==""||$_GET["ns"]===""||in_array($A,(array)$_POST["data"]));if($P||$f){$dm=null;if($Kd=="tar"){$dm=new
TmpFile();ob_start([$dm,'write'],1e5);}$jc=($P?$_POST["table_style"]:"");Admin::get()->dumpTable($A,$jc,(is_view($Q)?2:0));if(is_view($Q)&&$Kd!="tar")$Xm[]=$A;elseif($f){$l=fields($A);Admin::get()->dumpData($A,$_POST["data_style"],"SELECT *".convert_fields($l,$l)." FROM ".table($A));if($Kf&&!$jc&&$_POST["auto_increment"]&&function_exists('AdminNeo\restart_sequences_sql'))echo"\n".restart_sequences_sql($A);}if($Kf&&$_POST["triggers"]&&$P&&($om=trigger_sql($A)))echo"\nDELIMITER ;;\n$om\nDELIMITER ;\n";if($Kd=="tar"){ob_end_flush();tar_file((DB!=""?"":"$h/")."$A.csv",$dm);}elseif($Kf)echo"\n";}}if($Nc)echo
foreign_key_checks_sql(true)."\n";if($_POST["table_style"]&&function_exists('AdminNeo\foreign_keys_sql')){foreach($Jl
as$A=>$Q){$P=(DB==""||$_GET["ns"]===""||in_array($A,(array)$_POST["tables"]));if($P&&!is_view($Q))echo
foreign_keys_sql($A);}}foreach($Xm
as$Vm)Admin::get()->dumpTable($Vm,$_POST["table_style"],1);if($Kd=="tar")echo
pack("x512");}}}}if($Kf)echo"-- ".gmdate("Y-m-d H:i:s e")."\n";exit;}$A=DB!=""?h(DB):h(Admin::get()->getServerName(SERVER));page_header(lang(75).": $A",($_GET["export"]!=""?["table"=>$_GET["export"]]:[lang(75)]));echo"<form action='' method='post'>\n","<table class='box'>\n";$xc=['','USE','DROP+CREATE','CREATE'];$Gl=['','DROP+CREATE','CREATE'];$uc=['','TRUNCATE+INSERT','INSERT'];if(DIALECT=="sql")$uc[]='INSERT+UPDATE';echo"<tr><th>",lang(186),"</th><td>",html_radios("format",Admin::get()->getDumpFormats(),$N->getParameter("dumpFormat","sql")),"</td></tr>\n";if(DIALECT!="sqlite"){echo"<tr><th id='label-db'>",lang(31),"</th>","<td>",html_select('db_style',$xc,$N->getParameter("dumpDbStyle",DB==""?"CREATE":""),"","label-db"),"<span class='labels'>";if(support("type"))echo
checkbox("types",1,$N->getParameter("dumpTypes"),lang(111));if(support("routine"))echo
checkbox("routines",1,$N->getParameter("dumpRoutines",$_GET["dump"]==""?"1":""),lang(187));if(support("event"))echo
checkbox("events",1,$N->getParameter("dumpEvents",$_GET["dump"]==""?"1":""),lang(188));echo"</span></td></tr>";}echo"<tr><th id='label-tables'>",lang(164),"</th><td>",html_select('table_style',$Gl,$N->getParameter("dumpTableStyle","DROP+CREATE"),"","label-tables")," <span class='labels'>",checkbox("auto_increment",1,$N->getParameter("dumpAutoIncrement"),lang(48));if(support("trigger"))echo
checkbox("triggers",1,$N->getParameter("dumpTriggers","1"),lang(183));echo"</span></td></tr>","<tr><th id='label-data'>",lang(189),"</th><td>",html_select("data_style",$uc,$N->getParameter("dumpDataStyle","INSERT"),"","label-data"),"</td></tr>","<tr><th>",lang(190),"</th><td>",html_radios("output",Admin::get()->getDumpOutputs(),$N->getParameter("dumpOutput","file")),"</td></tr>\n","</table>\n","<p>","<input type='submit' class='button default' value='",lang(75),"'>",input_token(),"</p>\n","<table>\n",script("qsl('table').onclick = dumpClick;");$jj=[];if(DB!=""&&$_GET["ns"]===""){echo"<thead><tr><th>","<label class='block'><input type='checkbox' id='check-schemas' checked class='jsonly' title='".lang(191)."'>".lang(82)."</label>".script("gid('check-schemas').onclick = partial(formCheck, /^schemas\\[/);",""),"</thead>\n";foreach(Admin::get()->getSchemas()as$jk){if(!information_schema(DB,$jk))echo"<tr><td>".checkbox("schemas[]",$jk,true,$jk,"","block")."\n";}}elseif(DB!=""){$wb=($a!=""?"":" checked");echo"<thead><tr>","<th><label class='block'><input type='checkbox' id='check-tables'$wb class='jsonly' title='".lang(191)."'>".lang(9)."</label>".script("gid('check-tables').onclick = partial(formCheck, /^tables\\[/);",""),"<th class='right'><label class='block'>".lang(189)."<input type='checkbox' id='check-data'$wb class='jsonly' title='".lang(191)."'></label>".script("gid('check-data').onclick = partial(formCheck, /^data\\[/);",""),"</thead>\n";$Xm="";$Il=tables_list();foreach($Il
as$A=>$T){$ij=preg_replace('~_.*~','',$A);$wb=($a==""||$a==(substr($a,-1)=="%"?"$ij%":$A));$mj="<tr><td>".checkbox("tables[]",$A,$wb,$A,"","block");if($T!==null&&!preg_match('~table~i',$T))$Xm
.="$mj\n";else
echo"$mj<td class='right'><label class='block'><span id='Rows-".h($A)."'></span>".checkbox("data[]",$A,$wb)."</label>\n";$jj[$ij]++;}echo$Xm;if($Il)echo
script("ajaxSetHtml('".js_escape(ME)."script=db');");}else{$g=Admin::get()->getDatabases();echo"<thead><tr><th>","<label class='block'>".($g?"<input type='checkbox' id='check-databases'".($a==""?" checked":"")." class='jsonly' title='".lang(191)."'>".script("gid('check-databases').onclick = partial(formCheck, /^databases\\[/);",""):"").lang(31)."</label>","</thead>\n";if($g){foreach($g
as$h){if(!information_schema($h)){$ij=preg_replace('~_.*~','',$h);echo"<tr><td>".checkbox("databases[]",$h,$a==""||$a=="$ij%",$h,"","block")."\n";$jj[$ij]++;}}}else
echo"<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";}echo"</table>\n","</form>\n";$yg=[];foreach($jj
as$u=>$W){if($u!=""&&$W>1)$yg[]="<a href='".h(ME)."dump=".urlencode("$u%")."'>".icon("check").h($u)."*</a>";}if($yg)echo"<p class='links'>",implode("",$yg),"</p>\n";}elseif(isset($_GET["privileges"])){$am=DB!=""?h(": ".DB):"";page_header(lang(73).$am,[lang(73)]);echo'<p class="links top-links"><a href="',h(ME),'user=">',icon("user-add"),lang(192),"</a></p>\n";$H=Connection::get()->query("SELECT User, Host FROM mysql.".(DB==""?"user":"db WHERE ".q(DB)." LIKE Db")." ORDER BY Host, User");$Ce=$H;if(!$H)$H=Connection::get()->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");echo"<form action=''>\n";hidden_fields_get();echo
input_hidden("db",DB);if(!$Ce)echo
input_hidden("grant");echo"\n","<div class='scrollable'>\n","<table class='checkable'>\n","<thead><tr><th>".lang(6)."<th>".lang(5)."<th></thead>\n";while($J=$H->fetchAssoc())echo'<tr><td>'.h($J["User"])."<td>".h($J["Host"]).'<td><a href="'.h(ME.'user='.urlencode($J["User"]).'&host='.urlencode($J["Host"])).'">'.lang(39)."</a>\n";if(!$Ce||DB!="")echo"<tr><td><input class='input' name='user' autocapitalize='off'>"."<td><input class='input' name='host' value='localhost' autocapitalize='off'>"."<td><input type='submit' class='button' value='".lang(39)."'>\n";echo"</table>\n","</div>\n","</form>\n";}elseif(isset($_GET["sql"])){$N=Admin::get()->getSettings();if($_POST["export"]){$N->updateParameters(["exportFormat"=>$_POST["format"],"exportOutput"=>$_POST["output"],]);dump_headers("sql");Admin::get()->dumpTable("","");Admin::get()->dumpData("","table",$_POST["query"]);exit;}restart_session();$Xe=&get_session("queries");$We=&$Xe[DB];if($_POST["clear"]){$We=[];redirect(remove_from_uri("history"));}stop_session();$S=isset($_GET["import"])?lang(74):lang(41);page_header($S,[$S]);$ug="--".(DIALECT=="sql"?" ":"");if($_POST){$ue=false;if(!isset($_GET["import"]))$G=$_POST["query"];elseif($_POST["webfile"]){$kf=Admin::get()->getImportFilePath();if($kf){if(file_exists($kf))$ue=fopen($kf,"rb");elseif(file_exists("$kf.gz"))$ue=fopen("compress.zlib://$kf.gz","rb");}$G=$ue?fread($ue,1e6):false;}else$G=get_file("sql_file",true,";");if(is_string($G)){if(($Vg=ini_bytes("memory_limit"))!="-1")ini_set("memory_limit",max($Vg,strval(2*strlen($G)+memory_get_usage()+8e6)));if($G!=""&&strlen($G)<1e6){$uj=$G.(preg_match("~;[ \t\r\n]*\$~",$G)?"":";");if(!$We||first(end($We))!=$uj){restart_session();$We[]=[$uj,time()];set_session("queries",$Xe);stop_session();}}$Wk="(?:\\s|/\\*[\s\S]*?\\*/|(?:#|$ug)[^\n]*\n?|--\r?\n)";$Fc=";";$Gc=1;$Mh=0;$pd=true;$Yb=connect();if($Yb&&DB!=""){$Yb->selectDatabase(DB);if($_GET["ns"]!="")set_schema($_GET["ns"],$Yb);}$Nb=0;$zd=[];$Fi='[\'"'.(DIALECT=="sql"?'`#':(DIALECT=="sqlite"?'`[':(DIALECT=="mssql"?'[':''))).']|/\*|'.$ug.'|$'.(DIALECT=="pgsql"?'|\$([a-zA-Z]\w*)?\$':'');$hm=microtime(true);$gd=Admin::get()->getDumpFormats();unset($gd["sql"]);while($G!=""){if(!$Mh&&preg_match("~^$Wk*+DELIMITER\\s+(\\S+)~i",$G,$z)){$Fc=preg_quote($z[1]);$Gc=strlen($z[1]);$qe=Admin::get()->formatSqlCommandQuery(trim($z[0]));if($qe!="")echo"<pre><code class='jush-".DIALECT."'>$qe</code></pre>\n";$G=substr($G,strlen($z[0]));}elseif(!$Mh&&DIALECT=="pgsql"&&preg_match("~^($Wk*+COPY\\s+)[^;]+\\s+FROM\\s+stdin;~i",$G,$z)){$Fc="\n\\\\\\.\r?\n";$Gc=3;$Mh=strlen($z[0]);}else{preg_match("($Fc\\s*|$Fi)",$G,$z,PREG_OFFSET_CAPTURE,$Mh);list($se,$F)=$z[0];if(!$se&&$ue&&!feof($ue))$G
.=fread($ue,1e5);else{if(!$se&&rtrim($G)=="")break;$Mh=$F+strlen($se);if($se&&!preg_match("(^$Fc)",$se)){$lb=Driver::get()->hasCStyleEscapes()||(DIALECT=="pgsql"&&($F>0&&strtolower($G[$F-1])=="e"));$Si='(';if($se=='/*')$Si
.='\*/';elseif($se=='[')$Si
.=']';elseif(preg_match("~^$ug|^#~",$se))$Si
.="\n";else$Si
.=preg_quote($se).($lb?"|\\\\.":"");$Si
.='|$)s';while(preg_match($Si,$G,$z,PREG_OFFSET_CAPTURE,$Mh)){$bk=$z[0][0];if(!$bk&&$ue&&!feof($ue))$G
.=fread($ue,1e5);else{$Mh=$z[0][1]+strlen($bk);if(!isset($bk[0])||$bk[0]!="\\")break;}}}else{$pd=false;$uj=substr($G,0,$F+$Gc);$Nb++;$mj="<pre id='sql-$Nb'><code class='jush-".DIALECT."'>".Admin::get()->formatSqlCommandQuery(trim($uj))."</code></pre>\n";if(DIALECT=="sqlite"&&preg_match("~^$Wk*+(ATTACH|VACUUM\\b.*\\bINTO)\\b~is",$uj,$z)!==0){echo$mj,"<p class='error'>".lang(193,preg_match('~ATTACH~i',$z[1])?'ATTACH':'VACUUM INTO')."\n";$zd[]=" <a href='#sql-$Nb'>$Nb</a>";if($_POST["error_stops"])break;}else{if(!$_POST["only_errors"]){echo$mj;ob_flush();flush();}$cl=microtime(true);if(Connection::get()->multiQuery($uj)&&is_object($Yb)&&preg_match("~^$Wk*+USE\\b~i",$uj))$Yb->query($uj);do{$H=Connection::get()->storeResult();if(Connection::get()->getError()){echo($_POST["only_errors"]?$mj:""),"<p class='error'>",lang(194),(!empty(Connection::get()->getErrno())?" (".Connection::get()->getErrno().")":""),": ",error()."</p>\n";$zd[]=" <a href='#sql-$Nb'>$Nb</a>";if($_POST["error_stops"])break
2;}else{$Xl=" <span class='time'>(".format_time($cl).")</span>";$kd=(strlen($uj)<1000?" <a href='".h(ME)."sql=".urlencode(trim($uj))."'>".icon("edit").lang(39)."</a>":"");$yj=Connection::get()->getQueryInfo();$Aa=Connection::get()->getAffectedRows();$dn=($_POST["only_errors"]?null:Driver::get()->warnings());$fn="warnings-$Nb";$gn=$dn?"<a href='#$fn' class='toggle'>".lang(40).icon_chevron_down()."</a>":null;$Gd=$qi=null;$Hd="explain-$Nb";$Id=false;$Jd="export-$Nb";$w=0;if(is_object($H)){if(!$_POST["only_errors"])echo"<div class='table-result'>\n";$w=(int)$_POST["limit"];$qi=print_select_result($H,$Yb,[],$w);if(!$_POST["only_errors"]){echo"<p class='links'>";$Hh=$H->getRowsCount();echo($Hh?($w&&$Hh>$w?lang(195,$w):"").lang(196,$Hh):""),$Xl,$kd,$gn;if($Yb&&preg_match("~^($Wk|\\()*+SELECT\\b~i",$uj)&&($Gd=explain($Yb,$uj)))echo"<a href='#$Hd' class='toggle'>Explain".icon_chevron_down()."</a>";$Id=true;echo"<a href='#$Jd' class='toggle'>".lang(75).icon_chevron_down()."</a>","</p>\n";}}else{if(preg_match("~^$Wk*+(CREATE|DROP|ALTER)$Wk++(DATABASE|SCHEMA)\\b~i",$uj)){restart_session();set_session("dbs",null);stop_session();}if(!$_POST["only_errors"]){echo"<p class='message' title='".h($yj)."'>",lang(197,$Aa),"$Xl $kd";if($gn)echo", $gn";echo"</p>\n";}}if(!$_POST["only_errors"])echo
script("initToggles(qsl('p'));");if($dn)echo"<div id='$fn' class='hidden'>\n$dn</div>\n";if($Gd){echo"<div id='$Hd' class='hidden explain'>\n";print_select_result($Gd,$Yb,$qi);echo"</div>\n";}if($Id){echo"<form id='$Jd' action='' method='post' class='hidden'><p>\n",html_select("format",$gd,$N->getParameter("exportFormat")),html_select("output",Admin::get()->getDumpOutputs(),$N->getParameter("exportOutput"))." ",input_hidden("query",$uj),input_token()," <input type='submit' class='button' name='export' value='".lang(75)."'>";if(!$w)echo
script("qsl('input').onclick = function (event) { return sqlExport.call(this, event, '".js_escape(ME)."set=export-settings'); };","");echo"</p></form>\n";}if(is_object($H)&&!$_POST["only_errors"])echo"</div>\n";}$cl=microtime(true);}while(Connection::get()->nextResult());}$G=substr($G,$Mh);$Mh=0;}}}}if($pd)echo"<p class='message'>".lang(198)."\n";elseif($_POST["only_errors"]){$Ph=$Nb-count($zd);echo"<p class='".($Ph?"message":"error")."'>".lang(199,$Nb-count($zd))," <span class='time'>(".format_time($hm).")</span>\n";}elseif($zd&&$Nb>1)echo"<p class='error'>".lang(194).": ".implode("",$zd)."\n";}else
echo"<p class='error'>".upload_error($G)."\n";}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";if(!isset($_GET["import"])){$uj=$_GET["sql"];if($_POST)$uj=$_POST["query"];elseif($_GET["history"]=="all")$uj=$We;elseif($_GET["history"]!="")$uj=$We[$_GET["history"]][0];echo"<p>";textarea("query",$uj,20);echo
script(($_POST?"":"qs('textarea').focus();\n")."gid('form').onsubmit = partial(sqlSubmit, gid('form'), '".js_escape(remove_from_uri("sql|limit|error_stops|only_errors|history"))."');"),"</p>","<p><input type='submit' class='button default' value='".lang(200)."' title='Ctrl+Enter'>",lang(201).": <input type='number' name='limit' class='input size' value='".h($_POST?$_POST["limit"]:$_GET["limit"])."'>\n";}else{echo"<div class='field-sets'>\n","<fieldset><legend>".lang(202)."</legend><div class='fieldset-content'>";$Ke=(extension_loaded("zlib")?"[.gz]":"");if(ini_bool("file_uploads"))echo"SQL$Ke (&lt; ".ini_get("upload_max_filesize")."B): <input type='file' name='sql_file[]' multiple>","<input type='submit' class='button default' value='".lang(200)."'>",file_upload_form_script("form","sql_file[]");else
echo
lang(203);echo"</div></fieldset>\n";$kf=Admin::get()->getImportFilePath();if($kf)echo"<fieldset><legend>".lang(204)."</legend><div class='fieldset-content'>",lang(205,"<code>".h($kf)."$Ke</code>")," <input type='submit' class='button default' name='webfile' value='".lang(206)."'>","</div></fieldset>\n";echo"</div>\n","<p>";}echo
checkbox("error_stops",1,($_POST?$_POST["error_stops"]:(isset($_GET["error_stops"])?$_GET["error_stops"]:true)),lang(207)),checkbox("only_errors",1,($_POST?$_POST["only_errors"]:isset($_GET["import"])||$_GET["only_errors"]),lang(208)),input_token(),"</p>\n";if(!isset($_GET["import"]))Admin::get()->printAfterSqlCommand();if(!isset($_GET["import"])&&$We){echo"<div class='field-sets'>\n";print_fieldset_start("history",lang(209),"history",$_GET["history"]!="");for($W=end($We);$W;$W=prev($We)){$u=key($We);list($uj,$Xl,$od)=$W;echo" <pre><code class='jush-".DIALECT."'>",truncate_utf8(preg_replace('~\s+~',' ',ltrim(preg_replace("~^(#|$ug).*~m",'',$uj)))),"</code></pre>",'<p class="links">',"<a href='".h(ME."sql=&history=$u")."'>".icon("edit").lang(39)."</a>"," <span class='time' title='".@date('Y-m-d',$Xl)."'>".@date("H:i:s",$Xl).($od?" ($od)":"")."</span>","</p>";}echo"<p><input type='submit' class='button' name='clear' value='".lang(210)."'>\n","<a href='",h(ME."sql=&history=all")."' class='button light'>",icon("edit"),lang(211),"</a></p>\n";print_fieldset_end("history");echo"</div>\n";}echo"</form>\n";}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$l=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$l):""):where($_GET,$l));$Dm=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($l
as$A=>$k){if((!$Dm&&!isset($k["privileges"]["insert"]))||Admin::get()->getFieldName($k)=="")unset($l[$A]);}if($_POST&&!isset($_GET["select"])){$y=$_POST["referer"];if($_POST["insert"])$y=($Dm?null:$_SERVER["REQUEST_URI"]);elseif(!preg_match('~^.+&select=.+$~',$y))$y=ME."select=".urlencode($a);$t=indexes($a);$ym=unique_array(isset($_GET["where"])?$_GET["where"]:[],$t);$zj="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($y,lang(212),(bool)Driver::get()->delete($a,$zj,$ym?0:1));else{$Hk=[];foreach($l
as$A=>$k){$W=process_input($k);if($W!==false&&$W!==null)$Hk[idf_escape($A)]=$W;}if($Dm){if(!$Hk)redirect($y);queries_redirect($y,lang(213),(bool)Driver::get()->update($a,$Hk,$zj,$ym?0:1));if(is_ajax()){page_headers();page_messages();exit;}}else{$H=Driver::get()->insert($a,$Hk);$jg=($H?last_id($H):0);queries_redirect($y,lang(214,($jg?" $jg":"")),(bool)$H);}}}$J=null;if($Z){$L=[];foreach($l
as$A=>$k){if(isset($k["privileges"]["select"])){$Na=($_POST["clone"]&&$k["auto_increment"]?"''":convert_field($k));$L[]=($Na?"$Na AS ":"").idf_escape($A);}}$J=[];if(!support("table"))$L=["*"];if($L){$H=Driver::get()->select($a,$L,[$Z],$L,[],(isset($_GET["select"])?2:1));if(!$H)Admin::get()->addError(error());else{$J=$H->fetchAssoc();if(!$J)$J=false;}if(isset($_GET["select"])&&(!$J||$H->fetchAssoc()))$J=null;}}if(!support("table")&&!$l){if(!$Z){$H=Driver::get()->select($a,["*"],[],["*"]);$J=($H?$H->fetchAssoc():false);if(!$J)$J=[Driver::get()->primary=>""];}if($J){foreach($J
as$u=>$W){if(!$Z)$J[$u]=null;$l[$u]=["field"=>$u,"null"=>($u!=Driver::get()->primary),"auto_increment"=>($u==Driver::get()->primary)];}}}if(isset($_POST["save"])?$_POST["save"]:false){$fj=[];foreach((isset($_POST["fields"])?$_POST["fields"]:[])as$u=>$W)$fj[bracket_escape($u,true)]=$W;$J=$fj+($J?:[]);}if($_POST["edit"]){$md=array_filter($l,function($k){return!(isset($k["generated"])?$k["generated"]:null);});}else$md=$l;edit_form($a,$md,$J,$Dm);}elseif(isset($_GET["create"])){$a=$_GET["create"];$Ji=Driver::get()->getPartitionBy();$Ni=$Ji?Driver::get()->getPartitionsInfo($a):[];$Gj=referencable_primary($a);$ne=[];foreach($Gj
as$Bl=>$k)$ne[str_replace("`","``",$Bl)."`".str_replace("`","``",$k["field"])]=$Bl;$ti=[];$Q=[];if($a!=""){$ti=fields($a);$Q=table_status1($a);if(count($Q)<2)Admin::get()->addError(lang(79));}$J=$_POST;$J["Comment"]=normalize_newlines($J["Comment"]);$J["fields"]=(array)$J["fields"];if($J["auto_increment_col"])$J["fields"][$J["auto_increment_col"]]["auto_increment"]=true;if($_POST&&!Admin::get()->getErrors())Admin::get()->getSettings()->updateParameter("commentsOpened",isset($_POST["comments"])?$_POST["comments"]:null);if($_POST&&!process_fields($J["fields"])&&!Admin::get()->getErrors()){if($_POST["drop"])queries_redirect(substr(ME,0,-1),lang(215),drop_tables([$a]));else{$l=[];$Ga=[];$Im=false;$le=[];$si=reset($ti);$Ca=" FIRST";foreach($J["fields"]as$u=>$k){$o=$ne[$k["type"]];$sm=($o!==null?$Gj[$o]:$k);if($k["field"]!=""){if(!$k["generated"])$k["default"]=null;$sj=process_field($k,$sm);$Ga[]=[$k["orig"],$sj,$Ca];if(!$si||$sj!==process_field($si,$si)){$l[]=[$k["orig"],$sj,$Ca];if($k["orig"]!=""||$Ca)$Im=true;}if($o!==null)$le[idf_escape($k["field"])]=($a!=""&&DIALECT!="sqlite"?"ADD":" ").format_foreign_key(['table'=>$ne[$k["type"]],'source'=>[$k["field"]],'target'=>[$sm["field"]],'on_delete'=>$k["on_delete"],]);$Ca=" AFTER ".idf_escape($k["field"]);}elseif($k["orig"]!=""){$Im=true;$l[]=[$k["orig"]];}if($k["orig"]!=""){$si=next($ti);if(!$si)$Ca="";}}$Li=[];if(in_array($J["partition_by"],$Ji)){foreach($J
as$u=>$W){if(preg_match('~^partition~',$u))$Li[$u]=$W;}foreach($Li["partition_names"]as$u=>$A){if($A===""){unset($Li["partition_names"][$u]);unset($Li["partition_values"][$u]);}}$Li["partition_names"]=array_values($Li["partition_names"]);$Li["partition_values"]=array_values($Li["partition_values"]);if($Li==$Ni)$Li=[];}elseif(str_contains(isset($Q["Create_options"])?$Q["Create_options"]:"","partitioned"))$Li=null;$Xg=lang(216);if($a==""){cookie("neo_engine",isset($J["Engine"])?$J["Engine"]:"");$Xg=lang(217);}$A=trim($J["name"]);$y=ME.(support("table")?"table=":"select=").urlencode($A);$H=alter_table($a,$A,(DIALECT=="sqlite"&&($Im||$le)?$Ga:$l),$le,($J["Comment"]!=$Q["Comment"]?$J["Comment"]:null),($J["Engine"]&&$J["Engine"]!=$Q["Engine"]?$J["Engine"]:""),($J["Collation"]&&$J["Collation"]!=$Q["Collation"]?$J["Collation"]:""),($J["Auto_increment"]!=""?number($J["Auto_increment"]):""),$Li);if($H&&!Queries::$queries)redirect($y);queries_redirect($y,$Xg,$H);}}if($a!="")page_header(lang(36).": ".h($a),["table"=>$a,lang(36)]);else
page_header(lang(78),[lang(78)]);if(!$_POST){$um=Driver::get()->getTypes();$J=["Engine"=>$_COOKIE["neo_engine"],"fields"=>[["field"=>"","type"=>(isset($um["int"])?"int":(isset($um["integer"])?"integer":"")),"on_update"=>""]],"partition_names"=>[""],];if($a!=""){$J=$Q;$J["name"]=$a;$J["fields"]=[];if(!$_GET["auto_increment"])$J["Auto_increment"]="";foreach($ti
as$k){$k["generated"]=$k["generated"]?:(isset($k["default"])?"DEFAULT":"");$J["fields"][]=$k;}if($Ji){$J+=$Ni;$J["partition_names"][]="";$J["partition_values"][]="";}}}$Uf=[];if($J["Collation"])$Uf[$J["Collation"]]=true;foreach($J["fields"]as$k){if($k["collation"])$Uf[$k["collation"]]=true;}$Fb=Admin::get()->getCollations(array_keys($Uf));$vd=Driver::get()->engines();foreach($vd
as$ud){if(!strcasecmp($ud,$J["Engine"])){$J["Engine"]=$ud;break;}}$Kg=max_input_vars(12,20);if($Kg){$Te=(count($J["fields"])>$Kg?"":" hidden");echo"<p".($Te?" id='max-fields' data-columns='$Kg'":"")." class='error$Te'>".max_input_vars_error()."\n";}echo"<form action='' method='post' id='form'>\n";if(support("columns")||$a==""){echo"<p>",lang(218),": ","<input class='input' name='name' data-maxlength='64' value='",h($J["name"]),"' autocapitalize='off'",(($a==""&&!$_POST)?" autofocus":""),">";if($vd)echo" ",html_select("Engine",[""=>"(".lang(219).")"]+$vd,$J["Engine"]),help_script_command("value",true);if($Fb&&!preg_match("~sqlite|mssql~",DIALECT))echo" ",html_select("Collation",[""=>"(".lang(94).")"]+$Fb,$J["Collation"]);echo" <input type='submit' class='button default' value='",lang(117),"'>","</p>";}if(support("columns")&&($a==""||!Driver::get()->isPartition($a))){echo"<div class='scrollable'>\n","<table id='edit-fields' class='nowrap'>\n";edit_fields($J["fields"],$Fb,"TABLE",$ne);echo"</table>\n",script("initFieldsEditing(gid('edit-fields'));");if(support("move_col"))echo
script("initSortable('#edit-fields tbody');");echo"</div>\n","<p>",lang(48),": ","<input type='number' class='input size' name='Auto_increment' size='6' value='",h($J["Auto_increment"]),"'>";$Sb=$_POST?$_POST["comments"]:Admin::get()->getSettings()->getParameter("commentsOpened");$Pb=$Sb?"":"hidden";if(support("comment")){echo
checkbox("comments",1,$Sb,lang(47),"editingCommentsClick(this, ".(support("move_col")?7:6).");","jsonly")," ";if(preg_match('~\n~',$J["Comment"]))echo"<textarea name='Comment' rows='2' cols='20'",($Pb?" class='$Pb'":""),">",h($J["Comment"]),"</textarea>";else
echo"<input name='Comment' value='",h($J["Comment"]),"' data-maxlength='",(Connection::get()->isMinVersion("5.5")?2048:60),"' class='input $Pb'>";}echo"</p>\n<p>","<input type='submit' class='button default' value='",lang(117),"'>";}elseif($a!="")echo"<p>";if($a!="")echo"<input type='submit' class='button' name='drop' value='",lang(168),"'>",confirm(lang(220,$a)),"</p>\n";if($Ji&&(DIALECT=="sql"||$a=="")){echo"<div class='field-sets'>\n";$Ki=preg_match('~RANGE|LIST~',$J["partition_by"]);print_fieldset_start("partition",lang(221),"split",(bool)$J["partition_by"]);echo"<p>",html_select("partition_by",array_merge([""],$Ji),$J["partition_by"]),help_script_command("value.replace(/./, 'PARTITION BY \$&')",true),script("qsl('select').onchange = partitionByChange;"),"(<input class='input' name='partition' value='",h($J["partition"]),"'>) ",lang(50),": ","<input type='number' name='partitions' class='input size ",($Ki||!$J["partition_by"]?"hidden":""),"' value='",h($J["partitions"]),"'>","</p>\n","<table id='partition-table'",($Ki?"":" class='hidden'"),">\n","<thead><tr><th>",lang(222),"</th><th>",lang(52),"</th></tr></thead>\n";foreach($J["partition_names"]as$u=>$W){echo"<tr>","<td><input class='input' name='partition_names[]' value='",h($W),"' autocapitalize='off'>";if($u==count($J["partition_names"])-1)echo
script("qsl('input').oninput = partitionNameChange;");echo"</td>","<td><input class='input' name='partition_values[]' value='",h(isset($J["partition_values"][$u])?$J["partition_values"][$u]:""),"'></td>","</tr>\n";}echo"</table>\n","</p>\n";print_fieldset_end("partition");echo"</div>\n";}echo
input_token(),"</form>\n";}elseif(isset($_GET["indexes"])){$a=$_GET["indexes"];$qf=["PRIMARY","UNIQUE","INDEX"];$Q=table_status1($a,true);$of=Driver::get()->getIndexAlgorithms($Q);$e=Connection::get();$Gg=$e->isMariaDB();if(preg_match('~MyISAM|M?aria'.($e->isMinVersion($Gg?"10.0.5":"5.6")?'|InnoDB':'').'~i',$Q["Engine"]))$qf[]="FULLTEXT";if(preg_match('~MyISAM|M?aria'.($e->isMinVersion($Gg?"10.2.2":"5.7")?'|InnoDB':'').'~i',$Q["Engine"]))$qf[]="SPATIAL";if($Gg&&$e->isMinVersion("11.7")&&preg_match('~MyISAM|InnoDB~i',$Q["Engine"]))$qf[]="VECTOR";$t=indexes($a);$l=fields($a);$lj=[];if(DIALECT=="mongo"){$lj=$t["_id_"];unset($qf[0]);unset($t["_id_"]);}$J=$_POST;if($J){$N=Admin::get()->getSettings();if($N->getParameter("indexOptions")!==null)$N->updateParameter("indexOptions",null);}if($_POST&&!$_POST["add"]&&!$_POST["drop_col"]){$Ia=[];foreach($J["indexes"]as$s){$A=$s["name"];if(in_array($s["type"],$qf)){$c=[];$rg=[];$Jc=[];$ci=[];$nf=$of?(in_array($s["algorithm"],$of)?$s["algorithm"]:first($of)):"";$pf=(support("partial_indexes")?$s["partial"]:"");$Hk=[];ksort($s["columns"]);foreach($s["columns"]as$u=>$b){if($b!=""){$v=isset($s["lengths"][$u])?$s["lengths"][$u]:null;$Hc=isset($s["descs"][$u])?$s["descs"][$u]:null;$bi=isset($s["opclasses"][$u])?$s["opclasses"][$u]:null;$Hk[]=($l[$b]?idf_escape($b):$b).($v?"(".(+$v).")":"").($bi!=""?" ".idf_escape($bi):"").($Hc?" DESC":"");$c[]=$b;$rg[]=($v?:null);$Jc[]=$Hc;$ci[]="$bi";}}$Fd=$t[$A];if($Fd){ksort($Fd["columns"]);ksort($Fd["lengths"]);ksort($Fd["descs"]);if($s["type"]==$Fd["type"]&&array_values($Fd["columns"])===$c&&(!$Fd["lengths"]||array_values($Fd["lengths"])===$rg)&&array_values($Fd["descs"])===$Jc&&(!$Fd["opclasses"]||array_values($Fd["opclasses"])===$ci)&&(!$of||$Fd["algorithm"]===$nf)&&$Fd["partial"]==$pf){unset($t[$A]);continue;}}if($c)$Ia[]=[$s["type"],$A,$Hk,$nf,$pf];}}foreach($t
as$A=>$Fd)$Ia[]=[$Fd["type"],$A,"DROP"];if(!$Ia)redirect(ME."table=".urlencode($a));queries_redirect(ME."table=".urlencode($a),lang(223),alter_indexes($a,$Ia));}page_header(lang(176).": ".h($a),["table"=>$a,lang(176)]);$Yd=array_keys($l);if($_POST["add"]){foreach($J["indexes"]as$u=>$s){if($s["columns"][count($s["columns"])]!="")$J["indexes"][$u]["columns"][]="";}$s=end($J["indexes"]);if($s["type"]||array_filter($s["columns"],'strlen'))$J["indexes"][]=["columns"=>[1=>""]];}if(!$J){foreach($t
as$u=>$s){$t[$u]["name"]=$u;$t[$u]["columns"][]="";}$t[]=["columns"=>[1=>""]];$J["indexes"]=$t;}$rg=(DIALECT=="sql"||DIALECT=="mssql");$ci=Driver::get()->getIndexOpclasses();if($_POST)$Lk=$_POST["options"];else{$Lk=false;foreach($t
as$s){if(array_filter(isset($s["lengths"])?$s["lengths"]:[])||array_filter(isset($s["descs"])?$s["descs"]:[])||array_filter(isset($s["opclasses"])?$s["opclasses"]:[])||(isset($s["partial"])?$s["partial"]:"")!=""){$Lk=true;break;}}}echo"<form action='' method='post'>\n","<div class='scrollable'>\n","<table class='nowrap'>\n","<thead><tr>","<th id='label-type'>",lang(224),"</th>";$ii="class='idxopts".($Lk?"":" hidden")."'";if(count($of)>1)echo"<th id='label-method' $ii>",lang(225),doc_link(['sql'=>'create-index.html#create-index-storage-engine-index-types','mariadb'=>'ha-and-performance/optimization-and-tuning/optimization-and-indexes/storage-engine-index-types','pgsql'=>'indexes-types.html',]),"</th>";echo"<th><input type='submit' hidden>",lang(53).($rg?"<span $ii> (".lang(54).")</span>":"");if($rg||support("descidx"))echo
checkbox("options",1,$Lk,lang(100),"indexOptionsShow(this.checked)","jsonly")."\n";echo"</th>","<th id='label-name'>",lang(226),"</th>";if(support("partial_indexes"))echo"<th id='label-condition' $ii>",lang(55),"</th>";echo"<th>","<button name='add[0]' value='1' title='",lang(101),"' class='button light hidden'>",icon_solo("add"),"</button>","</th>","</tr></thead>\n";if($lj){echo"<tr><td>PRIMARY<td>";foreach($lj["columns"]as$b)echo
select_input(" disabled",array_combine($Yd,$Yd),$b),"<label><input type='checkbox' disabled>".lang(63)."</label> ";echo"<td><td>\n";}$Pf=1;foreach($J["indexes"]as$s){if(!$_POST["drop_col"]||$Pf!=key($_POST["drop_col"])){echo"<tr><td>",html_select("indexes[$Pf][type]",[-1=>""]+$qf,$s["type"],($Pf==count($J["indexes"])?"indexesAddRow.call(this);":""),"label-type"),"</td>";if(count($of)>1)echo"<td $ii>",html_select("indexes[$Pf][algorithm]",array_merge([""],$of),$s['algorithm'],"label-method"),"</td>";echo"<td>";ksort($s["columns"]);$q=1;foreach($s["columns"]as$u=>$b){echo"<span>".select_input(" name='indexes[$Pf][columns][$q]' title='".lang(44)."'",($l&&($b==""||$l[$b])?array_combine($Yd,$Yd):[]),$b,"partial(indexesChangeColumn, '".js_escape(DIALECT=="sql"?"":$_GET["indexes"]."_")."')"),"<span $ii>";if($rg)echo"<input type='number' name='indexes[$Pf][lengths][$q]' class='input size' value='".(h(isset($s["lengths"][$u])?$s["lengths"][$u]:"")),"' title='".lang(99),"'>";if($ci){$bi=isset($s["opclasses"][$u])?$s["opclasses"][$u]:"";echo
html_select("indexes[$Pf][opclasses][$q]",[""=>"(".lang(227).")"]+array_combine($ci,$ci)+($bi!=""?[$bi=>$bi]:[]),$bi),doc_link(['pgsql'=>'indexes-opclass.html']);}if(support("descidx"))echo
checkbox("indexes[$Pf][descs][$q]",1,isset($s["descs"][$u])?$s["descs"][$u]:false,lang(63));echo"<br></span></span>";$q++;}echo"</td>","<td><input name='indexes[$Pf][name]' value='",h($s["name"]),"' class='input' autocapitalize='off' aria-labelledby='label-name'></td>\n";if(support("partial_indexes"))echo"<td $ii><input name='indexes[$Pf][partial]' value='".h($s["partial"])."' autocapitalize='off' aria-labelledby='label-condition'>\n";echo"<td>","<button name='drop_col[$Pf]' value='1' title='",lang(59),"' class='button light'>",icon_solo("remove"),"</button>",script("qsl('button').onclick = onRemoveIndexRowClick;"),"</td>\n";}$Pf++;}echo"</table>\n","</div>\n","<p>","<input type='submit' class='button default' value='",lang(117),"'>",input_token(),"</p>\n","</form>\n";}elseif(isset($_GET["database"])){$J=$_POST;if($_POST&&!isset($_POST["add_x"])){$A=trim($J["name"]);if($_POST["drop"]){$_GET["db"]="";queries_redirect(remove_from_uri("db|database"),lang(228),drop_databases([DB]));}elseif(DB!==$A){if(DB!=""){$_GET["db"]=$A;queries_redirect(preg_replace('~\bdb=[^&]*&~','',ME)."db=".urlencode($A),lang(229),rename_database($A,$J["collation"]));}else{$g=explode("\n",str_replace("\r","",$A));$ll=true;$ig="";foreach($g
as$h){if(count($g)==1||$h!=""){if(!create_database($h,$J["collation"]))$ll=false;$ig=$h;}}restart_session();set_session("dbs",null);queries_redirect(ME."db=".urlencode($ig),lang(230),$ll);}}else{if(!$J["collation"])redirect(substr(ME,0,-1));query_redirect("ALTER DATABASE ".idf_escape($A).(preg_match('~^[a-z0-9_]+$~i',$J["collation"])?" COLLATE $J[collation]":""),substr(ME,0,-1),lang(231));}}if(DB!="")page_header(lang(70).": ".h(DB),[lang(70)]);else
page_header(lang(76),[lang(76)]);$A=DB;if($_POST)$A=$J["name"];elseif(DB!="")$J["collation"]=db_collation(DB,collations());elseif(DIALECT=="sql"){foreach(get_vals("SHOW GRANTS")as$Ce){if(preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~',$Ce,$z)&&$z[1]){$A=stripcslashes(idf_unescape("`$z[2]`"));break;}}}$Fb=Admin::get()->getCollations($J["collation"]?[$J["collation"]]:[]);echo"<form action='' method='post'>\n","<p>";if($_POST["add_x"]||strpos($A,"\n"))echo"<textarea id='name' name='name' rows='10' cols='40'>",h($A),"</textarea><br>\n";else
echo"<input class='input' name='name' id='name' value='",h($A),"' data-maxlength='64' autocapitalize='off' autofocus>\n";if($Fb)echo
html_select("collation",[""=>"(".lang(94).")"]+$Fb,$J["collation"]),doc_link(['sql'=>"charset-charsets.html",'mariadb'=>"reference/data-types/string-data-types/character-sets/supported-character-sets-and-collations",'mssql'=>"relational-databases/system-functions/sys-fn-helpcollations-transact-sql",]),"\n";echo"<input type='submit' class='button default' value='",lang(117),"'>\n";if(DB!="")echo"<input type='submit' class='button' name='drop' value='".lang(168)."'>".confirm(lang(220,DB))."\n";elseif(!$_POST["add_x"]&&$_GET["db"]=="")echo"<button name='add_x' value='1' title='",lang(101),"' class='button light'>",icon_solo("add"),"</button>\n";echo
input_token(),"</p>\n","</form>\n";}elseif(isset($_GET["scheme"])){$J=$_POST;if($_POST){$x=preg_replace('~ns=[^&]*&~','',ME)."ns=";if($_POST["drop"])query_redirect("DROP SCHEMA ".idf_escape($_GET["ns"]),$x,lang(232));else{$A=trim($J["name"]);$x
.=urlencode($A);if($_GET["ns"]=="")query_redirect("CREATE SCHEMA ".idf_escape($A),$x,lang(233));elseif($_GET["ns"]!=$A)query_redirect("ALTER SCHEMA ".idf_escape($_GET["ns"])." RENAME TO ".idf_escape($A),$x,lang(234));else
redirect($x);}}if($_GET["ns"]!="")page_header(lang(71).": ".h($_GET["ns"]),[lang(71)]);else
page_header(lang(77),[lang(77)]);if(!$J)$J["name"]=$_GET["ns"];echo"<form action='' method='post'>\n","<p>","<input class='input' name='name' id='name' value='",h($J["name"]),"' autocapitalize='off' autofocus>","<input type='submit' class='button default' value='",lang(117),"'>";if($_GET["ns"]!="")echo"<input type='submit' class='button' name='drop' value='".lang(168)."'>".confirm(lang(220,$_GET["ns"]))."\n";echo
input_token(),"</p>\n","</form>\n";}elseif(isset($_GET["call"])){$oa=$_GET["name"]?:$_GET["call"];page_header(lang(235).": ".h($oa),[lang(235)]);$Xj=routine($_GET["call"],(isset($_GET["callf"])?"FUNCTION":"PROCEDURE"));$lf=[];$yi=[];foreach($Xj["fields"]as$q=>$k){if(substr($k["inout"],-3)=="OUT"&&DIALECT=='sql')$yi[$q]="@".idf_escape($k["field"])." AS ".idf_escape($k["field"]);if(!$k["inout"]||substr($k["inout"],0,2)=="IN")$lf[]=$q;}if($_POST){$nb=[];foreach($Xj["fields"]as$u=>$k){$W="";if(in_array($u,$lf)){$W=process_input($k);if($W===false)$W="''";if(isset($yi[$u]))Connection::get()->query("SET @".idf_escape($k["field"])." = $W");}if(isset($yi[$u]))$nb[]="@".idf_escape($k["field"]);elseif(in_array($u,$lf))$nb[]=$W;}$G=(isset($_GET["callf"])?"SELECT ":"CALL ").($Xj["returns"]&&$Xj["returns"]["type"]=="record"?"* FROM ":"").table($oa)."(".implode(", ",$nb).")";$cl=microtime(true);$H=Connection::get()->multiQuery($G);$Aa=Connection::get()->getAffectedRows();echo
Admin::get()->formatSelectQuery($G,$cl,!$H);if(!$H)echo"<p class='error'>".error()."\n";else{$Yb=connect();if($Yb)$Yb->selectDatabase(DB);do{$H=Connection::get()->storeResult();if(is_object($H))print_select_result($H,$Yb);else
echo"<p class='message'>".lang(236,$Aa)." <span class='time'>".@date("H:i:s")."</span>\n";}while(Connection::get()->nextResult());if($yi)print_select_result(Connection::get()->query("SELECT ".implode(", ",$yi)));}}echo"<form action='' method='post'>\n";if($lf){echo"<table class='box'>\n";foreach($lf
as$u){$k=$Xj["fields"][$u];$A=$k["field"];echo"<tr><th>".Admin::get()->getFieldName($k);$X=isset($_POST["fields"][$A])?$_POST["fields"][$A]:"";if($X!=""){if($k["type"]=="set")$X=implode(",",$X);}input($k,$X,(string)(isset($_POST["function"][$A])?$_POST["function"][$A]:""));echo"\n";}echo"</table>\n";}echo"<p>\n","<input type='submit' class='button' value='",lang(235),"'>\n",input_token(),"</p>\n","</form>\n";$Ob=$Xj["comment"];if($Ob!==null&&$Ob!==""){$Ob=h(trim($Xj["comment"],"\n"));if(preg_match('~^ +~',$Ob,$_)){preg_match_all("~^($_[0]|$)~m",$Ob,$wg);if(count($wg[0])==substr_count($Ob,"\n"))$Ob=preg_replace("~^($_[0])~m","",$Ob);}$Ob=preg_replace('~(^|[^\n]\n)(Description|Parameters|Example)\n~',"$1\n<strong>$2</strong>\n",$Ob);echo"<pre class='comment'>$Ob</pre>\n";}}elseif(isset($_GET["foreign"])){$a=$_GET["foreign"];$A=$_GET["name"];$J=$_POST;if($_POST&&!$_POST["add"]&&!$_POST["change"]&&!$_POST["change-js"]){if(!$_POST["drop"]){$J["source"]=array_filter($J["source"],'strlen');ksort($J["source"]);$Nl=[];foreach($J["source"]as$u=>$W)$Nl[$u]=$J["target"][$u];$J["target"]=$Nl;}if(DIALECT=="sqlite")$H=recreate_table($a,$a,[],[],[" $A"=>($J["drop"]?"":" ".format_foreign_key($J))]);else{$Ia="ALTER TABLE ".table($a);$H=($A==""||queries("$Ia DROP ".(DIALECT=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($A)));if(!$J["drop"])$H=queries("$Ia ADD".format_foreign_key($J));}queries_redirect(ME."table=".urlencode($a),($J["drop"]?lang(237):($A!=""?lang(238):lang(239))),(bool)$H);if(!$J["drop"])Admin::get()->addError(lang(240));}if($A!="")page_header(lang(241).": ".h($A),["table"=>$a,lang(241)]);else
page_header(lang(180).": ".h($a),["table"=>$a,lang(180)]);if($_POST){ksort($J["source"]);if($_POST["change"]||$_POST["change-js"])$J["target"]=[];else$J["source"][]="";}elseif($A!=""){$ne=foreign_keys($a);$J=$ne[$A];$J["source"][]="";}else{$J["table"]=$a;$J["source"]=[""];}echo"<form action='' method='post'>\n";$Uk=array_keys(fields($a));if($J["db"]!="")Connection::get()->selectDatabase($J["db"]);if($J["ns"]!=""){$ui=get_schema();set_schema($J["ns"]);}$Fj=array_keys(array_filter(table_status('',true),'AdminNeo\fk_support'));$Nl=$Fj?array_keys(fields(in_array($J["table"],$Fj)?$J["table"]:reset($Fj))):[];$Xh="this.form['change-js'].value = '1'; this.form.submit();";echo"<p>","<span id='label-table'>",lang(242),":</span> ",html_select("table",$Fj,$J["table"],$Xh,"label-table");if(support("scheme")){$kk=array_filter(Admin::get()->getSchemas(),function($jk){return!information_schema(DB,$jk);});echo"<span id='label-schema'>",lang(82),":</span> ",html_select("ns",$kk,$J["ns"]!=""?$J["ns"]:$_GET["ns"],$Xh,"label-schema");if($J["ns"]!="")set_schema($ui);}elseif(DIALECT!="sqlite"){$yc=[];foreach(Admin::get()->getDatabases()as$h){if(!information_schema($h))$yc[]=$h;}echo"<span id='label-db'>",lang(243),":</span> ",html_select("db",$yc,$J["db"]!=""?$J["db"]:$_GET["db"],$Xh,"label-db");}echo
input_hidden("change-js"),"<noscript><input type='submit' class='button' name='change' value='",lang(244),"'></noscript>","</p>\n","<table>","<thead><tr><th id='label-source'>",lang(177),"<th id='label-target'>",lang(178),"</thead>\n";$Pf=0;foreach($J["source"]as$u=>$W){echo"<tr>","<td>".html_select("source[".(+$u)."]",[-1=>""]+$Uk,$W,($Pf==count($J["source"])-1?"foreignAddRow.call(this);":""),"label-source"),"<td>".html_select("target[".(+$u)."]",$Nl,isset($J["target"][$u])?$J["target"][$u]:null,"","label-target");$Pf++;}echo"</table>\n","<noscript><p><input type='submit' class='button' name='add' value='",lang(245),"'></p></noscript>","<p>\n","<span id='label-delete'>".lang(96),":</span> ",html_select("on_delete",[-1=>""]+Driver::get()->getOnActions(),$J["on_delete"],"","label-delete"),"<span id='label-update'>".lang(95),":</span> ",html_select("on_update",[-1=>""]+Driver::get()->getOnActions(),$J["on_update"],"","label-update");if(DRIVER=='pgsql')echo
html_select("deferrable",['NOT DEFERRABLE','DEFERRABLE','DEFERRABLE INITIALLY DEFERRED'],$J["deferrable"]);echo
doc_link(['sql'=>"innodb-foreign-key-constraints.html",'mariadb'=>"architecture/server-constraints/foreign-key-constraints",'pgsql'=>"sql-createtable.html#SQL-CREATETABLE-PARMS-REFERENCES",'mssql'=>"t-sql/statements/create-table-transact-sql",'oracle'=>"SQLRF01111",]),"</p>\n<p>","<input type='submit' class='button default' value='",lang(117),"'>";if($A!="")echo"<input type='submit' class='button' name='drop' value='",lang(168),"'>",confirm(lang(220,$A));echo
input_token(),"</p>\n","</form>\n";}elseif(isset($_GET["view"])){$a=$_GET["view"];$J=$_POST;$vi="VIEW";if(DIALECT=="pgsql"&&$a!=""){$el=table_status1($a);$vi=strtoupper($el["Engine"]);}if($_POST){$A=trim($J["name"]);$Na=" AS\n$J[select]";$y=ME."table=".urlencode($A);$Xg=lang(246);$T=($_POST["materialized"]?"MATERIALIZED VIEW":"VIEW");if(!$_POST["drop"]&&$a==$A&&DIALECT!="sqlite"&&$T=="VIEW"&&$vi=="VIEW")query_redirect((DIALECT=="mssql"?"ALTER":"CREATE OR REPLACE")." VIEW ".table($A).$Na,$y,$Xg);else{$Pl=$A."_adminneo_".uniqid();drop_create("DROP $vi ".table($a),"CREATE $T ".table($A).$Na,"DROP $T ".table($A),"CREATE $T ".table($Pl).$Na,"DROP $T ".table($Pl),($_POST["drop"]?substr(ME,0,-1):$y),lang(247),$Xg,lang(248),$a,$A);}}if(!$_POST&&$a!=""){$J=view($a);$J["name"]=$a;$J["materialized"]=($vi!="VIEW");if($j=error())Admin::get()->addError($j);}if($a!="")page_header(lang(37).": ".h($a),["table"=>$a,lang(37)]);else
page_header(lang(249),[lang(249)]);echo"<form action='' method='post'>\n","<p>",lang(226),":","<input class='input' name='name' value='",h($J["name"]),"' data-maxlength='64' autocapitalize='off'>\n";if(support("materializedview"))echo
checkbox("materialized",1,$J["materialized"],lang(170));echo"</p>\n<p>";textarea("select",$J["select"]);echo"</p>\n<p>","<input type='submit' class='button default' value='",lang(117),"'>\n";if($a!="")echo"<input type='submit' class='button' name='drop' value='",lang(168),"'>\n",confirm(lang(220,$a));echo
input_token(),"</p>\n","</form>\n";}elseif(isset($_GET["event"])){$ea=$_GET["event"];$Bf=["YEAR","QUARTER","MONTH","DAY","HOUR","MINUTE","WEEK","SECOND","YEAR_MONTH","DAY_HOUR","DAY_MINUTE","DAY_SECOND","HOUR_MINUTE","HOUR_SECOND","MINUTE_SECOND"];$fl=["ENABLED"=>"ENABLE","DISABLED"=>"DISABLE","SLAVESIDE_DISABLED"=>"DISABLE ON SLAVE"];$J=$_POST;if($_POST){if($_POST["drop"])query_redirect("DROP EVENT ".idf_escape($ea),substr(ME,0,-1),lang(250));elseif(in_array($J["INTERVAL_FIELD"],$Bf)&&isset($fl[$J["STATUS"]])){$ik="\nON SCHEDULE ".($J["INTERVAL_VALUE"]?"EVERY ".q($J["INTERVAL_VALUE"])." $J[INTERVAL_FIELD]".($J["STARTS"]?" STARTS ".q($J["STARTS"]):"").($J["ENDS"]?" ENDS ".q($J["ENDS"]):""):"AT ".q($J["STARTS"]))." ON COMPLETION".($J["ON_COMPLETION"]?"":" NOT")." PRESERVE";queries_redirect(substr(ME,0,-1),($ea!=""?lang(251):lang(252)),(bool)queries(($ea!=""?"ALTER EVENT ".idf_escape($ea).$ik.($ea!=$J["EVENT_NAME"]?"\nRENAME TO ".idf_escape($J["EVENT_NAME"]):""):"CREATE EVENT ".idf_escape($J["EVENT_NAME"]).$ik)."\n".$fl[$J["STATUS"]]." COMMENT ".q($J["EVENT_COMMENT"]).rtrim(" DO\n$J[EVENT_DEFINITION]",";").";"));}}if($ea!="")page_header(lang(253).": ".h($ea),[lang(253)]);else
page_header(lang(254),[lang(254)]);if(!$J&&$ea!=""){$K=get_rows("SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = ".q(DB)." AND EVENT_NAME = ".q($ea));$J=reset($K);}echo"<form action='' method='post'>\n","<table class='box box-light'>\n","<tr><th>",lang(226),"</th><td>","<input class='input' name='EVENT_NAME' value='",h($J["EVENT_NAME"]),"' data-maxlength='64' autocapitalize='off'>","</td></tr>\n","<tr><th title='datetime'>",lang(255),"</th><td>","<input class='input' name='STARTS' value='",h("$J[EXECUTE_AT]$J[STARTS]"),"'>","</td></tr>\n","<tr><th title='datetime'>",lang(256),"</th><td>","<input class='input' name='ENDS' value='",h($J["ENDS"]),"'>","</td></tr>\n","<tr><th>",lang(257),"</th><td>","<input type='number' name='INTERVAL_VALUE' value='",h($J["INTERVAL_VALUE"]),"' class='input size'> ",html_select("INTERVAL_FIELD",$Bf,$J["INTERVAL_FIELD"]),"</td></tr>\n","<tr><th>",lang(160),"</th><td>",html_select("STATUS",$fl,$J["STATUS"]),"</td></tr>\n","<tr><th>",lang(47),"</th><td>","<input class='input' name='EVENT_COMMENT' value='",h($J["EVENT_COMMENT"]),"' data-maxlength='64'>","</td></tr>\n","<tr><th></th><td>",checkbox("ON_COMPLETION","PRESERVE",$J["ON_COMPLETION"]=="PRESERVE",lang(258)),"</td></tr>\n","</table>\n","<p>";textarea("EVENT_DEFINITION",$J["EVENT_DEFINITION"]);echo"</p>\n","<p>","<input type='submit' class='button default' value='",lang(117),"'>";if($ea!="")echo"<input type='submit' class='button' name='drop' value='",lang(168),"'>",confirm(lang(220,$ea));echo"</p>\n",input_token(),"</form>\n";}elseif(isset($_GET["procedure"])){$oa=($_GET["name"]?:$_GET["procedure"]);$Xj=(isset($_GET["function"])?"FUNCTION":"PROCEDURE");$J=$_POST;$J["fields"]=(array)$J["fields"];if($_POST&&!process_fields($J["fields"])){foreach($J["fields"]as$u=>$k){if($k["field"]=="")unset($J["fields"][$u]);}$Sh=routine_id($oa,routine($_GET["procedure"],$Xj));$_h=routine_id($J["name"],$J);$ic=create_routine($Xj,$J);$y=substr(ME,0,-1);$Xg=lang(259);if(!$_POST["drop"]&&$Sh==$_h&&(DIALECT!="sql"||Connection::get()->isMariaDB()))query_redirect(substr_replace($ic,' OR REPLACE',6,0),$y,$Xg);else{$Pl="$J[name]_adminer_".uniqid();drop_create("DROP $Xj $Sh",$ic,"DROP $Xj $_h",create_routine($Xj,["name"=>$Pl]+$J),"DROP $Xj ".routine_id($Pl,$J),$y,lang(260),$Xg,lang(261),$oa,$J["name"]);}}if($oa!=""){$S=isset($_GET["function"])?lang(262):lang(263);page_header($S.": ".h($oa),[$S]);}else{$S=isset($_GET["function"])?lang(264):lang(265);page_header($S,[$S]);}if(!$_POST){if($oa=="")$J["language"]="sql";else{$J=routine($_GET["procedure"],$Xj);$J["name"]=$oa;}}$sb=get_vals("SHOW CHARACTER SET");sort($sb);$Yj=routine_languages();echo"<form action='' method='post' id='form'>\n","<p>",lang(226),": ","<input class='input' name='name' value='",h($J["name"]),"' data-maxlength='64' autocapitalize='off'>";if($Yj)echo"<span id='label-language'>",lang(10),":</span> ",html_select("language",$Yj,$J["language"],"","label-language");echo"<input type='submit' class='button default' value='",lang(117),"'>","</p>\n","<div class='scrollable'>\n","<table class='nowrap' id='edit-fields'>\n";edit_fields($J["fields"],$sb,$Xj);if(isset($_GET["function"])){echo"<tbody><tr>";if(support("move_col"))echo"<th></th>";echo"<th>",lang(266),"</th>";edit_type("returns",(array)$J["returns"],$sb,[],(DIALECT=="pgsql"?["void","trigger"]:[]));echo"<td></td>","</tr></tbody>\n";}echo"</table>\n",script("initFieldsEditing(gid('edit-fields'));");if(support("move_col"))echo
script("initSortable('#edit-fields tbody');");echo"</div>\n","<p>";textarea("definition",$J["definition"],20);echo"</p>\n<p>","<input type='submit' class='button default' value='",lang(117),"'>";if($oa!="")echo"<input type='submit' class='button' name='drop' value='",lang(168),"'>",confirm(lang(220,$oa));echo
input_token(),"</p>\n","</form>\n";}elseif(isset($_GET["sequence"])){$qa=$_GET["sequence"];$J=$_POST;if($_POST){$x=substr(ME,0,-1);$A=trim($J["name"]);if($_POST["drop"])query_redirect("DROP SEQUENCE ".idf_escape($qa),$x,lang(267));elseif($qa=="")query_redirect("CREATE SEQUENCE ".idf_escape($A),$x,lang(268));elseif($qa!=$A)query_redirect("ALTER SEQUENCE ".idf_escape($qa)." RENAME TO ".idf_escape($A),$x,lang(269));else
redirect($x);}if($qa!="")page_header(lang(270).": ".h($qa),[h($qa)]);else
page_header(lang(271),[lang(272)]);if(!$J)$J["name"]=$qa;echo"<form action='' method='post'>\n","<p>","<input class='input' name='name' value='",h($J["name"]),"' autocapitalize='off'>","<input type='submit' class='button default' value='",lang(117),"'>";if($qa!="")echo"<input type='submit' class='button' name='drop' value='".lang(168)."'>".confirm(lang(220,$qa))."\n";echo
input_token(),"</p>\n","</form>\n";}elseif(isset($_GET["type"])){$ra=$_GET["type"];$J=$_POST;if($_POST){$x=substr(ME,0,-1);if($_POST["drop"])query_redirect("DROP TYPE ".idf_escape($ra),$x,lang(273));else
query_redirect("CREATE TYPE ".idf_escape(trim($J["name"]))." $J[as]",$x,lang(274));}if($ra!="")page_header(lang(275).": ".h($ra),[h($ra)]);else
page_header(lang(272),[lang(272)]);if(!$J)$J["as"]="AS ";echo"<form action='' method='post'>\n";if($ra!=""){$um=Driver::get()->getTypes();$xd=type_values($um[$ra]);if($xd)echo"<p><code class='jush-".DIALECT."'>ENUM (".h($xd).")</code><p>\n";echo"<p>","<input type='submit' class='button' name='drop' value='".lang(168)."'>".confirm(lang(220,$ra)),"</p>\n";}else{echo"<p>",lang(226).": <input class='input' name='name' value='".h($J['name'])."' autocapitalize='off'>\n",doc_link(['pgsql'=>"datatype-enum.html",],"?"),"</p>\n<p>";textarea("as",$J["as"]);echo"</p>\n","<p><input type='submit' class='button default' value='".lang(117)."'></p>\n";}echo
input_token(),"</form>\n";}elseif(isset($_GET["check"])){$a=$_GET["check"];$A=$_GET["name"];$J=$_POST;if($J){if(DIALECT=="sqlite")$ll=recreate_table($a,$a,[],[],[],"",[],"$A",($J["drop"]?"":$J["clause"]));else{$ll=($A==""||queries("ALTER TABLE ".table($a)." DROP CONSTRAINT ".idf_escape($A)));if(!$J["drop"])$ll=(bool)queries("ALTER TABLE ".table($a)." ADD".($J["name"]!=""?" CONSTRAINT ".idf_escape($J["name"]):"")." CHECK ($J[clause])");}queries_redirect(ME."table=".urlencode($a),($J["drop"]?lang(276):($A!=""?lang(277):lang(278))),$ll);}if($A!="")page_header(lang(279).": ".h($A),["table"=>$a,lang(279)]);else
page_header(lang(182).": ".h($a),["table"=>$a,lang(182)]);if(!$J){$xb=Driver::get()->checkConstraints($a);$J=["name"=>$A,"clause"=>$xb[$A]];}echo"<form action='' method='post'>\n","<p>";if(DIALECT!="sqlite")echo
lang(226).': <input name="name" value="'.h($J["name"]).'" class="input" data-maxlength="64" autocapitalize="off"> ';echo
doc_link(['sql'=>"create-table-check-constraints.html",'mariadb'=>"reference/sql-statements/data-definition/constraint",'pgsql'=>"ddl-constraints.html#DDL-CONSTRAINTS-CHECK-CONSTRAINTS",'mssql'=>"relational-databases/tables/create-check-constraints",'sqlite'=>"lang_createtable.html#check_constraints",],"?"),"</p>\n<p>";textarea("clause",$J["clause"]);echo"</p>\n<p>","<input type='submit' class='button default' value='",lang(117),"'>";if($A!="")echo"<input type='submit' class='button' name='drop' value='",lang(168),"'>",confirm(lang(220,$A));echo
input_token(),"</p>\n","</form>\n";}elseif(isset($_GET["trigger"])){$a=$_GET["trigger"];$A=isset($_GET["name"])?$_GET["name"]:"";$nm=trigger_options();$J=trigger($A,$a)+["Trigger"=>$a."_bi"];if($_POST){if(in_array($_POST["Timing"],$nm["Timing"])&&in_array($_POST["Event"],$nm["Event"])&&in_array($_POST["Type"],$nm["Type"])){$Vh=" ON ".table($a);$bd="DROP TRIGGER ".idf_escape($A).(DIALECT=="pgsql"?$Vh:"");$y=ME."table=".urlencode($a);if($_POST["drop"])query_redirect($bd,$y,lang(280));else{if($A!="")queries($bd);queries_redirect($y,($A!=""?lang(281):lang(282)),(bool)queries(create_trigger($Vh,$_POST)));if($A!="")queries(create_trigger($Vh,$J+["Type"=>reset($nm["Type"])]));}}$J=$_POST;}if($A!="")page_header(lang(283).": ".h($A),["table"=>$a,lang(283)]);else
page_header(lang(184).": ".h($a),["table"=>$a,lang(184)]);echo"<form action='' method='post' id='form'>\n","<table class='box box-light'>\n","<tr><th id='label-time'>",lang(284),"</th><td>",html_select("Timing",$nm["Timing"],$J["Timing"],"triggerChange(/^".js_escape_re($a)."_[ba][iud]$/, '".js_escape($a)."', this.form);","label-time"),"</td></tr>\n","<tr><th id='label-event'>",lang(285),"</th><td>",html_select("Event",$nm["Event"],$J["Event"],"this.form['Timing'].onchange();","label-event");if(in_array("UPDATE OF",$nm["Event"]))echo" <input name='Of' value='".h($J["Of"])."' class='input hidden'>";echo"</td></tr>\n","<tr><th id='label-type'>",lang(45),"</th><td>",html_select("Type",$nm["Type"],$J["Type"],"","label-type"),"</td></tr>\n","</table>\n","<p>",lang(226),"<input class='input' name='Trigger' value='",h($J["Trigger"]),"' data-maxlength='64' autocapitalize='off'>","</p>\n",script("gid('form')['Timing'].onchange();"),"<p>";textarea("Statement",$J["Statement"]);echo"</p>\n","<p>","<input type='submit' class='button default' value='",lang(117),"'>";if($A!="")echo"<input type='submit' class='button' name='drop' value='",lang(168),"'>",confirm(lang(220,$A));echo"</p>\n",input_token(),"</form>\n";}elseif(isset($_GET["user"])){$sa=$_GET["user"];$pj=[""=>["All privileges"=>""]];foreach(get_rows("SHOW PRIVILEGES")as$J){foreach(explode(",",($J["Privilege"]=="Grant option"?"":$J["Context"]))as$dc)$pj[$dc=="File access on server"?"Server Admin":$dc][$J["Privilege"]]=$J["Comment"];}unset($pj["Server Admin"]["Usage"]);foreach($pj["Tables"]as$u=>$W)unset($pj["Databases"][$u]);$zh=[];if($_POST){foreach($_POST["objects"]as$u=>$W)$zh[$W]=(array)$zh[$W]+(array)$_POST["grants"][$u];}$Ee=[];if(isset($_GET["host"])&&($H=Connection::get()->query("SHOW GRANTS FOR ".q($sa)."@".q($_GET["host"])))){while($J=$H->fetchRow()){if(preg_match('~GRANT (.*) ON (.*) TO ~',$J[0],$z)&&preg_match_all('~ *([^(,]*[^ ,(])( *\([^)]+\))?~',$z[1],$_,PREG_SET_ORDER)){foreach($_
as$W){if($W[1]!="USAGE")$Ee["$z[2]$W[2]"][$W[1]]=true;if(preg_match('~ WITH GRANT OPTION~',$J[0]))$Ee["$z[2]$W[2]"]["GRANT OPTION"]=true;}}}}$Wi=!Connection::get()->isMariaDB()&&Connection::get()->isMinVersion("8");if($_POST){$Uh=(isset($_GET["host"])?q($sa)."@".q($_GET["host"]):"''");if($_POST["drop"])query_redirect("DROP USER $Uh",ME."privileges=",lang(286));else{$Bh=q($_POST["user"])."@".q($_POST["host"]);$Pi=$_POST["pass"];$lc=false;$H=true;if($Uh!=$Bh){$lc=(bool)queries("CREATE USER $Bh IDENTIFIED BY ".($_POST["hashed"]?"PASSWORD ":"").q($Pi));$H=$lc;}elseif($Pi!="")$H=(bool)queries("SET PASSWORD FOR $Bh = ".($Wi||$_POST["hashed"]?q($Pi):"PASSWORD(".q($Pi).")"));if($H){$Uj=[];foreach($zh
as$Kh=>$Ce){if(isset($_GET["grant"]))$Ce=array_filter($Ce);$Ce=array_keys($Ce);if(isset($_GET["grant"]))$Uj=array_diff(array_keys(array_filter($zh[$Kh],'strlen')),$Ce);elseif($Uh==$Bh){$Rh=array_keys((array)$Ee[$Kh]);$Uj=array_diff($Rh,$Ce);$Ce=array_diff($Ce,$Rh);unset($Ee[$Kh]);}if(preg_match('~^(.+)\s*(\(.*\))?$~U',$Kh,$z)&&(!grant(false,$Uj,$z[2],$z[1],$Bh)||!grant(true,$Ce,$z[2],$z[1],$Bh))){$H=false;break;}}}if($H&&isset($_GET["host"])){if($Uh!=$Bh)queries("DROP USER $Uh");elseif(!isset($_GET["grant"])){foreach($Ee
as$Kh=>$Uj){if(preg_match('~^(.+)(\(.*\))?$~U',$Kh,$z))grant(false,array_keys($Uj),$z[2],$z[1],$Bh);}}}if($H&&!Queries::$queries)redirect(ME."privileges=");queries_redirect(ME."privileges=",(isset($_GET["host"])?lang(287):lang(288)),$H);if($lc)Connection::get()->query("DROP USER $Bh");}}$S=isset($_GET["host"])?lang(6).": ".h("$sa@$_GET[host]"):lang(192);$am=isset($_GET["host"])?h($sa):lang(192);page_header($S,["privileges"=>['',lang(73)],$am]);if($_POST){$J=$_POST;$Ee=$zh;}else{$J=$_GET+["host"=>Connection::get()->getValue("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)")];if($Ee)$Ee[".*"]=[];elseif(DB!="")$Ee[idf_escape(addcslashes(DB,"%_\\")).".*"]=[];else$Ee["*.* "]=[];}echo"<form action='' method='post'>\n","<table class='box box-light'>\n","<tr><th>",lang(5),"</th>","<td><input class='input' name='host' data-maxlength='60' value='",h($J["host"]),"' autocapitalize='off'></td>\n","<tr><th>",lang(6),"</th>","<td><input class='input' name='user' data-maxlength='80' value='",h($J["user"]),"' autocapitalize='off'></td>\n",'<tr><th>',lang(30),"</th>","<td><input class='input' name='pass' id='pass' value='",h($J["pass"]),"' autocomplete='new-password'>";if(!$Wi)echo
checkbox("hashed",1,$J["hashed"],lang(289),"typePassword(this.form['pass'], this.checked);");echo"</td>\n";if(!$J["hashed"])echo
script("typePassword(gid('pass'));");echo"</table>\n","<div class='scrollable'><table class='checkable'>\n","<thead><tr><th colspan='2'>".lang(73).doc_link(['sql'=>"grant.html#priv_level","mariadb"=>"reference/sql-statements/account-management-sql-statements/grant#privilege-levels"])."</th>";$q=0;foreach($Ee
as$Kh=>$Ce){echo"<th>";if($Kh=="*.*")echo"*.*",input_hidden("objects[$q]","*.*");else
echo"<input class='input' name='objects[$q]' value='".h(trim($Kh))."' size='10' autocapitalize='off'>";echo"</th>";$q++;}echo"</tr></thead>\n";foreach([""=>"","Server Admin"=>lang(5),"Databases"=>lang(31),"Tables"=>lang(9),"Procedures"=>lang(290),]as$dc=>$Hc){foreach((array)$pj[$dc]as$oj=>$Ob){echo"<tr>";if($Hc)echo"<td>$Hc</td>";echo"<td".(!$Hc?" colspan='2'":"").' lang="en" title="'.h($Ob).'">'.h($oj)."</td>";$q=0;foreach($Ee
as$Kh=>$Ce){$A="'grants[$q][".h(strtoupper($oj))."]'";$X=$Ce[strtoupper($oj)];$tj=strpos($Kh,"@")!==false;$yh=$Kh==".*";$Ea=$oj=="All privileges";$De=$oj=="Grant option";if($Kh=="*.*"&&$oj=="Proxy")echo"<td></td>";elseif($tj&&$oj!="Proxy"&&!$De)echo"<td></td>";elseif($dc=="Server Admin"&&$Kh!=(isset($Ee["*.*"])?"*.*":".*")&&!(($tj||$yh)&&$oj=="Proxy"))echo"<td></td>";elseif(isset($_GET["grant"]))echo"<td><select name=$A>"."<option></option>"."<option value='1'".($X?" selected":"").">".lang(291)."</option>"."<option value='0'".($X=="0"?" selected":"").">".lang(292)."</option>"."</select></td>";else{echo"<td class='center'><label class='block'>","<input type='checkbox' name=$A value='1'".($X?" checked":"").($Ea?" id='grants-$q-all'":(!$De?" class='grants-$q'":"")).">";if($Ea)echo
script("qsl('input').onclick = function () { if (this.checked) formUncheckAll('.grants-$q'); };");elseif(!$De)echo
script("qsl('input').onclick = function () { if (this.checked) formUncheck('grants-$q-all'); };");echo"</label>";}$q++;}echo"</tr>";}}echo"</table></div>\n","<p>","<input type='submit' class='button default' value='",lang(117),"'>\n";if(isset($_GET["host"]))echo"<input type='submit' class='button' name='drop' value='",lang(168),"'>\n",confirm(lang(220,"$sa@$_GET[host]"));echo
input_token(),"</p>\n","</form>\n";}elseif(isset($_GET["processlist"])){if(support("kill")){if($_POST){$ag=0;foreach((array)$_POST["kill"]as$W){if(kill_process($W))$ag++;}queries_redirect(ME."processlist=",lang(293,$ag),$ag||!$_POST["kill"]);}}page_header(lang(158),[lang(158)]);echo"<form action='' method='post'>\n","<div class='scrollable'>\n","<table class='nowrap checkable'>\n";$q=-1;foreach(process_list()as$q=>$J){if(!$q){echo"<thead><tr lang='en'>".(support("kill")?"<th>":"");foreach($J
as$u=>$W)echo"<th>$u".doc_link(['sql'=>"show-processlist.html#processlist_".strtolower($u),'mariadb'=>"reference/sql-statements/administrative-sql-statements/show/show-processlist",'pgsql'=>"monitoring-stats.html#PG-STAT-ACTIVITY-VIEW",'oracle'=>"REFRN30223",]);echo"</thead>\n","<tbody>\n";}echo"<tr>".(support("kill")?"<td>".checkbox("kill[]",$J[DIALECT=="sql"?"Id":"pid"],0):"");foreach($J
as$u=>$W)echo"<td>".($W!=""&&((DIALECT=="sql"&&$u=="Info"&&preg_match("~Query|Killed~",$J["Command"]))||(DIALECT=="pgsql"&&$u=="query")||(DIALECT=="oracle"&&$u=="sql_text"))?"<code class='jush-".DIALECT."'>".truncate_utf8($W,100).'</code> <a href="'.h(ME.($J["db"]!=""?"db=".urlencode($J["db"])."&":"")."sql=".urlencode($W)).'">'.icon("edit").lang(294).'</a>':h($W));echo"\n";}if($q>=0)echo"</tbody>\n",script("mixin(qsl('tbody'), {onclick: tableClick, ondblclick: event => tableClick(event, true)});");echo"</table>\n","</div>\n","<p>";if(support("kill"))echo($q+1)."/".lang(295,max_connections()),"<p><input type='submit' class='button' value='".lang(296)."'>\n";echo
input_token(),"</p>\n","</form>\n",script("tableCheck();");}elseif(isset($_GET["select"])){$a=$_GET["select"];$Q=table_status1($a);$t=indexes($a);$l=fields($a);$ne=column_foreign_keys($a);$Nh=$Q["Oid"];$Vj=[];$c=[];$nk=[];$li=[];$Tl=null;foreach($l
as$u=>$k){$A=Admin::get()->getFieldName($k);$th=html_entity_decode(strip_tags($A),ENT_QUOTES);if(isset($k["privileges"]["select"])&&$A!=""){$c[$u]=$th;if(is_shortable($k))$Tl=Admin::get()->processSelectionLength();}if(isset($k["privileges"]["where"])&&$A!="")$nk[$u]=$th;if(isset($k["privileges"]["order"])&&$A!="")$li[$u]=$th;$Vj+=$k["privileges"];}list($L,$Fe)=Admin::get()->processSelectionColumns($c,$t);$L=array_unique($L);$Fe=array_unique($Fe);$If=count($Fe)<count($L);$Z=Admin::get()->processSelectionSearch($l,$t);$C=Admin::get()->processSelectionOrder($l,$t);$w=Admin::get()->processSelectionLimit();if($_GET["modify"]&&!Admin::get()->isDataEditAllowed())redirect(ME."select=".urlencode($a));if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$zm=>$J){$Na=convert_field($l[key($J)]);$L=[$Na?:idf_escape(key($J))];$Z[]=where_check($zm,$l);$I=Driver::get()->select($a,$L,$Z,$L);if($I)echo
first($I->fetchRow());}exit;}$lj=$Bm=[];foreach($t
as$s){if($s["type"]=="PRIMARY"){$lj=array_flip($s["columns"]);$Bm=($L?$lj:[]);foreach($Bm
as$u=>$W){if(in_array(idf_escape($u),$L))unset($Bm[$u]);}break;}}if($Nh&&!$lj){$lj=$Bm=[$Nh=>0];$t[]=["type"=>"PRIMARY","columns"=>[$Nh]];}$N=Admin::get()->getSettings();if($_POST){$in=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$xb=[];foreach($_POST["check"]as$tb)$xb[]=where_check($tb,$l);$in[]="((".implode(") OR (",$xb)."))";}$in=($in?"\nWHERE ".implode(" AND ",$in):"");if($_POST["export"]){$N->updateParameters(["exportFormat"=>$_POST["format"],"exportOutput"=>$_POST["output"],]);dump_headers($a);Admin::get()->dumpTable($a,"");$ve=($L?implode(", ",$L):"*").convert_fields($c,$l,$L)."\nFROM ".table($a);$Ie=($Fe&&$If?"\nGROUP BY ".implode(", ",$Fe):"").($C?"\nORDER BY ".implode(", ",$C):"");if(!is_array($_POST["check"])||$lj)$G="SELECT $ve$in$Ie";else{$wm=[];foreach($_POST["check"]as$W)$wm[]="(SELECT".limit($ve,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($W,$l).$Ie,1).")";$G=implode(" UNION ALL ",$wm);}Admin::get()->dumpData($a,"table",$G);exit;}if($_POST["save"]||$_POST["delete"]){$H=true;$Aa=0;$Hk=[];if(!$_POST["delete"]){$vk=array_keys($_POST["fields"]+$_POST["function"]);foreach($vk
as$A){$W=process_input($l[$A]);if($W!==null&&($_POST["clone"]||$W!==false))$Hk[idf_escape($A)]=($W!==false?$W:idf_escape($A));}}if($_POST["delete"]||$Hk){if($_POST["clone"])$G="INTO ".table($a)." (".implode(", ",array_keys($Hk)).")\nSELECT ".implode(", ",$Hk)."\nFROM ".table($a);if($_POST["all"]||($lj&&is_array($_POST["check"]))||$If){$H=($_POST["delete"]?Driver::get()->delete($a,$in):($_POST["clone"]?queries("INSERT $G$in".Driver::get()->getInsertReturningSql($a)):Driver::get()->update($a,$Hk,$in)));$Aa=Connection::get()->getAffectedRows();if(is_object($H))$Aa+=$H->getRowsCount();}else{foreach((array)$_POST["check"]as$W){$hn="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($W,$l);$H=($_POST["delete"]?Driver::get()->delete($a,$hn,1):($_POST["clone"]?queries("INSERT".limit1($a,$G,$hn)):Driver::get()->update($a,$Hk,$hn,1)));if(!$H)break;$Aa+=Connection::get()->getAffectedRows();}}}$Xg=lang(297,$Aa);if($_POST["clone"]&&$H&&$Aa==1){$jg=last_id($H);if($jg)$Xg=lang(214," $jg");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page":""),$Xg,(bool)$H);if(!$_POST["delete"]){$md=array_filter($l,function($k){return!(isset($k["generated"])?$k["generated"]:null);});edit_form($a,$md,(array)$_POST["fields"],!$_POST["clone"]);page_footer();exit;}}elseif(!$_POST["import"]){if(!$_POST["val"])Admin::get()->addError(lang(298));else{$ll=true;$Aa=0;foreach($_POST["val"]as$zm=>$J){$Hk=[];foreach($J
as$u=>$W){$u=bracket_escape($u,true);$Hk[idf_escape($u)]=(preg_match('~char|text~',$l[$u]["type"])||$W!=""?Admin::get()->processFieldInput($l[$u],$W):"NULL");}$ll=(bool)Driver::get()->update($a,$Hk," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($zm,$l),($If||$lj?0:1)," ");if(!$ll)break;$Aa+=Connection::get()->getAffectedRows();}queries_redirect(remove_from_uri(),lang(297,$Aa),$ll);}}elseif(!is_string($m=get_file("csv_file",true)))Admin::get()->addError(upload_error($m));elseif(!preg_match('~~u',$m))Admin::get()->addError(lang(299));else{$N->updateParameter("exportFormat",$_POST["import_format"]);$Jb=array_keys($l);preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$m,$_);$Aa=count($_[0]);Driver::get()->begin();$wk=($_POST["import_format"]=="csv;"?";":($_POST["import_format"]=="tsv"?"\t":","));$K=[];foreach($_[0]as$u=>$W){preg_match_all("~((?>\"[^\"]*\")+|[^$wk]*)$wk~",$W.$wk,$Ig);if(!$u&&!array_diff($Ig[1],$Jb)){$Jb=$Ig[1];$Aa--;}else{$Hk=[];foreach($Ig[1]as$q=>$Cb)$Hk[idf_escape($Jb[$q])]=($Cb==""&&$l[$Jb[$q]]["null"]?"NULL":q(preg_match('~^".*"$~s',$Cb)?str_replace('""','"',substr($Cb,1,-1)):$Cb));$K[]=$Hk;}}$ll=!$K||Driver::get()->insertUpdate($a,$K,$lj);if($ll)Driver::get()->commit();queries_redirect(remove_from_uri("page"),lang(300,$Aa),$ll);Driver::get()->rollback();}}$Bl=Admin::get()->getTableName($Q);if(is_ajax()){page_headers();ob_start();}else
page_header(lang(56).": $Bl",[$Bl]);$zf=null;if(isset($Vj["insert"])||!support("table")){$zf=[];foreach((array)$_GET["where"]as$W){if(isset($ne[$W["col"]])&&count($ne[$W["col"]])==1&&($W["op"]=="="||(!$W["op"]&&(is_array($W["val"])||!preg_match('~[_%]~',$W["val"])))))$zf["preset"."[".bracket_escape($W["col"])."]"]=$W["val"];}}Admin::get()->printTableMenu($Q,$zf);if(!$c&&support("table"))echo"<p class='error'>".lang(301).($l?".":": ".error())."\n";else{echo"<form id='form' action=''>\n","<div hidden>";hidden_fields_get();if(DB!=""){echo
input_hidden("db",DB);if(isset($_GET["ns"]))echo
input_hidden("ns",$_GET["ns"]);}echo
input_hidden("select",$a),"<input type='submit' class='button' value='".lang(56)."'>","</div>\n","<div class='field-sets'>\n";Admin::get()->printSelectionColumns($L,$c);Admin::get()->printSelectionSearch($Z,$nk,$t);Admin::get()->printSelectionOrder($C,$li,$t);Admin::get()->printSelectionLimit($w);Admin::get()->printSelectionLength($Tl);Admin::get()->printSelectionAction($t);echo"</div>\n</form>\n";$D=isset($_GET["page"])?$_GET["page"]:null;if($D=="last"){$te=Connection::get()->getValue(count_rows($a,$Z,$If,$Fe));$D=(int)floor(max(0,intval($te)-1)/$w);}else{$te=false;$D=(int)$D;}$ok=$L;$Ge=$Fe;if(!$ok){$ok[]="*";$fc=convert_fields($c,$l,$L);if($fc)$ok[]=substr($fc,2);}foreach($L
as$u=>$W){$k=$l[idf_unescape($W)];if($k&&($Na=convert_field($k)))$ok[$u]="$Na AS $W";}if(DIALECT=="pgsql"||DIALECT=="mssql"){foreach((array)$_GET["columns"]as$u=>$W){if(isset($ok[$u])&&$W["fun"])$ok[$u].=" AS ".idf_escape(apply_sql_function($W["fun"],($W["col"]!=""?$W["col"]:"*")));}}if(!$If&&$Bm){foreach($Bm
as$u=>$W){$ok[]=idf_escape($u);if($Ge)$Ge[]=idf_escape($u);}}$H=Driver::get()->select($a,$ok,$Z,$Ge,$C,$w,$D,true);if(!$H)echo"<p class='error'>".error()."\n";else{if(DIALECT=="mssql"&&$D)$H->seek($w*$D);$K=[];while($J=$H->fetchAssoc()){if($D&&DIALECT=="oracle")unset($J["RNUM"]);$K[]=$J;}if($_GET["modify"]&&$K){$Qg=max_input_vars(count($K[0])+1,20);echo($Qg&&count($K)>$Qg?"<p class='error'>".max_input_vars_error()."\n":"");}echo"<form id='selection_form' action='' method='post' enctype='multipart/form-data'>\n","<div class='table-footer-parent'>\n";if($_GET["page"]!="last"&&$w&&$Fe&&$If&&DIALECT=="sql")$te=Connection::get()->getValue(" SELECT FOUND_ROWS()");$nd=false;if(!$K)echo"<p class='message'>".lang(92)."\n";else{$Xa=Admin::get()->getBackwardKeys($a,$Bl);echo"<div class='scrollable'>\n","<table id='table' class='nowrap checkable'>\n","<thead><tr>";if($Fe||!$L){echo"<th class='actions'><input type='checkbox' id='all-page' class='jsonly' title='".lang(302)."'>".script("gid('all-page').onclick = partial(formCheck, /^check/);","");if(Admin::get()->isDataEditAllowed())echo" <a href='",h($_GET["modify"]?remove_from_uri("modify"):$_SERVER["REQUEST_URI"]."&modify=1")."' title='",lang(303),"'>",icon_solo("edit-all"),"</a>";}$vh=[];$ye=[];reset($L);$Aj=1;foreach($K[0]as$u=>$W){if(!isset($Bm[$u])){$qk=key($L);$W=isset($_GET["columns"][$qk])?$_GET["columns"][$qk]:[];$k=$l[$L?($W?$W["col"]:current($L)):$u];$A=($k?Admin::get()->getFieldName($k,$Aj):(isset($W["fun"])?"*":h($u)));if($A!=""){$Aj++;$vh[$u]=$A;$b=idf_escape($u);$cf=remove_from_uri('(order|desc)[^=]*|page').'&order%5B0%5D='.urlencode($u);$Hc="&desc%5B0%5D=1";$ki=isset($C[0])?$C[0]:"";$Rk=preg_replace('~ DESC( NULLS LAST)?$~','',$ki);$Tk=($Rk==$b||$Rk==$u);echo"<th id='th[".h(bracket_escape($u))."]'".($Tk?" aria-sort='".($Rk==$ki?"ascending":"descending")."'":"").">";$xe=apply_sql_function(isset($W["fun"])?$W["fun"]:null,$A);$Sk=isset($k["privileges"]["order"])||(isset($W["fun"])?$W["fun"]:null);if($Sk)echo'<a href="',h($cf.($Tk&&$Rk==$ki?$Hc:'')),'">',"$xe</a>";else
echo$xe;echo"<span class='column'>";if($Sk)echo"<a href='".h($cf.$Hc)."' title='".lang(63)."' class='button light'>",icon_solo("arrow-down"),"</a>";if(!isset($W["fun"])&&isset($k["privileges"]["where"]))echo"<a href='#fieldset-search' title='".lang(60)."' class='button light jsonly'>",icon_solo("search"),"</a>",script("qsl('a').onclick = partial(selectSearch, '".js_escape($u)."');");echo"</span>";}$ye[$u]=isset($W["fun"])?$W["fun"]:null;next($L);}}$rg=[];if($_GET["modify"]){foreach($K
as$J){foreach($J
as$u=>$W)$rg[$u]=max($rg[$u],min(40,strlen(utf8_decode($W))));}}if($Xa)echo"<th>".lang(19)."</th>";echo"</thead>\n","<tbody>\n";if(is_ajax())ob_end_clean();foreach(Admin::get()->fillForeignDescriptions($K,$ne)as$rh=>$J){$ym=unique_array($K[$rh],$t);if(!$ym){$ym=[];reset($L);foreach($K[$rh]as$u=>$W){if(!preg_match('~^(COUNT|AVG|GROUP_CONCAT|MAX|MIN|SUM)\(~',current($L)))$ym[$u]=$W;next($L);}}$zm="";foreach($ym
as$u=>$W){$k=isset($l[$u])?$l[$u]:null;$Hf=$k&&is_blob($k);if((DIALECT=="sql"||DIALECT=="pgsql")&&$k&&($Hf||preg_match('~char|text|enum|set~',$k["type"]))&&strlen($W)>64){$u=(strpos($u,'(')?$u:idf_escape($u));$u="MD5(".($Hf||DIALECT!='sql'||preg_match("~^utf8~",isset($k["collation"])?$k["collation"]:"")?$u:"CONVERT($u USING ".charset(Connection::get()).")").")";$W=md5($Hf?(string)Connection::get()->formatValue($W,$k):$W);}$zm
.="&".($W!==null?urlencode("where[".bracket_escape($u)."]")."=".urlencode($W===false?"f":$W):"null%5B%5D=".urlencode($u));}echo"<tr>";if($Fe||!$L){echo"<td class='actions'>",checkbox("check[]",substr($zm,1),in_array(substr($zm,1),(array)$_POST["check"]));if(!$If&&Admin::get()->isDataEditAllowed())echo" <a href='",h(ME."edit=".urlencode($a).$zm),"' class='edit' title='",lang(39),"'>",icon_solo("edit"),"</a>";}reset($L);foreach($J
as$u=>$W){if(isset($vh[$u])){$b=current($L);$k=isset($l[$u])?$l[$u]:null;$x="";if($k&&is_blob($k)&&$W!="")$x=ME.'download='.urlencode($a).'&field='.urlencode($u).$zm;if(!$x&&$W!==null){foreach((array)$ne[$u]as$o){if(count($ne[$u])==1||end($o["source"])==$u){$x="";foreach($o["source"]as$q=>$Uk)$x
.=where_link($q,$o["target"][$q],$K[$rh][$Uk]);$x=($o["db"]!=""?preg_replace('~([?&]db=)[^&]+~','\1'.urlencode($o["db"]),ME):ME).'select='.urlencode($o["table"]).$x;if($o["ns"])$x=preg_replace('~([?&]ns=)[^&]+~','\1'.urlencode($o["ns"]),$x);if(count($o["source"])==1)break;}}}if($b=="COUNT(*)"){$x=ME."select=".urlencode($a);$q=0;foreach((array)$_GET["where"]as$V){if(!array_key_exists($V["col"],$ym))$x
.=where_link($q++,$V["col"],$V["val"],$V["op"]);}foreach($ym
as$Sf=>$V)$x
.=where_link($q++,$Sf,$V);}$Gh=$W===null;$df=select_value($W,$x,$k,$Tl);$Ad=bracket_escape($u);$r=h("val[$zm][$Ad]");$gj=isset($_POST["val"][$zm][$Ad])?$_POST["val"][$zm][$Ad]:null;$Dm=isset($k["privileges"]["update"])?$k["privileges"]["update"]:false;$ld=!is_array($W)&&!($k&&is_blob($k))&&is_utf8((string)$W)&&$K[$rh][$u]==$W&&!$ye[$u]&&!(isset($k["generated"])?$k["generated"]:false);$T=($b&&preg_match('~^(AVG|MIN|MAX)\((.+)\)~',$b,$_)?$l[idf_unescape($_[2])]["type"]:(isset($k["type"])?$k["type"]:null));$lh=$T=="money"||($b&&preg_match('~^SUM\((.+)\)~',$b,$_)&&$l[idf_unescape($_[1])]["type"])=="money";$Rl=$T&&preg_match('~text|json|lob~',$T);$Jh=($T&&preg_match(number_type(),$T))||($b&&preg_match('~^(CHAR_LENGTH|ROUND|FLOOR|CEIL|UNIX_TIMESTAMP|TIME_TO_SEC|COUNT|SUM)\(~',$b));$Ab=$Jh&&($Gh||is_numeric(strip_tags($df))||$lh)?"class='number'":"";echo"<td id='$r' $Ab";if(($_GET["modify"]&&$ld&&!$Gh)||$gj!==null){$nd=true;$Le=h($gj!==null?$gj:$W);echo" data-editing='true'>".($Rl?"<textarea name='$r' cols='30' rows='".(substr_count($W,"\n")+1)."'>$Le</textarea>":"<input class='input' name='$r' value='$Le' size='$rg[$u]'>");}else{$Fg=strpos($df,"<i>…</i>");if($Dm)echo" data-text='".($Fg?2:($Rl?1:0))."'".($ld?"":" data-warning='".lang(304)."'");echo">$df";}}next($L);}if($Xa){echo"<td>";Admin::get()->printBackwardKeys($Xa,$K[$rh]);echo"</td>";}echo"</tr>\n";}if(is_ajax())exit;echo"</tbody>\n",script("mixin(qs('#table tbody'), {onclick: event => tableClick(event, false, ".(Admin::get()->isDataEditAllowed()?"true":"false")."), ondblclick: event => tableClick(event, true), onkeydown: onEditingKeydown});"),"</table>\n",script("initToggles(gid('table'));"),"</div>\n";}if(!is_ajax()){if($K||$D){$Cd=true;if($_GET["page"]!="last"){if(!$w||(count($K)<$w&&($K||!$D)))$te=($D?$D*$w:0)+count($K);elseif(DIALECT!="sql"||!$If){$te=($If?false:found_rows($Q,$Z));if($te<max(1e4,2*($D+1)*$w))$te=first(slow_query(count_rows($a,$Z,$If,$Fe)));elseif(DIALECT=='sql'||DIALECT=='pgsql')$Cd=false;}}$Bi=($w!==null&&($te===false||$te>$w||$D));if($Bi){if(($te===false?count($K)+1:$te-$D*$w)>$w)echo'<p class="links">','<a href="',h(remove_from_uri("page")."&page=".($D+1)),'" class="loadmore">',icon("expand"),lang(305),'</a>',script("qsl('a').onclick = partial(loadNextPage, $w, '".js_escape(lang(306))."');","");echo"\n";}echo"<div class='table-footer'><div class='field-sets'>\n";if($Bi){$Og=($te===false?$D+(count($K)>=$w?2:1):(int)floor(($te-1)/$w));$Xc="<li>…</li>";echo"<fieldset><legend>".lang(307)."</legend>";if(DIALECT!="simpledb"){echo"<div id='fieldset-pagination' class='fieldset-content'><ul class='pagination'>",pagination(0,$D);if($D>5)echo$Xc;for($q=max(1,$D-4);$q<min($Og,$D+5);$q++)echo
pagination($q,$D);if($Og>0){if($D+5<$Og)echo$Xc;echo($Cd&&$te!==false?pagination($Og,$D):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$Og'>".lang(308)."</a>");}echo"</ul></div>";}else{echo"<div id='fieldset-pagination'><ul class='pagination'>",pagination(0,$D);if($D>1)echo$Xc;if($D)echo
pagination($D,$D);if($Og>$D){echo
pagination($D+1,$D);if($Og>$D+1)echo$Xc;}echo"</ul></div>";}echo"</fieldset>\n";}echo"<fieldset>","<legend>".lang(309)."</legend><div class='fieldset-content'>";$Qc=($Cd?"":"~ ").$te;echo
checkbox("all",1,0,($te!==false?($Cd?"":"~ ").lang(196,$te):""),"countRows.call(this, '$Qc');")."\n","</div></fieldset>\n";if(Admin::get()->isDataEditAllowed()){echo"<fieldset",($_GET["modify"]?'':' class="jsonly"'),">","<legend>",lang(303),"</legend>";$dk=($_GET["modify"]?"":" data-inline-edit='1'".($nd?"":" disabled"));echo"<div class='fieldset-content'",($_GET["modify"]?"":" title='".lang(298)."'"),">","<input type='submit' class='button' id='modify-save' value='",lang(117),"'",$dk,">","</div>","</fieldset>\n","<fieldset>","<legend>",lang(167)," <span id='selected'></span></legend>","<div class='fieldset-content'>","<input type='submit' class='button' name='edit' value='",lang(39),"'> ","<input type='submit' class='button' name='clone' value='",lang(294),"'> ","<input type='submit' class='button' name='delete' value='",lang(121),"'>",confirm(),"</div>","</fieldset>\n";}$pe=Admin::get()->getDumpFormats();foreach((array)$_GET["columns"]as$b){if($b["fun"]){unset($pe['sql']);break;}}if($pe){print_fieldset_start("export",lang(75)." <span id='selected2'></span>","export");echo
html_select("format",$pe,$N->getParameter("exportFormat"));$zi=Admin::get()->getDumpOutputs();echo($zi?" ".html_select("output",$zi,$N->getParameter("exportOutput")):"")," <input type='submit' class='button' name='export' value='".lang(75)."'>\n";print_fieldset_end("export");}echo"</div></div>\n",script("initTableFooter()");}echo"</div>\n";if(Admin::get()->isDataEditAllowed()){echo"<p>","<a href='#import'>",icon("import"),lang(74),"</a>",script("qsl('a').onclick = partial(toggle, 'import');",""),"</p>","<p id='import'",($_POST["import"]?"":" class='hidden'"),">";if(ini_bool("file_uploads"))echo"<input type='file' name='csv_file'> ",html_select("import_format",["csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"],$N->getParameter("exportFormat"))," <input type='submit' class='button default' name='import' value='".lang(74)."'>",file_upload_form_script("selection_form","csv_file");else
echo
lang(203);echo"</p>";}echo
input_token(),"</form>\n",(!$Fe&&$L?"":script("tableCheck();"));}else
echo"</div>\n";}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["variables"])){$el=isset($_GET["status"]);$S=$el?lang(160):lang(159);page_header($S,[$S]);$Rm=($el?Admin::get()->getStatusVariables():Admin::get()->getServerVariables());if(!$Rm)echo"<p class='message'>",lang(92),"</p>\n";else{echo"<div class='scrollable'><table>\n";foreach($Rm
as$J){echo"<tr>";$u=array_shift($J);echo"<th><code class='jush-".DIALECT.($el?"status":"set")."'>".h($u)."</code></th>";foreach($J
as$W)echo"<td>",nl2br(h($W)),"</td>";echo"</tr>\n";}echo"</table></div>\n";}}elseif(isset($_GET["script"])){header("Content-Type: text/javascript; charset=utf-8");if($_GET["script"]=="db"){$ol=["Data_length"=>0,"Index_length"=>0,"Data_free"=>0];$f=[];$wc=null;foreach(table_status()as$A=>$Q){$f["Comment-$A"]=h($Q["Comment"]);if(!is_view($Q)||preg_match('~materialized~i',$Q["Engine"])){$f["Engine-$A"]=h($Q["Engine"]);$Eb=isset($Q["Collation"])?$Q["Collation"]:"";if($Eb==""){if($wc===null)$wc=db_collation(DB,collations())??"";$Eb=$wc;}$f["Collation-$A"]=h($Eb);foreach($ol+["Auto_increment"=>0,"Rows"=>0]as$u=>$W){if($Q[$u]!=""){$W=format_number($Q[$u]);if($W>=0)$f["$u-$A"]=($u=="Rows"?format_rows($Q):$W);if(isset($ol[$u]))$ol[$u]+=($Q["Engine"]!="InnoDB"||$u!="Data_free"?$Q[$u]:0);}elseif(array_key_exists($u,$Q))$f["$u-$A"]="?";}}}if(function_exists('AdminNeo\db_status'))$ol=db_status();foreach($ol
as$u=>$W)$f["sum-$u"]=format_number($W);echo
json_encode($f,JSON_UNESCAPED_UNICODE);}elseif($_GET["script"]=="kill")Connection::get()->query("KILL ".number($_POST["kill"]));else{$f=[];foreach(count_tables(Admin::get()->getDatabases(false))as$h=>$W){$f["tables-$h"]=$W;$f["size-$h"]=db_size($h);}echo
json_encode($f,JSON_UNESCAPED_UNICODE);}exit;}else{$Kl=array_merge((array)$_POST["tables"],(array)$_POST["views"]);if($Kl&&!$_POST["search"]){$H=true;$Xg="";if(DIALECT=="sql"&&$_POST["tables"]&&count($_POST["tables"])>1&&($_POST["drop"]||$_POST["truncate"]||$_POST["copy"]))queries("SET foreign_key_checks = 0");if($_POST["truncate"]){if($_POST["tables"])$H=truncate_tables($_POST["tables"]);$Xg=lang(310);}elseif($_POST["move"]){$H=move_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$Xg=lang(311);}elseif($_POST["copy"]){$H=copy_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$Xg=lang(312);}elseif($_POST["drop"]){if($_POST["views"])$H=drop_views($_POST["views"]);if($H&&$_POST["tables"])$H=drop_tables($_POST["tables"]);$Xg=lang(313);}elseif(DIALECT=="sqlite"&&$_POST["check"]){foreach((array)$_POST["tables"]as$P){foreach(get_rows("PRAGMA integrity_check(".q($P).")")as$J)$Xg
.="<b>".h($P)."</b>: ".h($J["integrity_check"])."<br>";}}elseif(DIALECT!="sql"){$H=(DIALECT=="sqlite"?queries("VACUUM"):apply_queries("VACUUM".($_POST["optimize"]?" ANALYZE":""),(array)$_POST["tables"]));$Xg=lang(314);}elseif(!$_POST["tables"])$Xg=lang(79);elseif($H=queries(($_POST["optimize"]?"OPTIMIZE":($_POST["check"]?"CHECK":($_POST["repair"]?"REPAIR":"ANALYZE")))." TABLE ".implode(", ",array_map('AdminNeo\idf_escape',$_POST["tables"])))){while($J=$H->fetchAssoc())$Xg
.="<b>".h($J["Table"])."</b>: ".h($J["Msg_text"])."<br>";}queries_redirect($_SERVER["REQUEST_URI"],$Xg,(bool)$H);}if($_GET["ns"]=="")page_header(lang(31).": ".h(DB),true);else
page_header(lang(82).": ".h($_GET["ns"]),true);Admin::get()->printDatabaseMenu();if($_GET["ns"]===""){echo"<h2 id='schemas'>".lang(315)."</h2>\n";$kk=Admin::get()->getSchemas();if(!$kk)echo"<p class='message'>".lang(316)."\n";else{echo"<div class='scrollable'>\n","<table class='nowrap'>\n",'<thead><tr class="wrap"><th>',lang(82),"</th></tr></thead>";foreach($kk
as$A)echo"<tr><th><a href='",h(ME),"ns=".urlencode($A),"' title='",lang(317),"'>".h($A)."</a></th></tr>";echo'</table></div>';}echo'<p class="links"><a href="'.h(ME).'scheme=">'.icon("database-add").lang(77)."</a>\n";}else{echo"<h2 id='tables-views'>".lang(318)."</h2>\n";$Fl=['sql'=>'show-table-status.html','mariadb'=>'reference/sql-statements/administrative-sql-statements/show/show-table-status'];$wc=db_collation(DB,collations());$c=["Engine"=>["label"=>lang(172),"doc"=>doc_link(['sql'=>'storage-engines.html','mariadb'=>'server-usage/storage-engines']),],];if($wc!="")$c["Collation"]=["label"=>lang(46),"doc"=>doc_link(['sql'=>'charset-charsets.html','mariadb'=>'reference/data-types/string-data-types/character-sets/supported-character-sets-and-collations']),];$c+=["Data_length"=>["label"=>lang(319),"doc"=>doc_link($Fl+['pgsql'=>'functions-admin.html#FUNCTIONS-ADMIN-DBOBJECT','oracle'=>'REFRN20286']),"link"=>"create","title"=>lang(36),],"Index_length"=>["label"=>lang(320),"doc"=>doc_link($Fl+['pgsql'=>'functions-admin.html#FUNCTIONS-ADMIN-DBOBJECT']),"link"=>"indexes","title"=>lang(176),],"Data_free"=>["label"=>lang(321),"doc"=>doc_link($Fl),"link"=>"edit","title"=>lang(8),],"Auto_increment"=>["label"=>lang(48),"doc"=>doc_link(['sql'=>'example-auto-increment.html','mariadb'=>'reference/data-types/auto_increment']),"link"=>"auto_increment=1&create","title"=>lang(36),],"Rows"=>["label"=>lang(322),"doc"=>doc_link($Fl+['pgsql'=>'catalog-pg-class.html#CATALOG-PG-CLASS','oracle'=>'REFRN20286']),"link"=>"select","title"=>lang(34),],];if(support("comment"))$c["Comment"]=["label"=>lang(47),"doc"=>doc_link($Fl+['pgsql'=>'functions-info.html#FUNCTIONS-INFO-COMMENT-TABLE']),];$C=(is_string($_GET["order"])?$_GET["order"]:"");$Ic=null;if(preg_match('~^(.+)-(asc|desc)$~',$C,$z)){$C=$z[1];$Ic=($z[2]=="desc");}if($C!="__table"&&!isset($c[$C]))$C="";if($Ic===null)$Ic=isset($c[$C]["link"]);$ln=($C!=""&&$C!="__table")||support("fast_status");$Il=($ln?table_status():tables_list());if(!$Il)echo"<p class='message'>".lang(79)."\n";else{echo"<form action='' method='post'>\n","<div class='table-footer-parent'>\n";if(support("table")){echo"<div class='field-sets'>\n","<fieldset><legend>".lang(323)." <span id='selected2'></span></legend><div class='fieldset-content'>",html_select("op",Admin::get()->getOperators(),isset($_POST["op"])?$_POST["op"]:Driver::get()->getLikeOperator()),"<input type='search' class='input' name='query' value='".h($_POST["query"])."'>",script("qsl('input').onkeydown = event => bodyKeydown(event, 'search');","")," <input type='submit' class='button' name='search' value='".lang(60)."'>\n","</div></fieldset>\n","</div>\n";if($_POST["search"]&&$_POST["query"]!=""){$_GET["where"][0]["op"]=$_POST["op"];search_tables();}}echo"<div class='scrollable'>\n","<table class='nowrap checkable'>\n",'<thead><tr class="wrap">','<th class="actions"><input id="check-all" type="checkbox" class="input jsonly" title="'.lang(191).'">'.script("gid('check-all').onclick = partial(formCheck, /^(tables|views)\[/);","");$sh=($C==""||$C=="__table");$Al=($sh&&!$Ic?ME."order=__table-desc":substr(ME,0,-1));$uh=($sh&&DIALECT!="sqlite");echo'<th'.($uh?" aria-sort='".($Ic?"descending":"ascending")."'":'').'><a href="'.h($Al).'">'.lang(9).'</a>';foreach($c
as$u=>$b){$Kc=($u===$C?!$Ic:isset($b["link"]));echo'<th'.($u===$C?" aria-sort='".($Ic?"descending":"ascending")."'":'').'><a href="'.h(ME)."order=$u-".($Kc?"desc":"asc").'">'.$b["label"].'</a>'.$b["doc"];}echo"</thead>\n","<tbody>\n";if($C=="__table"){if($Ic)$Il=array_reverse($Il,true);}elseif($C){uasort($Il,function($ua,$Ua)use($C,$Ic){$mn=isset($ua[$C])?$ua[$C]:null;$nn=isset($Ua[$C])?$Ua[$C]:null;$H=($mn<$nn?-1:($mn>$nn?1:0));return($Ic?-$H:$H);});}$ol=["Data_length"=>0,"Index_length"=>0,"Data_free"=>0];$R=0;foreach($Il
as$A=>$el){$Vm=($ln?is_view($el):$el!==null&&!preg_match('~table|sequence~i',$el));$ud=($ln?(isset($el["Engine"])?$el["Engine"]:""):$el);$r=h("Table-".$A);echo'<tr><th class="actions">'.checkbox(($Vm?"views[]":"tables[]"),$A,in_array("$A",$Kl,true),"","","",$r);if(!Admin::get()->getSettings()->isSelectionPreferred()&&(support("table")||support("indexes")))$wa="table";else$wa="select";echo"<th><a href='",h(ME),"$wa=",urlencode($A),"' id='$r'>",h($A),"</a></th>";if($Vm&&!preg_match('~materialized~i',$ud)){$S=lang(171);$Kb=count($c)-(support("comment")?2:1);echo'<td colspan="'.$Kb.'">'.(support("view")?"<a href='".h(ME)."view=".urlencode($A)."' title='".lang(37)."'>$S</a>":$S),"<td align='right'><a href='".h(ME)."select=".urlencode($A)."' title='".lang(34)."'>?</a>";}else{foreach($c
as$u=>$b){if($u=="Comment")continue;$r=" id='$u-".h($A)."'";$x=isset($b["link"])?$b["link"]:"";if(!$x){$W="";if($ln){$W=isset($el[$u])?$el[$u]:"";if($u=="Collation"&&$W=="")$W=$wc;}echo"<td$r>".h($W);continue;}$W="?";if($ln){$Ih=isset($el[$u])?$el[$u]:"";if(is_numeric($Ih)&&$Ih>=0){$W=($u=="Rows"?format_rows($el):format_number($Ih));if(isset($ol[$u])&&($ud!="InnoDB"||$u!="Data_free"))$ol[$u]+=$Ih;}}echo"<td align='right'>".(support("table")||$u=="Rows"||(support("indexes")&&$u!="Data_length")?"<a href='".h(ME."$x=").urlencode($A)."'$r title='".$b["title"]."'>".h($W)."</a>":"<span$r>".h($W)."</span>");}$R++;}echo(support("comment")?"<td id='Comment-".h($A)."'>".($ln?h(isset($el["Comment"])?$el["Comment"]:""):""):""),"\n";}echo"</tbody>\n",script("mixin(qsl('tbody'), {onclick: tableClick, ondblclick: event => tableClick(event, true)});"),"<tfoot><tr>","<td><th>".lang(295,count($Il)),"<td>".h(DIALECT=="sql"?Connection::get()->getValue("SELECT @@default_storage_engine"):""),($wc!=""?"<td>".h($wc):"");if($ln&&function_exists('AdminNeo\db_status'))$ol=db_status();foreach($ol
as$u=>$nl)echo"<td align='right' id='sum-$u'>".($ln?format_number($nl):"");echo"<td></td><td></td>";if(support("comment"))echo"<td></td>";echo"</tr></tfoot>\n","</table>\n","</div>\n",($ln?"":script("ajaxSetHtml('".js_escape(ME)."script=db');"));if(Admin::get()->isDataEditAllowed()){echo"<div class='table-footer'><div class='field-sets'>\n";$Pm="<input type='submit' class='button' value='".lang(324)."'> ".help_script("VACUUM");$fi="<input type='submit' class='button' name='optimize' value='".lang(325)."'> ".help_script(DIALECT=="sql"?"OPTIMIZE TABLE":"VACUUM ANALYZE");echo"<fieldset><legend>".lang(167)." <span id='selected'></span></legend><div class='fieldset-content'>".(DIALECT=="sqlite"?$Pm."<input type='submit' class='button' name='check' value='".lang(326)."'> ".help_script("PRAGMA integrity_check"):(DIALECT=="pgsql"?$Pm.$fi:(DIALECT=="sql"?"<input type='submit' class='button' value='".lang(327)."'> ".help_script("ANALYZE TABLE").$fi."<input type='submit' class='button' name='check' value='".lang(326)."'> ".help_script("CHECK TABLE")."<input type='submit' class='button' name='repair' value='".lang(328)."'> ".help_script("REPAIR TABLE"):"")))."<input type='submit' class='button' name='truncate' value='".lang(329)."'> ".help_script(DIALECT=="sqlite"?"DELETE":("TRUNCATE".(DIALECT=="pgsql"?"":" TABLE"))).confirm()."<input type='submit' class='button' name='drop' value='".lang(168)."'>".help_script("DROP TABLE").confirm()."\n";$g=(support("scheme")?Admin::get()->getSchemas():Admin::get()->getDatabases());echo"</div></fieldset>\n";if(count($g)!=1&&DIALECT!="sqlite"){echo"<fieldset><legend>".lang(330)." <span id='selected3'></span></legend><div>";$h=(isset($_POST["target"])?$_POST["target"]:(support("scheme")?$_GET["ns"]:DB));echo($g?html_select("target",$g,$h,"","label-move"):'<input class="input" name="target" value="'.h($h).'" autocapitalize="off">')," <input type='submit' class='button' name='move' value='".lang(331)."'>",(support("copy")?" <input type='submit' class='button' name='copy' value='".lang(332)."'> ".checkbox("overwrite",1,$_POST["overwrite"],lang(333)):""),"</div></fieldset>\n";}echo
input_hidden("all"),script("qsl('input').onclick = partial(countTables, $R);"),input_token(),"</div></div>\n",script("initTableFooter()");}echo"</div>\n","</form>\n",script("tableCheck();");}echo'<p class="links"><a href="',h(ME),'create=">',icon("table-add"),lang(78),"</a>\n";if(support("view"))echo'<a href="',h(ME),'view=">',icon("view-add"),lang(249),"</a>\n";if(support("routine")){echo"<h2 id='routines'>".lang(187)."</h2>\n";$Zj=routines();if($Zj){$Rb=$Zj[0]["ROUTINE_COMMENT"]!==null;echo"<table>\n",'<thead><tr>','<th>',lang(226),'</th><td>',lang(45),'</td><td>',lang(266),"</td>";if($Rb)echo"<td>",lang(47),"</td>";echo"<td></td>","</tr></thead>\n";foreach($Zj
as$J){$A=($J["SPECIFIC_NAME"]==$J["ROUTINE_NAME"]?"":"&name=".urlencode($J["ROUTINE_NAME"]));echo'<tr>','<th><a href="',h(ME.($J["ROUTINE_TYPE"]!="PROCEDURE"?'callf=':'call=').urlencode($J["SPECIFIC_NAME"]).$A),'">',h($J["ROUTINE_NAME"]),'</a></th>','<td>',h($J["ROUTINE_TYPE"]),'</td>','<td>',h($J["DTD_IDENTIFIER"]),'</td>';if($Rb)echo'<td>',truncate_utf8(preg_replace('~\s{2,}~'," ",trim($J["ROUTINE_COMMENT"])),50),'</td>';echo'<td><a href="'.h(ME.($J["ROUTINE_TYPE"]!="PROCEDURE"?'function=':'procedure=').urlencode($J["SPECIFIC_NAME"]).$A).'">'.lang(179)."</a></td>";}echo"</table>\n";}echo'<p class="links">';if(support("procedure"))echo'<a href="',h(ME),'procedure=">',icon("function-add"),lang(265),"</a>";echo'<a href="',h(ME),'function=">',icon("function-add"),lang(264),"</a>\n","</p>\n";}if(support("sequence")){echo"<h2 id='sequences'>".lang(334)."</h2>\n";$xk=get_vals("SELECT sequence_name FROM information_schema.sequences WHERE sequence_schema = current_schema() ORDER BY sequence_name");if($xk){echo"<table>\n","<thead><tr><th>",lang(226),"</th><td></td></tr></thead>\n";foreach($xk
as$W)echo"<tr>","<th>",h($W),"</th>","<td><a href='",h(ME),"sequence=",urlencode($W),"'>",lang(179),"</a></td>\n";echo"</table>\n";}echo"<p class='links'><a href='",h(ME),"sequence='>",icon("add"),lang(271),"</a></p>\n";}if(support("type")){echo"<h2 id='user-types'>".lang(111)."</h2>\n";$Mm=types();if($Mm){echo"<table>\n","<thead><tr><th>",lang(226),"</th><td></td></tr></thead>\n";foreach($Mm
as$W)echo"<tr>","<th>",h($W),"</th>","<td><a href='",h(ME),"type=",urlencode($W),"'>",lang(179),"</a></td>\n";echo"</table>\n";}echo"<p class='links'><a href='",h(ME),"type='>",icon("add"),lang(272),"</a></p>\n";}if(support("event")){echo"<h2 id='events'>".lang(188)."</h2>\n";$K=get_rows("SHOW EVENTS");if($K){echo"<table>\n","<thead><tr><th>".lang(226)."<td>".lang(335)."<td>".lang(255)."<td>".lang(256)."<td></thead>\n";foreach($K
as$J)echo"<tr>","<th>".h($J["Name"]),"<td>".($J["Execute at"]?lang(336)."<td>".h($J["Execute at"]):lang(257)." ".h($J["Interval value"])." ".h($J["Interval field"])."<td>".h($J["Starts"])),"<td>".h($J["Ends"]),'<td><a href="'.h(ME).'event='.urlencode($J["Name"]).'">'.lang(179).'</a>';echo"</table>\n";$Bd=Connection::get()->getValue("SELECT @@event_scheduler");if($Bd&&$Bd!="ON")echo"<p class='error'><code class='jush-sqlset'>event_scheduler</code>: ".h($Bd)."\n";}echo'<p class="links"><a href="',h(ME),'event=">',icon("event-add"),lang(254),"</a></p>\n";}}}page_footer();