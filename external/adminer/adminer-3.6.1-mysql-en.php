<?php
/** Adminer - Compact database management
* @link http://www.adminer.org/
* @author Jakub Vrana, http://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 3.6.1
*/error_reporting(6135);$gc=!ereg('^(unsafe_raw)?$',ini_get("filter.default"));if($gc||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$X){$Vf=filter_input_array(constant("INPUT$X"),FILTER_UNSAFE_RAW);if($Vf)$$X=$Vf;}}if(isset($_GET["file"])){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");if($_GET["file"]=="favicon.ico"){header("Content-Type: image/x-icon");echo"\000\000\000\000\000\000\000(\000\000\000\000\000(\000\000\000\000\000\000 \000\000\000\000\000\000\000\000\000¿\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000ˇˇˇ\000\000\000ˇ\000aN\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\00031\000\000\0001\000\000\0001\000\000\0001\000\000\0001\000\000\0001\000\000\0003331!31\000!\000\000\000!\000\000\000\000!\"\000\000\000\000\000\000\000\000\000\000\000\000ˇˇ’\000¿ˇ’\000Ä\000\000Ä\000Ä§\000ÄÄ\000Ä\000\000Ä\000\000Ä\000\000Äˇ\000Äˇ\000¿\000ˇ\000˛\000ˇ\000ˇ\000ˇÅ’\000ˇˇ’\000";}elseif($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("\n1ÃáìŸåﬁl7úáB1Ñ4vb0òÕfsëºÍn2BÃ—±Ÿòﬁn:á#(ºb.\rDc)»»a7EÑë§¬l¶√±îËi1Ãésò¥Á-4ôáf”	»Œi7Ü≥ÈÜÑéåF√©ñ®aç'3I– d´¬!S±Êæ:4Áß+MdÂgØã¨«É°Óˆtô∞cëÜ£ı„È†b{èH(∆ì—ît1…)t⁄}F¶p0ôï8Ë\\82õDL>Ç9`'C°º€ó889§» éxQÿ˛\000Óe4ôÕQ òl¡≠P±øVâ≈bÒëóΩT4≤\\ûW/ôÊÈ’\nêÄ` 7\"hƒqπË4ZM6£T÷\r≠r\\ñ∂C{h€7\r”x67Œ©∫J á2.3êÂ9àKûÎ¢H¢,å!mî∆Üo\$„π.[\r&Ó#\$≤<¡àfÕ)èZ£\000=œr®è9√‹jŒ™J†Ë0´c,|Œ=ë√‚˘ΩÍö°Rs_6£Ñ›∑≠˚Ç·…ÌÄZ6£2Bæp\\-á1s2…“>éÉ X:\r‹∫ñ»3ªbö√ºÕ-8SLı¿Ìº…K.¸¥-‹“•\rH@ml·:¢Îµ;Æ˙˛¶ÓJ£0LR–2¥!Ëø´ÂAÍà∆2§	mMT7ÛåÁS’5]2Ï√Ó‰Eç)ÿ»£Ø≠Ê8\rÉÚ*`‹Ø.i˙Ç6Uıu*—è›”¥ìN÷«Ôe∆’◊U&¿MÑ˝DÕ\n†·#∆œÌ%%∑W`˙4ç£8˜j®	Ω≥XáCk|2Ll©Œ\r˝˘)É¿Z;∑”†t¯æC√7âTÒJCå?Í¡NŒƒ<Ì°nÛ: (TÖ.+ùU9eàÛóÆí[US56ÓeWíÔzH≈Ù®®Ë-éÉ»‡2á∑‡¬3å¢Ì˛ñ⁄ÿCÇ`Õ>ÖÀö»èÆ®PFÍÜÛS À¡ºﬁ\rCù&4`ı†X1[ç'∏£„b	eYñtWi™õÜ‰:C®⁄7cÄﬁ≤0ÍåâÈJıì”ñıGR´ˇV“|Ùurç¡l™¥•-º\r„ª\n8f{ª◊=ÑÈ9åùgiyé˚¢Ã£4òt8.¸/wôè}Í 2¯ã,Ê√{ZäÆêäªåêÂÅîc3øÈ^†@1d¥’Çç#êÆ'≈^Ñ[∞\r£(Ê°Íõ(tä;√wµÃh˜\nÙ»Kw~/Õ©§g6J‚?«©¢Øv2éXcà\r5˚!0nàs`%Âö\n¡rﬂ¯nqPVÑh@ÿP)§Îß∞·Hπ-]üê∞å\raº7p≠Í	0÷Y›ãÜRaçSë(ÄØôÛÁk\r∞˜‚˝ã!<Vœ©6†nfõ\000f\"·ç»™≈\\÷ ä|9n–ƒKbÒÄåπè8X6Oó‚FSÒÆ1G\n”Z{…b°†Û™–v≈#-(7Öƒ“ÿxpr-»£¶π§|>1˙@H)	!ú)'‰ı!f<Và;ŸlÃ8¨“ÓU§¨| ·∑î…*Lπ˜cLu>üfBﬂå\"qÆ<≤Üì‰»;ïÅâ&Æ»∑“h Fi0∫pdkU!!*r¿Éï-.†uŒc©¸j◊\"H3Ã(ÿõ3j°ì*s¨™D∞ƒ√€:AòÑr‡›_–}d Œá0X“Eû)ﬂ¨∑úä›ÿ;ûS—÷É©mgl¸*\r»ôPàò(£Â{jpç¢9ˆ√9/∞—Ûòßûú^Ö•lÆ¶•†ï95é”u ©Ûa4æ*'lñ≠*TCëº!N8®`ÂDi\"g%Õ˚B˘ÙAï© há≤gÆ¥rê›sÜ^ØÉ·1hâ0_Œ9»S™;Ê	fô≤% Õ⁄Td¥EDû”GTÁ–˙Rú20‰‚ü%yî·;7^~e„ë¨ÓRµ:rß[™¡≠§èq†◊&?4+Ä-≤∆]s’£¯{ÍÀ§=≥\$˙À)⁄c†È÷¶∆Ñ”ìIäÙTi©Åh\$4ÁkbßRuµÉ¶⁄zamNaŒ\"í¨È˙a\$ Ÿåÿä b∫ÕìexÜ–ﬁÅhu(Ê∆Fò42·Xb\reñÏ›≤åNÓı)ù∑Sî≈'@SvJd)C•Ño‚âs≥” ¯Ä«P*gW:^À˙–W+T˙ΩóIówº]√r≥ûìπ,0õ\000rÅ∞¶gè§∂7Uz·¨8æ&UT\\2‚ú6§‡\000r≈ÿp@7Ëë≠F™˜7‚GT5R¥UT¯c£>£O‡1òµ|˘ØáäÅ{W˙¿∫›C√x;é¨^–Œ#€iRb:«∏r}÷,fp%»u ◊*áHLµ'ÿ`@~&å0Õ≤kT≥*èü˘§=∏ö≥ÌÑÌ»\n–æ‹ä›Vk’| ëõ5¶#.ÍÏ3µà:E€ápÓù{…w÷\r‡ºÙXtË}");}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
lzw_decompress("f:õågCIº‹\n:ÃÊsaîPi2\nOgc	»e6LÜÛ‘⁄e7∆s)–ã\rè»HGíIí∆∆3aÑÊs'c„—D i6úNå£—Ëú—2H„Ò8úuF§RÖ#≥îîÍr7á#©îv}Ä@†ê`Qåﬁo5öa‘Iú‹,2O'8îR-q:PÕ∆S∏(àaºä*wÉ(∏Á%ø‡pí<F)‹nx8‰zA\"≥Z-C€e∏V'àßÉÅ¶™s¢‰q’˚;NFì1‰≠≤9ÎGºÕ¶'0ô\r¶õŸ»ø±9n`√—ÄúX1©›ÅG3Ã‡tçee9äÆ:NeÌä˝N±–OSÚz¯cëåzlé`5‚„Å»ﬂ	≥3‚Òyﬂ¸8.ä\r„êŒπP‹˙\rÉ@ç£Æÿ\\1\r„ Û\000Ç@2j8ÿó=.∫¶∞ -r»√°®¨0çäËQ®Íä∫hƒbºåÅÏ`Å¿éª^9ãq⁄E!£ í7)#¿∫™* ¿Q∆»ã\000äÿ“1´»Ê\"ëh >Éÿ˙∞––∆⁄-C \"í‰XÆáS`\\ºè§F÷¨h8‡ä≤‚†¬3ß£`X:OÒö,™á´⁄˙)£8ä“<BN–É;>9¡8“Ûácº<á*¨Ê2éc•9œÈ >¢H¿zôOj™B'B™˙™éä∫≤å5ë,ÚÑPÏb5–45Ä÷3Ïˇ@ÖÅ:∑N+iöjõ’J¢ü⁄ä\\™é	®∆á·@º>ã†∆4Xr(QrèÅ RŸ° dÛÖu=œtÕA8A{åc\\äß)≥Ω|◊ÅC4\n6ÿWÌò7(V4l6µ	ñ9\r°vÀéaﬁ&:CKòç!Ê-°£pŒ:\r\000V¢M†QÜ#ıK@º\000“.ÖŸÄÀVy∑õ¶wE˚\"’„fÉ|jﬁbgŸºF>ƒã	BHnñ›∫Z¢‘B≤B∆\$…F0ïœ√=™ÒkC-9ç„∏˛C¥‰âOîç9^Z3\r«rÖ7‡÷0Í®uŸw˘ïM∏g˘≈∞†’v2∂‘qI∞ºÛ≤ÏˆpÂµ¿∏‰h5c„Py«ÖŸ.Êç[∞˜•hV'-çY¿Ã”T∞Ë”Ö›õí:v–˛ü®O&&6ä®Zó·»Œª	rn˚∂ï•æBc£o⁄ Õ0˙MÉ„£§≤xz]‘åô’çÍ«!ÄvÈdz/sÉ‚€CåÌë¶=ıd9KπeVXŸs:pÃ—à8ñr√A0&iÆ)÷§R\$˙Y_VÈ4¨æÖz˜»;ia∞4∞† lIÁÉãÙûÓqÿwlM(ßî¿‰·b)O&êÊ√xl&°¿√ìÿF`˘óUí¶Ç◊√ôÌy∆]N©PAx^+`êÜòLî™’gå∫(ÇÜ“|8Å¡,áƒË°Ë)äëY-*h∞™XkwT·–<°†BQ[Úú=´ÖtW„»on¥»@c›d“BÉ◊Æ»}U ¡>+5jW‰õFwN’&Ÿ6Ô\$Î o1~™◊©√\\	'“†¡=4@•D™hƒR,DFÎ≈+ÁIi êK\$Åêr≤#»áª\000¿!&Oö>¡BæÀ¶)Ü(·ò:∞ `LOóØà2&	∑Sk:(ì.8ë4h˙	‰ﬂ_\000˛u44⁄˙—[+ßE˚Ë»\rB…\$-ïf,Á\000CO;`âoÕ≤G>x£YSáIø@ŒAÌ_¡ëç9÷⁄˛Â	üÌì?÷ÍˇÿÏˆK ˆç3dñÀ®‘'sç£L∆]CY3-§Fbpÿ-.UDt¢ôîFäò(’>¢P:dä`»êb;°Æ@:⁄gí∂jJHP˜P— ºx\ríPêhl%‡∞4V\000ÿÚN àO5˘G†KàÅSöû¢÷Z¬Q	IveÏà≥÷ç>É¢¥)0‘7PêhL(†ò7 ÊŸ†™ÜøXK\rb,Uv\rêI®¢bï®P\rÊÊÃs§-SŒ‹áIìËUC±°Ã)•x9LC›®pF	´í∞-‘≥¿)=íI¬ÉAU¿≤‚ÜY!„ê!Üƒ‰'ãö\n`M◊w\$2Ü`VO.lw%0\\”Suì¯eß@ß©√‡œë◊p3€È¶%!‘'úÖô1V0aUÚ!π9DﬁBaçp0™ÜÕ6\r\r·ò3pË.ËtxK\na`ÆD@>¡∂¡\\'ÖI.å¢ShËCP!»«¿;î[bn\\\rí≥™˛˝ÑÙ≤±l™•8¿7„,hQC\nY\rˆJ¸£XdC d\nAº;ÇáfF+ÓWw–4LPÔ…ªHAµ!Ù;óâ&VÃ≤yÉ¢65öÂzÁÕ=ı£CÌçr“mƒä’i`ÀâbÒ\\gy‘ã\$#QùËmt–s«\\‡rsƒY˚≤\000y°®„ß£‰KC@i≈ßZ[ãyp∞¬C–]Ål.@Vº8\$¿≈Njpõ:Ic·ˆêœ}ï4Kï†é^É˙4ß)Êæ”8≤\000S4Äƒ‚§°ohR£®√\nﬂo:õTj≠Y´µÜ≤÷ïÀgmFpóJÈE4Oj∏µ´∫çfpœvyòP¬ÜP€Ω%Ÿ›Ô\\3ÚGÉpM\rˆŒ÷®-‹√g6ÖOaπà¥jŸ∞ß∆ås6?/i&¥´!‚ú3e∑GU7¯m\rŒ6¢˛Iñë@ãZsì¿nN¡?!\r N£-7ó√„≥ô5!B¬*πŸáı•Ø.Û…a.˘Á”œ√Búï|®°o§Hv·ªÌËΩâ£MˇX‹ZãÈnƒ‹hÔ§”±Æ≥I∞8/HQVˆ'rÌ%∑M±g\$\$VóÅGvì˚ËîIôRÉ(a©çBîNœ«HóÇÔÈ7%ÂPÅﬂíw~S.g9Z{¥ﬂÔOºéá??ìP¢ö¡ÊÜ‚{!¡bXJ§«W'Ä‚hrqDê«@√¶ö\$9œW6fä˛0‡§î@b˝‰B˘_0°É–b.¿&ôXMYØ~‹B#Í„ÇnÂc1£ç&1S>È…}—%¡‹≠’˚o‰’Jº≠eﬁ»ÑÜ‚\$˛\"Dx`∂ˇÇqIXDb0≤ê\000}Ê(0¿ÍCÜTƒFèÀ~ëÈ#Ç≤™+ÑWÔR˘ià∫!Ï;	£\000¢âî≠¨ıËÑ¯èåöNHi¶˚¿Ú˚0éÑ4(É«\"ËäØ‹çÖHpF˛iÀgÜ¿¢J(†æj¨º\rc@¬¿®\r\">‡eLÃ/ˇ∞ì\000\r‡†X˚´ÇH†“ ÜLıo[àB®∞‘\$O¶V¢àÄa ]\rÔö†f≤¯b∑È∞˚I¢˚Ø ¸&æ¸`¬ıojàêH¿êLâ¢˜œÄ–É¶∂ ü\r∞ÍöÔ© `)ÇˆN´jj™`ÍÉÎd∂ë(d FÖ]mÍ>â*∞%jH1NÍNo0õ(¢äoﬂhÊÿ†RLh±eL•YË§L‰®ﬁÏ≥\"|ì¨JH@)+D¥Éuë^ƒ†Sœêà©EkãB¥pÀ0G¶xbC*Ù–&ÇÄA≈lö{q∞– è\\ò•ö8ÉCÕi\nb⁄*p1'∞V“LvxPƒÃÏæJdX ÇHP\r§ÇÖ≠¨[` [≠≤\\\000ºø-`ÈÉœ≤(FÓ˛EƒÑãf•\$\n-†ö √“ß Úª£à®í`6`œ&rjÄ–§äf¿€&ílt‡˙•\"™\r¨ä†ÇJ£fˆC(‡t∆PSã&Æ1%Úã&2)Ñ√ò¶IÔ(“≈&Ú{&R”)R,Ä¬\rRhÆ‡ƒX\rÄƒ€ ÷.\000%å*†‚+•J'àNI\rÄ´‡Ë00ÜÂS	ì1ÄÀ1∆f t\nÖ \\#\\6 ∞	‡ƒ\rDo2¯S@ ìE4ìL cƒ	®:>¿Ê¬bE2k\nÄ†<‡uEX¿Û0DÏô≥0\$@ﬁCO\\2˙)Ä†	‡¶\nÜ\000‡ã:\000RíÍÆF/˝8ìÑ0¬K1≥Ñ	)-\$Ëp\$#.†®é¶‘Y≠Ó[®:æ.F\000Z?¿ÓßÜ≤Í\$Ã) …+SÉsºì¡sƒ8#d<@∞¥\nT§ä¨?+%õ7´2”äSîT©^;§,T£ŒLæë–ƒ7f]C\"ECÄÛ É(.ÚÚaTLi‰ô8≥ºIîPëﬁ∑¬≥D–6`í„&àXäæ¨2Ë¯¢%!4~äPL√F*ãDÃ\\0fX”¥¿dX<Cƒt†4ÎP·Ä \nè‘3‡RÏÑh˘N0 Û¥”äº¨¿P˘R¯PÄ∂˘Qå≥ç˙W‰ÑÜ™∏Á\\ãû2Â˝HÂ	Ó˝\$ÍOÄ√ffâ<Úi;«ô¥tódi,àEä†M£à≤Îi/‘™‘L\000^éFÄu(aäœJÇypZ;¢Ú¯º˝Bòåı`\ré‹íŒ œîı¥DÉ®±	8iä≥:ÿ	äôÒl¿êÏ˙†dÔ¡–\\ıpËqÌ˚ÄŸêÈ—ˇı0∞£d∞ÎµJEà%Kt˜æLÊ	Ï:\$±\$›≠Qo7QØ∫LTdÎ<‚Ú\$5Ì[£ãLÍdä»L`ÑÏW(rfe—IUÓëÉ.≈FPΩ`hÄd˚>ˆ¿Ëœ/Ï®5\000bŒ§Ω#“@€UB‰`Œê∞è∂:∑ïlK%∑c†zŒÊ3)fÒ^tÄÍ\000`#õ_Æ∏r«0(J†2∞\rfÚ¬õXœ;HÑHC_/ÏÊËÑ%iÀ‰:Ø/ §Û“Œrç)‡®\rÎD8à‹¿ñ\$ ±O¶øl†Ë)ËPÏÊVckÄ»≥‡æklæV∫66æ¯\000p‚a]VÛ@`äI¬\$#≈p†…o§ê9◊\$Çß\\˜∂ÙåÓ:+ıÔS5æ≥\r·^Ûﬁﬂ):!Íge∞ØÙoŒ(ÄeHÒ·I PΩÛ‰ækî3‡LRs¯V†™\n@íá\000€K£.ã’0\"#>(∂QDtùD≥/;¥<W\"ËŸ≤œf‘∂Tî∫0ˆˇ`RŒúW2¢Ü†ßrãf’·z÷]{É|ÜÖ£_ER\000YG|ìºÜC_qW‘c&]UHë|œi7'jVõ~ƒj√k2'oIo`ÀqCƒŒU‘JG6ÄZ•5‡„†V∑X0§ÑÛÚﬁ ‰Ñ	Äﬁ8.*†P XÉê#Ñ86Îpk0 !£8≤◊7Sc§æòVæÏs@Â\\>Ç°ÃæÇ\$¡≤õ)Ú¢J“®<KÈC[.TîæóãWîù~éå\$ØÄ≠Ê–üÊ‘LòiÜÀøz+ ◊®˝Q%fF?ãWßKÔ’dMHo%ƒ\\è¸t`‰ˆOe6eeìÅc†yÑPÀá±\\VQ`∞x~6-ıÉ™†ˆ¡∏`æòÕ\$&Ú’†Väòñ≥À—r WX\"Ω Wc‰∆xg[ÿksÇÇÇFû…©ã ìèW±Èi≤\n\nÏ∏≥‰UØ{ï9VOÛ‰Û™œÜÀ≈æ\rÄÖÁ™\rÇC§\rKf\r¶>„ﬁ>\$Úµ„Ó#àr?c˙?‰'`‰@ÑÓE Ä∆„@ı*848&87û®πÖZYã8ix8Ä◊V”Uò\n≠£ƒ∂†*yÂ#¬2<l^TÖ–\$£Aû∞p(eú˝@^boüπ’vK∫πÀòÇŒ√@O°bg†Ø3iÜæËˆÄÿ∏â<cŒ‹ƒ199t9Âm£¶:£‘;ú&y›U√«§ÉÆ÷∫XHc€°Yáú„?¢\000‘p£•È_áse&]XhU°b%¢¸/ øûCP_P∏Pö&†‹\r`D™# D¥î6B™@D4!.§∂`‚\rÄø¨\n™;∫¯∫ø¨-Õ¶U§⁄º0⁄¬¬⁄◊¨È!Æ	!öÎg≠bçB†t?Çí“å.z¬&`ﬁ\r†^B\000^πRé‰e&A#^?b@‡H˘C@¥‡œ¨Çä¨≥ØπûÖKtÄŒ4˚@§B\000Áõ@W±¬?wñÉπ¥uá≠⁄–>õ/¨ æk¨∫ÿ\reÆªq¨ö◊∑˙¬R˚)∂€≠˚Ö¨ªÉ∑#>+ÇC\nã‘,¿‹v`Œª&öÎÊ¨¿F‡™{¨÷å®\000]°`ôùWõ®ãf! €®˚Õ∑{À©ˆg¢é‡–ÉõËÄæR∏V£ù†dC8¿›EñF,ªD:|oªßÑäÇÕw¢/(‘‰ëy¿2<84SöÊv”öt ⁄yÇ⁄&≠Fø∫;tÙE˙_î0ƒ`¶Ãå∞ ∆|Æ~≠(m¿y	6ä»Â\000MBÍØ.Uy#¶q.w≈y˛\nÄ‰\n%«»Î\000Áäè˛@Ú≤π˛'úùäïb˝QE…<ó+VÏËvLX.C‡„àÉfø¿…Õ#dq√íY^üú÷Î#dY¶“†6d¢≠\$m∂>m„í„´◊«GV9.\\ÊÕﬁo:…†À¬ÜgŒcêÚ©Ê)Çé \"îΩºTÉ˝U\r Óı–mÜe’~öCiè¡5Øp¯¯ΩT¿ï¥öË‰˘11\000z(Âl,Ü\$n5¯í•C◊”◊˘}É◊„™á\"G=qÿËukÎ!vm⁄ﬂÇêT≠\$\000YÿÏ3‡∏<≈)∏}™+hΩ∂®\r◊*}Ñùt≥›z!›µíΩ≤ç4:ì‹›ò\rùúG]ÚH§ôGR™©0™≤ª‹±•ØåÑÎ›Ô#¢óÀ}÷´ù›ﬁÜóR˝∫4H%îÊøY7TÁír¥~.~|Ù‚Ê=œ¶EÆºr”jã&~Ω`∫(ê\000îŒP`œJïØÒH%„2Á±·!ŒxÖº\\Ã‹Õ'ÜX¬–'ñR◊§≤5ì¢Õáh-VæNÏ¶B§∑/ÂÕìÈË\r^ê tå˝TÕß«µ!π~j•È|q\nêÀ“Ω◊Ë[Í·ÍN\"ÎÓ&“E¥.	ÁÍá.\0005≈ÍÁÌ∆Õ1ÂßUÓàdG^Î»ü#.˝⁄rRFQ%(‡Ë\\‡zã•<6C\neÉÚÄ∫p£FF?.\$GÃ¿ˇ4\\˛ï*ı†–`b£?8‡∫ÙÊ:D∫ë‹áˆüm˜\000NKøv‹˝¶¿„ÛLÙŒI(pEÆDi≤ä\000ò	 ñ†¯\n@ã:@∞\n è‡˘¸\000(ø´˙|ƒ∫h\"+* P˚Ó‡J<ÊyÌBª‡c‡£ÚL∫\"^‹ÛÛ∆\r‡¥|Ëäå¯GËÄ∏eÅa}#ÈÄ˙Ñåô%∆`¯ÄŒ‡„Å´,¡†úÜ`ÅÅÒÔÀt˚tñÏƒ´@†8Ä|w\\&@0¸åÚoÄHsÔï,˚>Ã0Á~g√Ú¸tÏxÆá2\000~Hó¯¿l\$!K\nhüå}gçíåá(»rXlüør∫ä\000‡l~a\nF¸˜¬åÒˇÔÎŒœ≥@@Ã\"N4)_ôBË¡n¢HÇ¸&uºh™i	@W¿RJ”)¨@'-àˆ«¨)É\000˝\$`‡ß¯(†.\\qïq¡ç8¨),v»Ääò+âtå Îu ¢Ñ3‡_òü¯©`Œ\000Æ¿|}¸,@ƒ˛¿9?∏˚sxM,õ>H,sπ†ßQÇ@¿E°Ω›·\na]\000ÄKfÿY \r`\0000%iîGXC1ÿ\$ÙÉ5ãúFyHÎ∏°äÌuî¿ÄKEk»\$ÂQ·Ω%–‰åÇ>-≥˝˜m\"N9>\n†û¡â‰c%2Ò÷ÆıKCë{lÙ?N–i†Ä¥b∆Ä ∏`ê0\000¢\"}DCûæÌ8 ÄÖˆÑr/Äí ‚åy\\A◊àzƒ@d@òZƒz\$±sÚC\rAp@*¬Õ9IƒS¶?èà\n¸°Ç&^ø	ˆËí9ê_Ñ!%çeäÅ®áÀ≈FC¨êlåU«ëp\ní+â'|@Eá@>°—\r¡r„©¯⁄J°1ÿ\$¢ß2ÖxFbﬁ80BmÓùÑ¯aB\"»Ø4á2^ê\"(æ≈¸‡–våÜ ã8ì∏I» n\$Ac\"04ÄÓ(≈S)|eÉ¯Ä‚Äx)¥gÉVœÛ8ΩŒÖ[t¬!∫e)Pz]Ã®\$Uî#¥éQ∂∏ëHgxKÑÖ,ëº*#œ†»(äã¨’–‡ôêƒpq≥…ˇM¨“(“?⁄ıäx@	)√ô⁄–Å€Ìb†y\000GÅx@'o\000tåÓÁ=Ú6év€HåÓ∑D–œ∆Ä*ãHÖ*kÚ=‡ÎœT\r¯êëRhˇ«‡ ãzêƒñÑhÚ)ë∆•è˚÷«U ìOV?ÚåN–m@Ä‰œ∏x\000Ó°?ÅÃDçÆ§64	Üçs)Yâ∞\$˜©ΩZA25ºÉ†h©=T¶rS9=‚ﬁŸyõB,ßq¥Ö_?ÒóHº⁄.>íÄæE´n§9#‰ÜÄ≥`<;ﬁI.)¨§Ω%0ù…I#≤à\$E\"AxHúÓ&´OÄ¯gò¥;`|+—íUÆ.Ú…™Gıìó›¶Ö“.I–À†s¸ü@Î'˜Ô 	Ú)ëX–	—†>»l⁄ßùpBà≈[\r¬áV·î“ú¿˙C`\"µ¡≤+qwD\nË˚Ä∞—ùwb,‡ÇÛÖ)Ø9e“ú	\"ÆŸ)ÙsåöTQ™]=Ã‹Rô\\#∏+ÒÍ.¯	√õ*Ä;«√@+w¸4^Ñ‚∏◊8QâN\râîÃ´AcÂ¯áîuf ∏z∆“Mic;aŸW%≤±DlYßL @∂gò}ÖNà≠cËX˛gï»πƒYqÛ—@VW'\$Ä!E-\000^ﬁ0Ùµí&¿tOZVì\rπ`Åûb≤˛ñ#?·≈!®Ä\n<N‰vƒj!÷î Ä⁄? ùÅƒ©Ÿ/¢ã>&Bë\000BK…J7\$Ú¥F¢f¶är!\\Õ¨x`°C·hØªVk¿1*U P/9)/f6}ëpÕi#öY%®Ò1†\$Å`Ifòã…ßLΩ·=t◊-5ıDÕacFY≤“`—6Ö ÅPÚÔ_“NBïåÍãîiwd¸I¢önhC	–Öwô¨ã”4qåﬁÄNπG+^orõX—…‹¯2L˙'Ûéÿ~Bj'ò}ñTÂ·Cö1ó@‰®@:\":l†möËù¶¨@B,)Óì\nIÃ vãbñ∏˙sÅ\r»[Ñã:©ãd'lh[ùÿq:êç†<!≤|äY6Å√Œ∆	°Pt§Ìñï7ÉÍŒp ”}pàgÄJs™O÷ö∏ùc°=9⁄I»±≠ï\000…íí2Ä#.ö`!TQ#ç‰ç≤KërlìföÙÎ‹*B‘ï êœE∞E*'Ànsª´ÃÔB¨t7ÿƒÒ  z 'öÿ@[È\">˛!O…çs‹ÇxE¿(S4\"üÃﬂ˙˜ıÃå“@1c˙·ÿ∫∏∞\r‰S,n`ïD!2I∞)¥4j› ù<Ò§âµs‚Hc¸k•Ùg	ÉÄ∫a!5ò\\!¸\n—&âar\n(qn6†ò√pÙÖ≥`¥∆!ª>4E¶í¢2e≈A‘Vûî06\"¢gõ2%Tg%°=[R\" ¨ïëf∞sƒªÕ™6áyU EõCÄ–ˆáÍßá%àm0™*Å∫äÙP¢’#ãD…ÜQ=G§¡É∞¿—!ÙFŒn„√ñ~£§áä5…¯3[N…#y\r¢>≤K8À\"Zvß(ú¢ñ\rr¿S9ÿ; |3§DÏß©;T≠9åV,ù\rxJÂFd”ÑVôÅÛ/@^  Å9:@|~™s‡Ø@Àt*hÏ†¿OL¥s±q˜á)ûÈ)–'öjœ,Ñ 4å@È ®úÕ“Œ!:-) ›Jös”Jù nkùƒ…:2T\nûXüE>a¥‚∫wÜæXgû-1ùå©e\n\000|ì®8|˜√A\$ ÿj=Iq6––\000üï®3âhà∫~∫WÜ`îÛ·‹∆102Ö¥\r—∞ñÒöå±≥8TŒWƒ3î©„›Qzï,œ1Ù(~™>+S@ìÕFÜëÂ?§K…L¡œs24≥‚“ÑË#[L˛¨&¢£Ú… ,–ö»÷ØëëÅDäkê—öÓ©\"Ä™Y?år&J!¥}ƒı1s!ÖIS»ŸOŒïBYMQ™,cZπ‘¯CıL®Ï~Í°⁄©Ω÷™¥ª;\000x™≥KG≈=÷B√ˇ´’k;!⁄≤ãÇØ¿s5[\"ç¬k:µê»Âˆ5€”°¸hä eÍ—q≥÷¶q+¢|Hr3Â{AµÎB7ö—÷î‹Ò≠BÈQRÄØ÷&Æµ-´ÀÍ¯d⁄— é\"¨RüF;9†Üï'›Rå÷Áj_UZH2{‚\$ÜuA%÷–£D¨ÿ±ñm2ÿ-æq0AWÄD´ŒFa¡\n™C¿3Ç\000°…VEëãÅÙÄ.Q¨\000∫±ùCÓ&∆ÈÆ∏@∫˚Å®\r·ƒ+!êJ—∆¬∫gÆ™]Ék≥X1„-Eß—£%t†Ì]RÊ*¡∆n|pçÁÏeÑåÀﬁB⁄ìeÄÏ\n,€√Í,é¢H≈=î‘C%ˆàﬁ≠Ò£C√Ä9S3œæL‡/¬+júg˙«‘Ü»œè9ïx§phÍ¨õö b7´G,.◊“W’ıƒ7ñƒyZ¡ıV∏¶–v≤µ,E5\$:_…(•&“J-óËˇfoŸå÷5≈_er+ï]˙^îπ´°å!õÏadÅçdÀ§à¿\000Û	óLP]	G8¡“>C\000!∞ö%´8Ì6qsÄËö2‡>qˇÌ4P∆ê¯d∫Ç¬dbs`öÄ;ò]™¬‘,–\\ñjÌò¢q–√i{⁄P‡aáñ·k@ÏÎ{Eë‹©9+ŸQ>`4¢H!§‘	·≈•kp:(.D‡/;”èmV¿-™◊(5∂‡Q\$vr®Z.Õ¥º;ë(Æ§æΩGf;}{JävwB”\rMWê∑È\\Ç±-¡§ÔxbëÇî⁄s∫ùºÄ≈q\narì˛˚Éã“ﬂ@Ñ1#∆Dﬂ«π°.Â\n5Á!p°†2t!±ﬁM«Æ&3ãëOxW≤ﬁë[k\000íª:‹®_}Ëç-¶@Äı–#Õ…»j&Ÿ6xπ@.à–À\000[\000£pŸï~w ¡ΩHÔp<Ü]ÖW0ÕŒV€¡?æVE›~nnó@‹˙â77}’C>›2›uï=ªl‚kv|Äπ`√(π’Õ¿cvp6∂VÊ∆[Ö’“çaGl\\‹• ~ÿ?u±òfÏ§‘ïﬁ.”r€µéfÌÕ¢M‹“áuÎæ^9iEI⁄êF~Dc ÜäÙßÛÆ}q[†Ä∫ıWDàΩ—Ëˆ,“Â≤^∞é qÓrêP»›™ÊΩú5∑oj∏^‚Íªπï∑nñu¢]8Ì◊ê7©ˇ”èx’ò›p⁄µıÔ# ˚…õVâ\"ËiY@{w·¿ô †1æ•!AKºÖ~˙◊â∂¢ÎÃ2ªª_ûÌ\000;ø]‹R}¿]b¬ó„ÛÔÂ~õwÑñÛÔ3Hp‘P0∑dπΩÊ∞\$;˚[~˜Ë1—œ-ÇJÜnáØÑ)JãÄ˚e[1'e}∂yÌ£rR—√Ê›U&Wë'*J®√—ßiÊ`[=}µ√/n1wáÇ÷[U∂∞r—%‡>Ÿpt∂iE~o6)ò8ëHj5Õ9,ÿV\000¨˘Aü\nx\nÓ¸bº‹s	∏)SëπÄT}uù`¯`Y–g»˜Ök¸¨ø8(_oÖ¸0·ãÉÍËƒBªÖ›.ÒwÀàj˚∏ËWqõõ3™Ùó®§É¿jvyπ†Ï0g´!§ÿ∞/\000ı˜cÄ∆Cz†Ò3D8≠c¿∏E&@1/R`s`€¸–");}else{header("Content-Type: image/gif");switch($_GET["file"]){case"plus.gif":echo"GIF87a\000\000°\000\000ÓÓÓ\000\000\000ôôô\000\000\000,\000\000\000\000\000\000\000!Ñè©ÀÌMÒÃ*)æo˙Ø) qï°eàµÓ#ƒÚLÀ\000;";break;case"cross.gif":echo"GIF87a\000\000°\000\000ÓÓÓ\000\000\000ôôô\000\000\000,\000\000\000\000\000\000\000#Ñè©ÀÌ#\na÷Fo~y√.Å_waî·1Á±JÓG¬L◊6]\000\000;";break;case"up.gif":echo"GIF87a\000\000°\000\000ÓÓÓ\000\000\000ôôô\000\000\000,\000\000\000\000\000\000\000 Ñè©ÀÌMQN\nÔ}Ùûa8äyöa≈∂Æ\000«Ú\000;";break;case"down.gif":echo"GIF87a\000\000°\000\000ÓÓÓ\000\000\000ôôô\000\000\000,\000\000\000\000\000\000\000 Ñè©ÀÌMÒÃ*)æ[W˛\\¢«L&Ÿú∆∂ï\000«Ú\000;";break;case"arrow.gif":echo"GIF89a\000\n\000Ä\000\000ÄÄÄˇˇˇ!˘\000\000\000,\000\000\000\000\000\n\000\000Çiñ±ãûî™”≤ﬁª\000\000;";break;}}exit;}function
connection(){global$f;return$f;}function
adminer(){global$b;return$b;}function
idf_unescape($Bc){$Vc=substr($Bc,-1);return
str_replace($Vc.$Vc,$Vc,substr($Bc,1,-1));}function
escape_string($X){return
substr(q($X),1,-1);}function
remove_slashes($ve,$gc=false){if(get_magic_quotes_gpc()){while(list($w,$X)=each($ve)){foreach($X
as$Pc=>$W){unset($ve[$w][$Pc]);if(is_array($W)){$ve[$w][stripslashes($Pc)]=$W;$ve[]=&$ve[$w][stripslashes($Pc)];}else$ve[$w][stripslashes($Pc)]=($gc?$W:stripslashes($W));}}}}function
bracket_escape($Bc,$ya=false){static$If=array(':'=>':1',']'=>':2','['=>':3');return
strtr($Bc,($ya?array_flip($If):$If));}function
h($P){return
htmlspecialchars(str_replace("\0","",$P),ENT_QUOTES);}function
nbsp($P){return(trim($P)!=""?h($P):"&nbsp;");}function
nl_br($P){return
str_replace("\n","<br>",$P);}function
checkbox($C,$Y,$Ka,$Tc="",$Jd="",$Oc=false){static$r=0;$r++;$I="<input type='checkbox' name='$C' value='".h($Y)."'".($Ka?" checked":"").($Jd?' onclick="'.h($Jd).'"':'').($Oc?" class='jsonly'":"")." id='checkbox-$r'>";return($Tc!=""?"<label for='checkbox-$r'>$I".h($Tc)."</label>":$I);}function
optionlist($Md,$Ve=null,$bg=false){$I="";foreach($Md
as$Pc=>$W){$Nd=array($Pc=>$W);if(is_array($W)){$I.='<optgroup label="'.h($Pc).'">';$Nd=$W;}foreach($Nd
as$w=>$X)$I.='<option'.($bg||is_string($w)?' value="'.h($w).'"':'').(($bg||is_string($w)?(string)$w:$X)===$Ve?' selected':'').'>'.h($X);if(is_array($W))$I.='</optgroup>';}return$I;}function
html_select($C,$Md,$Y="",$Id=true){if($Id)return"<select name='".h($C)."'".(is_string($Id)?' onchange="'.h($Id).'"':"").">".optionlist($Md,$Y)."</select>";$I="";foreach($Md
as$w=>$X)$I.="<label><input type='radio' name='".h($C)."' value='".h($w)."'".($w==$Y?" checked":"").">".h($X)."</label>";return$I;}function
confirm($eb=""){return" onclick=\"return confirm('".'Are you sure?'.($eb?" (' + $eb + ')":"")."');\"";}function
print_fieldset($r,$ad,$hg=false,$Jd=""){echo"<fieldset><legend><a href='#fieldset-$r' onclick=\"".h($Jd)."return !toggle('fieldset-$r');\">$ad</a></legend><div id='fieldset-$r'".($hg?"":" class='hidden'").">\n";}function
bold($Ea){return($Ea?" class='active'":"");}function
odd($I=' class="odd"'){static$q=0;if(!$I)$q=-1;return($q++%2?$I:'');}function
js_escape($P){return
addcslashes($P,"\r\n'\\/");}function
json_row($w,$X=null){static$hc=true;if($hc)echo"{";if($w!=""){echo($hc?"":",")."\n\t\"".addcslashes($w,"\r\n\"\\").'": '.($X!==null?'"'.addcslashes($X,"\r\n\"\\").'"':'undefined');$hc=false;}else{echo"\n}\n";$hc=true;}}function
ini_bool($Fc){$X=ini_get($Fc);return(eregi('^(on|true|yes)$',$X)||(int)$X);}function
sid(){static$I;if($I===null)$I=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$I;}function
q($P){global$f;return$f->quote($P);}function
get_vals($G,$Ta=0){global$f;$I=array();$H=$f->query($G);if(is_object($H)){while($J=$H->fetch_row())$I[]=$J[$Ta];}return$I;}function
get_key_vals($G,$g=null){global$f;if(!is_object($g))$g=$f;$I=array();$H=$g->query($G);if(is_object($H)){while($J=$H->fetch_row())$I[$J[0]]=$J[1];}return$I;}function
get_rows($G,$g=null,$j="<p class='error'>"){global$f;$ab=(is_object($g)?$g:$f);$I=array();$H=$ab->query($G);if(is_object($H)){while($J=$H->fetch_assoc())$I[]=$J;}elseif(!$H&&!is_object($g)&&$j&&defined("PAGE_HEADER"))echo$j.error()."\n";return$I;}function
unique_array($J,$t){foreach($t
as$s){if(ereg("PRIMARY|UNIQUE",$s["type"])){$I=array();foreach($s["columns"]as$w){if(!isset($J[$w]))continue
2;$I[$w]=$J[$w];}return$I;}}$I=array();foreach($J
as$w=>$X){if(!preg_match('~^(COUNT\\((\\*|(DISTINCT )?`(?:[^`]|``)+`)\\)|(AVG|GROUP_CONCAT|MAX|MIN|SUM)\\(`(?:[^`]|``)+`\\))$~',$w))$I[$w]=$X;}return$I;}function
where($Z){global$v;$I=array();foreach((array)$Z["where"]as$w=>$X)$I[]=idf_escape(bracket_escape($w,1)).(($v=="sql"&&ereg('\\.',$X))||$v=="mssql"?" LIKE ".exact_value(addcslashes($X,"%_\\")):" = ".exact_value($X));foreach((array)$Z["null"]as$w)$I[]=idf_escape($w)." IS NULL";return
implode(" AND ",$I);}function
where_check($X){parse_str($X,$Ja);remove_slashes(array(&$Ja));return
where($Ja);}function
where_link($q,$Ta,$Y,$Kd="="){return"&where%5B$q%5D%5Bcol%5D=".urlencode($Ta)."&where%5B$q%5D%5Bop%5D=".urlencode(($Y!==null?$Kd:"IS NULL"))."&where%5B$q%5D%5Bval%5D=".urlencode($Y);}function
cookie($C,$Y){global$ba;$ae=array($C,(ereg("\n",$Y)?"":$Y),time()+2592000,preg_replace('~\\?.*~','',$_SERVER["REQUEST_URI"]),"",$ba);if(version_compare(PHP_VERSION,'5.2.0')>=0)$ae[]=true;return
call_user_func_array('setcookie',$ae);}function
restart_session(){if(!ini_bool("session.use_cookies"))session_start();}function
stop_session(){if(!ini_bool("session.use_cookies"))session_write_close();}function&get_session($w){return$_SESSION[$w][DRIVER][SERVER][$_GET["username"]];}function
set_session($w,$X){$_SESSION[$w][DRIVER][SERVER][$_GET["username"]]=$X;}function
auth_url($yb,$N,$V,$i=null){global$zb;preg_match('~([^?]*)\\??(.*)~',remove_from_uri(implode("|",array_keys($zb))."|username|".($i!==null?"db|":"").session_name()),$_);return"$_[1]?".(sid()?SID."&":"").($yb!="server"||$N!=""?urlencode($yb)."=".urlencode($N)."&":"")."username=".urlencode($V).($i!=""?"&db=".urlencode($i):"").($_[2]?"&$_[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($z,$A=null){if($A!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($z!==null?$z:$_SERVER["REQUEST_URI"]))][]=$A;}if($z!==null){if($z=="")$z=".";header("Location: $z");exit;}}function
query_redirect($G,$z,$A,$_e=true,$Wb=true,$cc=false){global$f,$j,$b;if($Wb)$cc=!$f->query($G);$df="";if($G)$df=$b->messageQuery("$G;");if($cc){$j=error().$df;return
false;}if($_e)redirect($z,$A.$df);return
true;}function
queries($G=null){global$f;static$ye=array();if($G===null)return
implode(";\n",$ye);$ye[]=(ereg(';$',$G)?"DELIMITER ;;\n$G;\nDELIMITER ":$G);return$f->query($G);}function
apply_queries($G,$uf,$Rb='table'){foreach($uf
as$R){if(!queries("$G ".$Rb($R)))return
false;}return
true;}function
queries_redirect($z,$A,$_e){return
query_redirect(queries(),$z,$A,$_e,false,!$_e);}function
remove_from_uri($Zd=""){return
substr(preg_replace("~(?<=[?&])($Zd".(SID?"":"|".session_name()).")=[^&]*&~",'',"$_SERVER[REQUEST_URI]&"),0,-1);}function
pagination($D,$jb){return" ".($D==$jb?$D+1:'<a href="'.h(remove_from_uri("page").($D?"&page=$D":"")).'">'.($D+1)."</a>");}function
get_file($w,$pb=false){$ec=$_FILES[$w];if(!$ec||$ec["error"])return$ec["error"];$I=file_get_contents($pb&&ereg('\\.gz$',$ec["name"])?"compress.zlib://$ec[tmp_name]":($pb&&ereg('\\.bz2$',$ec["name"])?"compress.bzip2://$ec[tmp_name]":$ec["tmp_name"]));if($pb){$ef=substr($I,0,3);if(function_exists("iconv")&&ereg("^\xFE\xFF|^\xFF\xFE",$ef,$Ge))$I=iconv("utf-16","utf-8",$I);elseif($ef=="\xEF\xBB\xBF")$I=substr($I,3);}return$I;}function
upload_error($j){$md=($j==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($j?'Unable to upload a file.'.($md?" ".sprintf('Maximum allowed file size is %sB.',$md):""):'File does not exist.');}function
repeat_pattern($E,$bd){return
str_repeat("$E{0,65535}",$bd/65535)."$E{0,".($bd%65535)."}";}function
is_utf8($X){return(preg_match('~~u',$X)&&!preg_match('~[\\0-\\x8\\xB\\xC\\xE-\\x1F]~',$X));}function
shorten_utf8($P,$bd=80,$kf=""){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{FFFF}]",$bd).")($)?)u",$P,$_))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$bd).")($)?)",$P,$_);return
h($_[1]).$kf.(isset($_[2])?"":"<i>...</i>");}function
friendly_url($X){return
preg_replace('~[^a-z0-9_]~i','-',$X);}function
hidden_fields($ve,$Cc=array()){while(list($w,$X)=each($ve)){if(is_array($X)){foreach($X
as$Pc=>$W)$ve[$w."[$Pc]"]=$W;}elseif(!in_array($w,$Cc))echo'<input type="hidden" name="'.h($w).'" value="'.h($X).'">';}}function
hidden_fields_get(){echo(sid()?'<input type="hidden" name="'.session_name().'" value="'.h(session_id()).'">':''),(SERVER!==null?'<input type="hidden" name="'.DRIVER.'" value="'.h(SERVER).'">':""),'<input type="hidden" name="username" value="'.h($_GET["username"]).'">';}function
column_foreign_keys($R){global$b;$I=array();foreach($b->foreignKeys($R)as$m){foreach($m["source"]as$X)$I[$X][]=$m;}return$I;}function
enum_input($U,$va,$k,$Y,$Kb=null){global$b;preg_match_all("~'((?:[^']|'')*)'~",$k["length"],$hd);$I=($Kb!==null?"<label><input type='$U'$va value='$Kb'".((is_array($Y)?in_array($Kb,$Y):$Y===0)?" checked":"")."><i>".'empty'."</i></label>":"");foreach($hd[1]as$q=>$X){$X=stripcslashes(str_replace("''","'",$X));$Ka=(is_int($Y)?$Y==$q+1:(is_array($Y)?in_array($q+1,$Y):$Y===$X));$I.=" <label><input type='$U'$va value='".($q+1)."'".($Ka?' checked':'').'>'.h($b->editVal($X,$k)).'</label>';}return$I;}function
input($k,$Y,$o){global$Qf,$b,$v;$C=h(bracket_escape($k["field"]));echo"<td class='function'>";$Ie=($v=="mssql"&&$k["auto_increment"]);if($Ie&&!$_POST["save"])$o=null;$rc=(isset($_GET["select"])||$Ie?array("orig"=>'original'):array())+$b->editFunctions($k);$va=" name='fields[$C]'";if($k["type"]=="enum")echo
nbsp($rc[""])."<td>".$b->editInput($_GET["edit"],$k,$va,$Y);else{$hc=0;foreach($rc
as$w=>$X){if($w===""||!$X)break;$hc++;}$Id=($hc?" onchange=\"var f = this.form['function[".h(js_escape(bracket_escape($k["field"])))."]']; if ($hc > f.selectedIndex) f.selectedIndex = $hc;\"":"");$va.=$Id;echo(count($rc)>1?html_select("function[$C]",$rc,$o===null||in_array($o,$rc)||isset($rc[$o])?$o:"","functionChange(this);"):nbsp(reset($rc))).'<td>';$Hc=$b->editInput($_GET["edit"],$k,$va,$Y);if($Hc!="")echo$Hc;elseif($k["type"]=="set"){preg_match_all("~'((?:[^']|'')*)'~",$k["length"],$hd);foreach($hd[1]as$q=>$X){$X=stripcslashes(str_replace("''","'",$X));$Ka=(is_int($Y)?($Y>>$q)&1:in_array($X,explode(",",$Y),true));echo" <label><input type='checkbox' name='fields[$C][$q]' value='".(1<<$q)."'".($Ka?' checked':'')."$Id>".h($b->editVal($X,$k)).'</label>';}}elseif(ereg('blob|bytea|raw|file',$k["type"])&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$C'$Id>";elseif(($zf=ereg('text|lob',$k["type"]))||ereg("\n",$Y)){if($zf&&$v!="sqlite")$va.=" cols='50' rows='12'";else{$K=min(12,substr_count($Y,"\n")+1);$va.=" cols='30' rows='$K'".($K==1?" style='height: 1.2em;'":"");}echo"<textarea$va>".h($Y).'</textarea>';}else{$nd=(!ereg('int',$k["type"])&&preg_match('~^(\\d+)(,(\\d+))?$~',$k["length"],$_)?((ereg("binary",$k["type"])?2:1)*$_[1]+($_[3]?1:0)+($_[2]&&!$k["unsigned"]?1:0)):($Qf[$k["type"]]?$Qf[$k["type"]]+($k["unsigned"]?0:1):0));echo"<input value='".h($Y)."'".($nd?" maxlength='$nd'":"").(ereg('char|binary',$k["type"])&&$nd>20?" size='40'":"")."$va>";}}}function
process_input($k){global$b;$Bc=bracket_escape($k["field"]);$o=$_POST["function"][$Bc];$Y=$_POST["fields"][$Bc];if($k["type"]=="enum"){if($Y==-1)return
false;if($Y=="")return"NULL";return+$Y;}if($k["auto_increment"]&&$Y=="")return
null;if($o=="orig")return($k["on_update"]=="CURRENT_TIMESTAMP"?idf_escape($k["field"]):false);if($o=="NULL")return"NULL";if($k["type"]=="set")return
array_sum((array)$Y);if(ereg('blob|bytea|raw|file',$k["type"])&&ini_bool("file_uploads")){$ec=get_file("fields-$Bc");if(!is_string($ec))return
false;return
q($ec);}return$b->processInput($k,$Y,$o);}function
search_tables(){global$b,$f;$_GET["where"][0]["op"]="LIKE %%";$_GET["where"][0]["val"]=$_POST["query"];$mc=false;foreach(table_status()as$R=>$S){$C=$b->tableName($S);if(isset($S["Engine"])&&$C!=""&&(!$_POST["tables"]||in_array($R,$_POST["tables"]))){$H=$f->query("SELECT".limit("1 FROM ".table($R)," WHERE ".implode(" AND ",$b->selectSearchProcess(fields($R),array())),1));if(!$H||$H->fetch_row()){if(!$mc){echo"<ul>\n";$mc=true;}echo"<li>".($H?"<a href='".h(ME."select=".urlencode($R)."&where[0][op]=".urlencode($_GET["where"][0]["op"])."&where[0][val]=".urlencode($_GET["where"][0]["val"]))."'>$C</a>\n":"$C: <span class='error'>".error()."</span>\n");}}}echo($mc?"</ul>":"<p class='message'>".'No tables.')."\n";}function
dump_headers($Ac,$ud=false){global$b;$I=$b->dumpHeaders($Ac,$ud);$Xd=$_POST["output"];if($Xd!="text")header("Content-Disposition: attachment; filename=".$b->dumpFilename($Ac).".$I".($Xd!="file"&&!ereg('[^0-9a-z]',$Xd)?".$Xd":""));session_write_close();return$I;}function
dump_csv($J){foreach($J
as$w=>$X){if(preg_match("~[\"\n,;\t]~",$X)||$X==="")$J[$w]='"'.str_replace('"','""',$X).'"';}echo
implode(($_POST["format"]=="csv"?",":($_POST["format"]=="tsv"?"\t":";")),$J)."\r\n";}function
apply_sql_function($o,$Ta){return($o?($o=="unixepoch"?"DATETIME($Ta, '$o')":($o=="count distinct"?"COUNT(DISTINCT ":strtoupper("$o("))."$Ta)"):$Ta);}function
password_file(){$vb=ini_get("upload_tmp_dir");if(!$vb){if(function_exists('sys_get_temp_dir'))$vb=sys_get_temp_dir();else{$fc=@tempnam("","");if(!$fc)return
false;$vb=dirname($fc);unlink($fc);}}$fc="$vb/adminer.key";$I=@file_get_contents($fc);if($I)return$I;$oc=@fopen($fc,"w");if($oc){$I=md5(uniqid(mt_rand(),true));fwrite($oc,$I);fclose($oc);}return$I;}function
is_mail($Hb){$ua='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$xb='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$E="$ua+(\\.$ua+)*@($xb?\\.)+$xb";return
preg_match("(^$E(,\\s*$E)*\$)i",$Hb);}function
is_url($P){$xb='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return(preg_match("~^(https?)://($xb?\\.)+$xb(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i",$P,$_)?strtolower($_[1]):"");}function
slow_query($G){global$b,$T;$i=$b->database();if(support("kill")&&is_object($g=connect())&&($i==""||$g->select_db($i))){$Rc=$g->result("SELECT CONNECTION_ID()");echo'<script type="text/javascript">
var timeout = setTimeout(function () {
	ajax(\'',js_escape(ME),'script=kill\', function () {
	}, \'token=',$T,'&kill=',$Rc,'\');
}, ',1000*$b->queryTimeout(),');
</script>
';}else$g=null;ob_flush();flush();$I=@get_key_vals($G,$g);if($g){echo"<script type='text/javascript'>clearTimeout(timeout);</script>\n";ob_flush();flush();}return
array_keys($I);}function
lzw_decompress($Ba){$ub=256;$Ca=8;$Oa=array();$Je=0;$Ke=0;for($q=0;$q<strlen($Ba);$q++){$Je=($Je<<8)+ord($Ba[$q]);$Ke+=8;if($Ke>=$Ca){$Ke-=$Ca;$Oa[]=$Je>>$Ke;$Je&=(1<<$Ke)-1;$ub++;if($ub>>$Ca)$Ca++;}}$tb=range("\0","\xFF");$I="";foreach($Oa
as$q=>$Na){$Gb=$tb[$Na];if(!isset($Gb))$Gb=$lg.$lg[0];$I.=$Gb;if($q)$tb[]=$lg.$Gb[0];$lg=$Gb;}return$I;}global$b,$f,$zb,$Eb,$Ob,$j,$rc,$vc,$ba,$Gc,$v,$ca,$Uc,$Hd,$ie,$if,$T,$Kf,$Qf,$Xf,$ga;if(!$_SERVER["REQUEST_URI"])$_SERVER["REQUEST_URI"]=$_SERVER["ORIG_PATH_INFO"];if(!strpos($_SERVER["REQUEST_URI"],'?')&&$_SERVER["QUERY_STRING"]!="")$_SERVER["REQUEST_URI"].="?$_SERVER[QUERY_STRING]";$ba=$_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off");@ini_set("session.use_trans_sid",false);if(!defined("SID")){session_name("adminer_sid");$ae=array(0,preg_replace('~\\?.*~','',$_SERVER["REQUEST_URI"]),"",$ba);if(version_compare(PHP_VERSION,'5.2.0')>=0)$ae[]=true;call_user_func_array('session_set_cookie_params',$ae);session_start();}remove_slashes(array(&$_GET,&$_POST,&$_COOKIE),$gc);if(function_exists("set_magic_quotes_runtime"))set_magic_quotes_runtime(false);@set_time_limit(0);@ini_set("zend.ze1_compatibility_mode",false);@ini_set("precision",20);function
get_lang(){return'en';}function
lang($Jf,$_d){$ke=($_d==1?0:1);$Jf=str_replace("%d","%s",$Jf[$ke]);$_d=number_format($_d,0,".",',');return
sprintf($Jf,$_d);}if(extension_loaded('pdo')){class
Min_PDO
extends
PDO{var$_result,$server_info,$affected_rows,$error;function
__construct(){global$b;$ke=array_search("",$b->operators);if($ke!==false)unset($b->operators[$ke]);}function
dsn($Bb,$V,$he,$Vb='auth_error'){set_exception_handler($Vb);parent::__construct($Bb,$V,$he);restore_exception_handler();$this->setAttribute(13,array('Min_PDOStatement'));$this->server_info=$this->getAttribute(4);}function
query($G,$Rf=false){$H=parent::query($G);$this->error="";if(!$H){$Pb=$this->errorInfo();$this->error=$Pb[2];return
false;}$this->store_result($H);return$H;}function
multi_query($G){return$this->_result=$this->query($G);}function
store_result($H=null){if(!$H)$H=$this->_result;if($H->columnCount()){$H->num_rows=$H->rowCount();return$H;}$this->affected_rows=$H->rowCount();return
true;}function
next_result(){$this->_result->_offset=0;return@$this->_result->nextRowset();}function
result($G,$k=0){$H=$this->query($G);if(!$H)return
false;$J=$H->fetch();return$J[$k];}}class
Min_PDOStatement
extends
PDOStatement{var$_offset=0,$num_rows;function
fetch_assoc(){return$this->fetch(2);}function
fetch_row(){return$this->fetch(3);}function
fetch_field(){$J=(object)$this->getColumnMeta($this->_offset++);$J->orgtable=$J->table;$J->orgname=$J->name;$J->charsetnr=(in_array("blob",(array)$J->flags)?63:0);return$J;}}}$zb=array();$zb=array("server"=>"MySQL")+$zb;if(!defined("DRIVER")){$ne=array("MySQLi","MySQL","PDO_MySQL");define("DRIVER","server");if(extension_loaded("mysqli")){class
Min_DB
extends
MySQLi{var$extension="MySQLi";function
Min_DB(){parent::init();}function
connect($N,$V,$he){mysqli_report(MYSQLI_REPORT_OFF);list($zc,$je)=explode(":",$N,2);$I=@$this->real_connect(($N!=""?$zc:ini_get("mysqli.default_host")),($N.$V!=""?$V:ini_get("mysqli.default_user")),($N.$V.$he!=""?$he:ini_get("mysqli.default_pw")),null,(is_numeric($je)?$je:ini_get("mysqli.default_port")),(!is_numeric($je)?$je:null));if($I){if(method_exists($this,'set_charset'))$this->set_charset("utf8");else$this->query("SET NAMES utf8");}return$I;}function
result($G,$k=0){$H=$this->query($G);if(!$H)return
false;$J=$H->fetch_array();return$J[$k];}function
quote($P){return"'".$this->escape_string($P)."'";}}}elseif(extension_loaded("mysql")&&!(ini_get("sql.safe_mode")&&extension_loaded("pdo_mysql"))){class
Min_DB{var$extension="MySQL",$server_info,$affected_rows,$error,$_link,$_result;function
connect($N,$V,$he){$this->_link=@mysql_connect(($N!=""?$N:ini_get("mysql.default_host")),("$N$V"!=""?$V:ini_get("mysql.default_user")),("$N$V$he"!=""?$he:ini_get("mysql.default_password")),true,131072);if($this->_link){$this->server_info=mysql_get_server_info($this->_link);if(function_exists('mysql_set_charset'))mysql_set_charset("utf8",$this->_link);else$this->query("SET NAMES utf8");}else$this->error=mysql_error();return(bool)$this->_link;}function
quote($P){return"'".mysql_real_escape_string($P,$this->_link)."'";}function
select_db($mb){return
mysql_select_db($mb,$this->_link);}function
query($G,$Rf=false){$H=@($Rf?mysql_unbuffered_query($G,$this->_link):mysql_query($G,$this->_link));$this->error="";if(!$H){$this->error=mysql_error($this->_link);return
false;}if($H===true){$this->affected_rows=mysql_affected_rows($this->_link);$this->info=mysql_info($this->_link);return
true;}return
new
Min_Result($H);}function
multi_query($G){return$this->_result=$this->query($G);}function
store_result(){return$this->_result;}function
next_result(){return
false;}function
result($G,$k=0){$H=$this->query($G);if(!$H||!$H->num_rows)return
false;return
mysql_result($H->_result,0,$k);}}class
Min_Result{var$num_rows,$_result,$_offset=0;function
Min_Result($H){$this->_result=$H;$this->num_rows=mysql_num_rows($H);}function
fetch_assoc(){return
mysql_fetch_assoc($this->_result);}function
fetch_row(){return
mysql_fetch_row($this->_result);}function
fetch_field(){$I=mysql_fetch_field($this->_result,$this->_offset++);$I->orgtable=$I->table;$I->orgname=$I->name;$I->charsetnr=($I->blob?63:0);return$I;}function
__destruct(){mysql_free_result($this->_result);}}}elseif(extension_loaded("pdo_mysql")){class
Min_DB
extends
Min_PDO{var$extension="PDO_MySQL";function
connect($N,$V,$he){$this->dsn("mysql:host=".str_replace(":",";unix_socket=",preg_replace('~:(\\d)~',';port=\\1',$N)),$V,$he);$this->query("SET NAMES utf8");return
true;}function
select_db($mb){return$this->query("USE ".idf_escape($mb));}function
query($G,$Rf=false){$this->setAttribute(1000,!$Rf);return
parent::query($G,$Rf);}}}function
idf_escape($Bc){return"`".str_replace("`","``",$Bc)."`";}function
table($Bc){return
idf_escape($Bc);}function
connect(){global$b;$f=new
Min_DB;$ib=$b->credentials();if($f->connect($ib[0],$ib[1],$ib[2])){$f->query("SET sql_quote_show_create = 1, autocommit = 1");return$f;}$I=$f->error;if(function_exists('iconv')&&!is_utf8($I)&&strlen($L=iconv("windows-1250","utf-8",$I))>strlen($I))$I=$L;return$I;}function
get_databases($ic){global$f;$I=get_session("dbs");if($I===null){$G=($f->server_info>=5?"SELECT SCHEMA_NAME FROM information_schema.SCHEMATA":"SHOW DATABASES");$I=($ic?slow_query($G):get_vals($G));restart_session();set_session("dbs",$I);stop_session();}return$I;}function
limit($G,$Z,$x,$Bd=0,$Xe=" "){return" $G$Z".($x!==null?$Xe."LIMIT $x".($Bd?" OFFSET $Bd":""):"");}function
limit1($G,$Z){return
limit($G,$Z,1);}function
db_collation($i,$d){global$f;$I=null;$fb=$f->result("SHOW CREATE DATABASE ".idf_escape($i),1);if(preg_match('~ COLLATE ([^ ]+)~',$fb,$_))$I=$_[1];elseif(preg_match('~ CHARACTER SET ([^ ]+)~',$fb,$_))$I=$d[$_[1]][-1];return$I;}function
engines(){$I=array();foreach(get_rows("SHOW ENGINES")as$J){if(ereg("YES|DEFAULT",$J["Support"]))$I[]=$J["Engine"];}return$I;}function
logged_user(){global$f;return$f->result("SELECT USER()");}function
tables_list(){global$f;return
get_key_vals("SHOW".($f->server_info>=5?" FULL":"")." TABLES");}function
count_tables($h){$I=array();foreach($h
as$i)$I[$i]=count(get_vals("SHOW TABLES IN ".idf_escape($i)));return$I;}function
table_status($C=""){$I=array();foreach(get_rows("SHOW TABLE STATUS".($C!=""?" LIKE ".q(addcslashes($C,"%_")):""))as$J){if($J["Engine"]=="InnoDB")$J["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\\1',$J["Comment"]);if(!isset($J["Rows"]))$J["Comment"]="";if($C!="")return$J;$I[$J["Name"]]=$J;}return$I;}function
is_view($S){return!isset($S["Rows"]);}function
fk_support($S){return
eregi("InnoDB|IBMDB2I",$S["Engine"]);}function
fields($R){$I=array();foreach(get_rows("SHOW FULL COLUMNS FROM ".table($R))as$J){preg_match('~^([^( ]+)(?:\\((.+)\\))?( unsigned)?( zerofill)?$~',$J["Type"],$_);$I[$J["Field"]]=array("field"=>$J["Field"],"full_type"=>$J["Type"],"type"=>$_[1],"length"=>$_[2],"unsigned"=>ltrim($_[3].$_[4]),"default"=>($J["Default"]!=""||ereg("char",$_[1])?$J["Default"]:null),"null"=>($J["Null"]=="YES"),"auto_increment"=>($J["Extra"]=="auto_increment"),"on_update"=>(eregi('^on update (.+)',$J["Extra"],$_)?$_[1]:""),"collation"=>$J["Collation"],"privileges"=>array_flip(explode(",",$J["Privileges"])),"comment"=>$J["Comment"],"primary"=>($J["Key"]=="PRI"),);}return$I;}function
indexes($R,$g=null){$I=array();foreach(get_rows("SHOW INDEX FROM ".table($R),$g)as$J){$I[$J["Key_name"]]["type"]=($J["Key_name"]=="PRIMARY"?"PRIMARY":($J["Index_type"]=="FULLTEXT"?"FULLTEXT":($J["Non_unique"]?"INDEX":"UNIQUE")));$I[$J["Key_name"]]["columns"][]=$J["Column_name"];$I[$J["Key_name"]]["lengths"][]=$J["Sub_part"];}return$I;}function
foreign_keys($R){global$f,$Hd;static$E='`(?:[^`]|``)+`';$I=array();$gb=$f->result("SHOW CREATE TABLE ".table($R),1);if($gb){preg_match_all("~CONSTRAINT ($E) FOREIGN KEY \\(((?:$E,? ?)+)\\) REFERENCES ($E)(?:\\.($E))? \\(((?:$E,? ?)+)\\)(?: ON DELETE ($Hd))?(?: ON UPDATE ($Hd))?~",$gb,$hd,PREG_SET_ORDER);foreach($hd
as$_){preg_match_all("~$E~",$_[2],$bf);preg_match_all("~$E~",$_[5],$xf);$I[idf_unescape($_[1])]=array("db"=>idf_unescape($_[4]!=""?$_[3]:$_[4]),"table"=>idf_unescape($_[4]!=""?$_[4]:$_[3]),"source"=>array_map('idf_unescape',$bf[0]),"target"=>array_map('idf_unescape',$xf[0]),"on_delete"=>($_[6]?$_[6]:"RESTRICT"),"on_update"=>($_[7]?$_[7]:"RESTRICT"),);}}return$I;}function
view($C){global$f;return
array("select"=>preg_replace('~^(?:[^`]|`[^`]*`)*\\s+AS\\s+~isU','',$f->result("SHOW CREATE VIEW ".table($C),1)));}function
collations(){$I=array();foreach(get_rows("SHOW COLLATION")as$J){if($J["Default"])$I[$J["Charset"]][-1]=$J["Collation"];else$I[$J["Charset"]][]=$J["Collation"];}ksort($I);foreach($I
as$w=>$X)asort($I[$w]);return$I;}function
information_schema($i){global$f;return($f->server_info>=5&&$i=="information_schema")||($f->server_info>=5.5&&$i=="performance_schema");}function
error(){global$f;return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",$f->error));}function
error_line(){global$f;if(ereg(' at line ([0-9]+)$',$f->error,$Ge))return$Ge[1]-1;}function
exact_value($X){return
q($X)." COLLATE utf8_bin";}function
create_database($i,$Ra){set_session("dbs",null);return
queries("CREATE DATABASE ".idf_escape($i).($Ra?" COLLATE ".q($Ra):""));}function
drop_databases($h){set_session("dbs",null);return
apply_queries("DROP DATABASE",$h,'idf_escape');}function
rename_database($C,$Ra){if(create_database($C,$Ra)){$He=array();foreach(tables_list()as$R=>$U)$He[]=table($R)." TO ".idf_escape($C).".".table($R);if(!$He||queries("RENAME TABLE ".implode(", ",$He))){queries("DROP DATABASE ".idf_escape(DB));return
true;}}return
false;}function
auto_increment(){$xa=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$s){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$s["columns"],true)){$xa="";break;}if($s["type"]=="PRIMARY")$xa=" UNIQUE";}}return" AUTO_INCREMENT$xa";}function
alter_table($R,$C,$l,$jc,$Wa,$Mb,$Ra,$wa,$ee){$sa=array();foreach($l
as$k)$sa[]=($k[1]?($R!=""?($k[0]!=""?"CHANGE ".idf_escape($k[0]):"ADD"):" ")." ".implode($k[1]).($R!=""?$k[2]:""):"DROP ".idf_escape($k[0]));$sa=array_merge($sa,$jc);$ff="COMMENT=".q($Wa).($Mb?" ENGINE=".q($Mb):"").($Ra?" COLLATE ".q($Ra):"").($wa!=""?" AUTO_INCREMENT=$wa":"").$ee;if($R=="")return
queries("CREATE TABLE ".table($C)." (\n".implode(",\n",$sa)."\n) $ff");if($R!=$C)$sa[]="RENAME TO ".table($C);$sa[]=$ff;return
queries("ALTER TABLE ".table($R)."\n".implode(",\n",$sa));}function
alter_indexes($R,$sa){foreach($sa
as$w=>$X)$sa[$w]=($X[2]=="DROP"?"\nDROP INDEX ".idf_escape($X[1]):"\nADD $X[0] ".($X[0]=="PRIMARY"?"KEY ":"").($X[1]!=""?idf_escape($X[1])." ":"").$X[2]);return
queries("ALTER TABLE ".table($R).implode(",",$sa));}function
truncate_tables($uf){return
apply_queries("TRUNCATE TABLE",$uf);}function
drop_views($gg){return
queries("DROP VIEW ".implode(", ",array_map('table',$gg)));}function
drop_tables($uf){return
queries("DROP TABLE ".implode(", ",array_map('table',$uf)));}function
move_tables($uf,$gg,$xf){$He=array();foreach(array_merge($uf,$gg)as$R)$He[]=table($R)." TO ".idf_escape($xf).".".table($R);return
queries("RENAME TABLE ".implode(", ",$He));}function
copy_tables($uf,$gg,$xf){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($uf
as$R){$C=($xf==DB?table("copy_$R"):idf_escape($xf).".".table($R));if(!queries("DROP TABLE IF EXISTS $C")||!queries("CREATE TABLE $C LIKE ".table($R))||!queries("INSERT INTO $C SELECT * FROM ".table($R)))return
false;}foreach($gg
as$R){$C=($xf==DB?table("copy_$R"):idf_escape($xf).".".table($R));$fg=view($R);if(!queries("DROP VIEW IF EXISTS $C")||!queries("CREATE VIEW $C AS $fg[select]"))return
false;}return
true;}function
trigger($C){if($C=="")return
array();$K=get_rows("SHOW TRIGGERS WHERE `Trigger` = ".q($C));return
reset($K);}function
triggers($R){$I=array();foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_")))as$J)$I[$J["Trigger"]]=array($J["Timing"],$J["Event"]);return$I;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Type"=>array("FOR EACH ROW"),);}function
routine($C,$U){global$f,$Ob,$Gc,$Qf;$pa=array("bool","boolean","integer","double precision","real","dec","numeric","fixed","national char","national varchar");$Pf="((".implode("|",array_merge(array_keys($Qf),$pa)).")\\b(?:\\s*\\(((?:[^'\")]*|$Ob)+)\\))?\\s*(zerofill\\s*)?(unsigned(?:\\s+zerofill)?)?)(?:\\s*(?:CHARSET|CHARACTER\\s+SET)\\s*['\"]?([^'\"\\s]+)['\"]?)?";$E="\\s*(".($U=="FUNCTION"?"":$Gc).")?\\s*(?:`((?:[^`]|``)*)`\\s*|\\b(\\S+)\\s+)$Pf";$fb=$f->result("SHOW CREATE $U ".idf_escape($C),2);preg_match("~\\(((?:$E\\s*,?)*)\\)\\s*".($U=="FUNCTION"?"RETURNS\\s+$Pf\\s+":"")."(.*)~is",$fb,$_);$l=array();preg_match_all("~$E\\s*,?~is",$_[1],$hd,PREG_SET_ORDER);foreach($hd
as$Zd){$C=str_replace("``","`",$Zd[2]).$Zd[3];$l[]=array("field"=>$C,"type"=>strtolower($Zd[5]),"length"=>preg_replace_callback("~$Ob~s",'normalize_enum',$Zd[6]),"unsigned"=>strtolower(preg_replace('~\\s+~',' ',trim("$Zd[8] $Zd[7]"))),"full_type"=>$Zd[4],"inout"=>strtoupper($Zd[1]),"collation"=>strtolower($Zd[9]),);}if($U!="FUNCTION")return
array("fields"=>$l,"definition"=>$_[11]);return
array("fields"=>$l,"returns"=>array("type"=>$_[12],"length"=>$_[13],"unsigned"=>$_[15],"collation"=>$_[16]),"definition"=>$_[17],"language"=>"SQL",);}function
routines(){return
get_rows("SELECT * FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = ".q(DB));}function
routine_languages(){return
array();}function
begin(){return
queries("BEGIN");}function
insert_into($R,$O){return
queries("INSERT INTO ".table($R)." (".implode(", ",array_keys($O)).")\nVALUES (".implode(", ",$O).")");}function
insert_update($R,$O,$qe){foreach($O
as$w=>$X)$O[$w]="$w = $X";$Yf=implode(", ",$O);return
queries("INSERT INTO ".table($R)." SET $Yf ON DUPLICATE KEY UPDATE $Yf");}function
last_id(){global$f;return$f->result("SELECT LAST_INSERT_ID()");}function
explain($f,$G){return$f->query("EXPLAIN $G");}function
found_rows($S,$Z){return($Z||$S["Engine"]!="InnoDB"?null:$S["Rows"]);}function
types(){return
array();}function
schemas(){return
array();}function
get_schema(){return"";}function
set_schema($Te){return
true;}function
create_sql($R,$wa){global$f;$I=$f->result("SHOW CREATE TABLE ".table($R),1);if(!$wa)$I=preg_replace('~ AUTO_INCREMENT=\\d+~','',$I);return$I;}function
truncate_sql($R){return"TRUNCATE ".table($R);}function
use_sql($mb){return"USE ".idf_escape($mb);}function
trigger_sql($R,$Q){$I="";foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_")),null,"-- ")as$J)$I.="\n".($Q=='CREATE+ALTER'?"DROP TRIGGER IF EXISTS ".idf_escape($J["Trigger"]).";;\n":"")."CREATE TRIGGER ".idf_escape($J["Trigger"])." $J[Timing] $J[Event] ON ".table($J["Table"])." FOR EACH ROW\n$J[Statement];;\n";return$I;}function
show_variables(){return
get_key_vals("SHOW VARIABLES");}function
process_list(){return
get_rows("SHOW FULL PROCESSLIST");}function
show_status(){return
get_key_vals("SHOW STATUS");}function
convert_field($k){if(ereg("binary",$k["type"]))return"HEX(".idf_escape($k["field"]).")";if(ereg("geometry|point|linestring|polygon",$k["type"]))return"AsWKT(".idf_escape($k["field"]).")";}function
unconvert_field($k,$I){if(ereg("binary",$k["type"]))$I="unhex($I)";if(ereg("geometry|point|linestring|polygon",$k["type"]))$I="GeomFromText($I)";return$I;}function
support($dc){global$f;return!ereg("scheme|sequence|type".($f->server_info<5.1?"|event|partitioning".($f->server_info<5?"|view|routine|trigger":""):""),$dc);}$v="sql";$Qf=array();$if=array();foreach(array('Numbers'=>array("tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21),'Date and time'=>array("date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4),'Strings'=>array("char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295),'Lists'=>array("enum"=>65535,"set"=>64),'Binary'=>array("bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295),'Geometry'=>array("geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0),)as$w=>$X){$Qf+=$X;$if[$w]=array_keys($X);}$Xf=array("unsigned","zerofill","unsigned zerofill");$Ld=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","REGEXP","IN","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","");$rc=array("char_length","date","from_unixtime","lower","round","sec_to_time","time_to_sec","upper");$vc=array("avg","count","count distinct","group_concat","max","min","sum");$Eb=array(array("char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",),array("(^|[^o])int|float|double|decimal"=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",));}define("SERVER",$_GET[DRIVER]);define("DB",$_GET["db"]);define("ME",preg_replace('~^[^?]*/([^?]*).*~','\\1',$_SERVER["REQUEST_URI"]).'?'.(sid()?SID.'&':'').(SERVER!==null?DRIVER."=".urlencode(SERVER).'&':'').(isset($_GET["username"])?"username=".urlencode($_GET["username"]).'&':'').(DB!=""?'db='.urlencode(DB).'&'.(isset($_GET["ns"])?"ns=".urlencode($_GET["ns"])."&":""):''));$ga="3.6.1";class
Adminer{var$operators;function
name(){return"<a href='http://www.adminer.org/' id='h1'>Adminer</a>";}function
credentials(){return
array(SERVER,$_GET["username"],get_session("pwds"));}function
permanentLogin(){return
password_file();}function
database(){return
DB;}function
databases($ic=true){return
get_databases($ic);}function
queryTimeout(){return
5;}function
headers(){return
true;}function
head(){return
true;}function
loginForm(){global$zb;echo'<table cellspacing="0">
<tr><th>System<td>',html_select("auth[driver]",$zb,DRIVER,"loginDriver(this);"),'<tr><th>Server<td><input name="auth[server]" value="',h(SERVER),'" title="hostname[:port]">
<tr><th>Username<td><input id="username" name="auth[username]" value="',h($_GET["username"]),'">
<tr><th>Password<td><input type="password" name="auth[password]">
<tr><th>Database<td><input name="auth[db]" value="',h($_GET["db"]);?>">
</table>
<script type="text/javascript">
var username = document.getElementById('username');
username.focus();
username.form['auth[driver]'].onchange();
</script>
<?php

echo"<p><input type='submit' value='".'Login'."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],'Permanent login')."\n";}function
login($fd,$he){return
true;}function
tableName($pf){return
h($pf["Name"]);}function
fieldName($k,$Od=0){return'<span title="'.h($k["full_type"]).'">'.h($k["field"]).'</span>';}function
selectLinks($pf,$O=""){echo'<p class="tabs">';$ed=array("select"=>'Select data',"table"=>'Show structure');if(is_view($pf))$ed["view"]='Alter view';else$ed["create"]='Alter table';if($O!==null)$ed["edit"]='New item';foreach($ed
as$w=>$X)echo" <a href='".h(ME)."$w=".urlencode($pf["Name"]).($w=="edit"?$O:"")."'".bold(isset($_GET[$w])).">$X</a>";echo"\n";}function
foreignKeys($R){return
foreign_keys($R);}function
backwardKeys($R,$of){return
array();}function
backwardKeysPrint($za,$J){}function
selectQuery($G){global$v;return"<p><a href='".h(remove_from_uri("page"))."&amp;page=last' title='".'Last page'."'>&gt;&gt;</a> <code class='jush-$v'>".h(str_replace("\n"," ",$G))."</code> <a href='".h(ME)."sql=".urlencode($G)."'>".'Edit'."</a></p>\n";}function
rowDescription($R){return"";}function
rowDescriptions($K,$kc){return$K;}function
selectVal($X,$y,$k){$I=($X===null?"<i>NULL</i>":(ereg("char|binary",$k["type"])&&!ereg("var",$k["type"])?"<code>$X</code>":$X));if(ereg('blob|bytea|raw|file',$k["type"])&&!is_utf8($X))$I=lang(array('%d byte','%d bytes'),strlen($X));return($y?"<a href='$y'>$I</a>":$I);}function
editVal($X,$k){return$X;}function
selectColumnsPrint($M,$e){global$rc,$vc;print_fieldset("select",'Select',$M);$q=0;$qc=array('Functions'=>$rc,'Aggregation'=>$vc);foreach($M
as$w=>$X){$X=$_GET["columns"][$w];echo"<div>".html_select("columns[$q][fun]",array(-1=>"")+$qc,$X["fun"]),"(<select name='columns[$q][col]' onchange='selectFieldChange(this.form);'><option>".optionlist($e,$X["col"],true)."</select>)</div>\n";$q++;}echo"<div>".html_select("columns[$q][fun]",array(-1=>"")+$qc,"","this.nextSibling.nextSibling.onchange();"),"(<select name='columns[$q][col]' onchange='selectAddRow(this);'><option>".optionlist($e,null,true)."</select>)</div>\n","</div></fieldset>\n";}function
selectSearchPrint($Z,$e,$t){print_fieldset("search",'Search',$Z);foreach($t
as$q=>$s){if($s["type"]=="FULLTEXT"){echo"(<i>".implode("</i>, <i>",array_map('h',$s["columns"]))."</i>) AGAINST"," <input name='fulltext[$q]' value='".h($_GET["fulltext"][$q])."' onchange='selectFieldChange(this.form);'>",checkbox("boolean[$q]",1,isset($_GET["boolean"][$q]),"BOOL"),"<br>\n";}}$_GET["where"]=(array)$_GET["where"];reset($_GET["where"]);$Ia="this.nextSibling.onchange();";for($q=0;$q<=count($_GET["where"]);$q++){list(,$X)=each($_GET["where"]);if(!$X||("$X[col]$X[val]"!=""&&in_array($X["op"],$this->operators))){echo"<div><select name='where[$q][col]' onchange='$Ia'><option value=''>(".'anywhere'.")".optionlist($e,$X["col"],true)."</select>",html_select("where[$q][op]",$this->operators,$X["op"],$Ia),"<input name='where[$q][val]' value='".h($X["val"])."' onchange='".($X?"selectFieldChange(this.form)":"selectAddRow(this)").";'></div>\n";}}echo"</div></fieldset>\n";}function
selectOrderPrint($Od,$e,$t){print_fieldset("sort",'Sort',$Od);$q=0;foreach((array)$_GET["order"]as$w=>$X){if(isset($e[$X])){echo"<div><select name='order[$q]' onchange='selectFieldChange(this.form);'><option>".optionlist($e,$X,true)."</select>",checkbox("desc[$q]",1,isset($_GET["desc"][$w]),'descending')."</div>\n";$q++;}}echo"<div><select name='order[$q]' onchange='selectAddRow(this);'><option>".optionlist($e,null,true)."</select>","<label><input type='checkbox' name='desc[$q]' value='1'>".'descending'."</label></div>\n";echo"</div></fieldset>\n";}function
selectLimitPrint($x){echo"<fieldset><legend>".'Limit'."</legend><div>";echo"<input name='limit' size='3' value='".h($x)."' onchange='selectFieldChange(this.form);'>","</div></fieldset>\n";}function
selectLengthPrint($_f){if($_f!==null){echo"<fieldset><legend>".'Text length'."</legend><div>",'<input name="text_length" size="3" value="'.h($_f).'">',"</div></fieldset>\n";}}function
selectActionPrint($t){echo"<fieldset><legend>".'Action'."</legend><div>","<input type='submit' value='".'Select'."'>"," <span id='noindex' title='".'Full table scan'."'></span>","<script type='text/javascript'>\n","var indexColumns = ";$e=array();foreach($t
as$s){if($s["type"]!="FULLTEXT")$e[reset($s["columns"])]=1;}$e[""]=1;foreach($e
as$w=>$X)json_row($w);echo";\n","selectFieldChange(document.getElementById('form'));\n","</script>\n","</div></fieldset>\n";}function
selectCommandPrint(){return!information_schema(DB);}function
selectImportPrint(){return!information_schema(DB);}function
selectEmailPrint($Ib,$e){}function
selectColumnsProcess($e,$t){global$rc,$vc;$M=array();$tc=array();foreach((array)$_GET["columns"]as$w=>$X){if($X["fun"]=="count"||(isset($e[$X["col"]])&&(!$X["fun"]||in_array($X["fun"],$rc)||in_array($X["fun"],$vc)))){$M[$w]=apply_sql_function($X["fun"],(isset($e[$X["col"]])?idf_escape($X["col"]):"*"));if(!in_array($X["fun"],$vc))$tc[]=$M[$w];}}return
array($M,$tc);}function
selectSearchProcess($l,$t){global$v;$I=array();foreach($t
as$q=>$s){if($s["type"]=="FULLTEXT"&&$_GET["fulltext"][$q]!="")$I[]="MATCH (".implode(", ",array_map('idf_escape',$s["columns"])).") AGAINST (".q($_GET["fulltext"][$q]).(isset($_GET["boolean"][$q])?" IN BOOLEAN MODE":"").")";}foreach((array)$_GET["where"]as$X){if("$X[col]$X[val]"!=""&&in_array($X["op"],$this->operators)){$Za=" $X[op]";if(ereg('IN$',$X["op"])){$Dc=process_length($X["val"]);$Za.=" (".($Dc!=""?$Dc:"NULL").")";}elseif(!$X["op"])$Za.=$X["val"];elseif($X["op"]=="LIKE %%")$Za=" LIKE ".$this->processInput($l[$X["col"]],"%$X[val]%");elseif(!ereg('NULL$',$X["op"]))$Za.=" ".$this->processInput($l[$X["col"]],$X["val"]);if($X["col"]!="")$I[]=idf_escape($X["col"]).$Za;else{$Sa=array();foreach($l
as$C=>$k){$Mc=ereg('char|text|enum|set',$k["type"]);if((is_numeric($X["val"])||!ereg('int|float|double|decimal|bit',$k["type"]))&&(!ereg("[\x80-\xFF]",$X["val"])||$Mc)){$C=idf_escape($C);$Sa[]=($v=="sql"&&$Mc&&!ereg('^utf8',$k["collation"])?"CONVERT($C USING utf8)":$C);}}$I[]=($Sa?"(".implode("$Za OR ",$Sa)."$Za)":"0");}}}return$I;}function
selectOrderProcess($l,$t){$I=array();foreach((array)$_GET["order"]as$w=>$X){if(isset($l[$X])||preg_match('~^((COUNT\\(DISTINCT |[A-Z0-9_]+\\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\\)|COUNT\\(\\*\\))$~',$X))$I[]=(isset($l[$X])?idf_escape($X):$X).(isset($_GET["desc"][$w])?" DESC":"");}return$I;}function
selectLimitProcess(){return(isset($_GET["limit"])?$_GET["limit"]:"30");}function
selectLengthProcess(){return(isset($_GET["text_length"])?$_GET["text_length"]:"100");}function
selectEmailProcess($Z,$kc){return
false;}function
selectQueryBuild($M,$Z,$tc,$Od,$x,$D){return"";}function
messageQuery($G){global$v;static$eb=0;restart_session();$r="sql-".($eb++);$xc=&get_session("queries");if(strlen($G)>1e6)$G=ereg_replace('[\x80-\xFF]+$','',substr($G,0,1e6))."\n...";$xc[$_GET["db"]][]=array($G,time());return" <span class='time'>".@date("H:i:s")."</span> <a href='#$r' onclick=\"return !toggle('$r');\">".'SQL command'."</a><div id='$r' class='hidden'><pre><code class='jush-$v'>".shorten_utf8($G,1000).'</code></pre><p><a href="'.h(str_replace("db=".urlencode(DB),"db=".urlencode($_GET["db"]),ME).'sql=&history='.(count($xc[$_GET["db"]])-1)).'">'.'Edit'.'</a></div>';}function
editFunctions($k){global$Eb;$I=($k["null"]?"NULL/":"");foreach($Eb
as$w=>$rc){if(!$w||(!isset($_GET["call"])&&(isset($_GET["select"])||where($_GET)))){foreach($rc
as$E=>$X){if(!$E||ereg($E,$k["type"]))$I.="/$X";}if($w&&!ereg('set|blob|bytea|raw|file',$k["type"]))$I.="/=";}}return
explode("/",$I);}function
editInput($R,$k,$va,$Y){if($k["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$va value='-1' checked><i>".'original'."</i></label> ":"").($k["null"]?"<label><input type='radio'$va value=''".($Y!==null||isset($_GET["select"])?"":" checked")."><i>NULL</i></label> ":"").enum_input("radio",$va,$k,$Y,0);return"";}function
processInput($k,$Y,$o=""){if($o=="=")return$Y;$C=$k["field"];$I=($k["type"]=="bit"&&ereg("^([0-9]+|b'[0-1]+')\$",$Y)?$Y:q($Y));if(ereg('^(now|getdate|uuid)$',$o))$I="$o()";elseif(ereg('^current_(date|timestamp)$',$o))$I=$o;elseif(ereg('^([+-]|\\|\\|)$',$o))$I=idf_escape($C)." $o $I";elseif(ereg('^[+-] interval$',$o))$I=idf_escape($C)." $o ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+$~i",$Y)?$Y:$I);elseif(ereg('^(addtime|subtime|concat)$',$o))$I="$o(".idf_escape($C).", $I)";elseif(ereg('^(md5|sha1|password|encrypt)$',$o))$I="$o($I)";return
unconvert_field($k,$I);}function
dumpOutput(){$I=array('text'=>'open','file'=>'save');if(function_exists('gzencode'))$I['gz']='gzip';if(function_exists('bzcompress'))$I['bz2']='bzip2';return$I;}function
dumpFormat(){return
array('sql'=>'SQL','csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV');}function
dumpTable($R,$Q,$Nc=false){if($_POST["format"]!="sql"){echo"\xef\xbb\xbf";if($Q)dump_csv(array_keys(fields($R)));}elseif($Q){$fb=create_sql($R,$_POST["auto_increment"]);if($fb){if($Q=="DROP+CREATE")echo"DROP ".($Nc?"VIEW":"TABLE")." IF EXISTS ".table($R).";\n";if($Nc)$fb=remove_definer($fb);echo($Q!="CREATE+ALTER"?$fb:($Nc?substr_replace($fb," OR REPLACE",6,0):substr_replace($fb," IF NOT EXISTS",12,0))).";\n\n";}if($Q=="CREATE+ALTER"&&!$Nc){$G="SELECT COLUMN_NAME, COLUMN_DEFAULT, IS_NULLABLE, COLLATION_NAME, COLUMN_TYPE, EXTRA, COLUMN_COMMENT FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ".q($R)." ORDER BY ORDINAL_POSITION";echo"DELIMITER ;;
CREATE PROCEDURE adminer_alter (INOUT alter_command text) BEGIN
	DECLARE _column_name, _collation_name, after varchar(64) DEFAULT '';
	DECLARE _column_type, _column_default text;
	DECLARE _is_nullable char(3);
	DECLARE _extra varchar(30);
	DECLARE _column_comment varchar(255);
	DECLARE done, set_after bool DEFAULT 0;
	DECLARE add_columns text DEFAULT '";$l=array();$oa="";foreach(get_rows($G)as$J){$qb=$J["COLUMN_DEFAULT"];$J["default"]=($qb!==null?q($qb):"NULL");$J["after"]=q($oa);$J["alter"]=escape_string(idf_escape($J["COLUMN_NAME"])." $J[COLUMN_TYPE]".($J["COLLATION_NAME"]?" COLLATE $J[COLLATION_NAME]":"").($qb!==null?" DEFAULT ".($qb=="CURRENT_TIMESTAMP"?$qb:$J["default"]):"").($J["IS_NULLABLE"]=="YES"?"":" NOT NULL").($J["EXTRA"]?" $J[EXTRA]":"").($J["COLUMN_COMMENT"]?" COMMENT ".q($J["COLUMN_COMMENT"]):"").($oa?" AFTER ".idf_escape($oa):" FIRST"));echo", ADD $J[alter]";$l[]=$J;$oa=$J["COLUMN_NAME"];}echo"';
	DECLARE columns CURSOR FOR $G;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
	SET @alter_table = '';
	OPEN columns;
	REPEAT
		FETCH columns INTO _column_name, _column_default, _is_nullable, _collation_name, _column_type, _extra, _column_comment;
		IF NOT done THEN
			SET set_after = 1;
			CASE _column_name";foreach($l
as$J)echo"
				WHEN ".q($J["COLUMN_NAME"])." THEN
					SET add_columns = REPLACE(add_columns, ', ADD $J[alter]', IF(
						_column_default <=> $J[default] AND _is_nullable = '$J[IS_NULLABLE]' AND _collation_name <=> ".(isset($J["COLLATION_NAME"])?"'$J[COLLATION_NAME]'":"NULL")." AND _column_type = ".q($J["COLUMN_TYPE"])." AND _extra = '$J[EXTRA]' AND _column_comment = ".q($J["COLUMN_COMMENT"])." AND after = $J[after]
					, '', ', MODIFY $J[alter]'));";echo"
				ELSE
					SET @alter_table = CONCAT(@alter_table, ', DROP ', _column_name);
					SET set_after = 0;
			END CASE;
			IF set_after THEN
				SET after = _column_name;
			END IF;
		END IF;
	UNTIL done END REPEAT;
	CLOSE columns;
	IF @alter_table != '' OR add_columns != '' THEN
		SET alter_command = CONCAT(alter_command, 'ALTER TABLE ".table($R)."', SUBSTR(CONCAT(add_columns, @alter_table), 2), ';\\n');
	END IF;
END;;
DELIMITER ;
CALL adminer_alter(@adminer_alter);
DROP PROCEDURE adminer_alter;

";}}}function
dumpData($R,$Q,$G){global$f,$v;$jd=($v=="sqlite"?0:1048576);if($Q){if($_POST["format"]=="sql"&&$Q=="TRUNCATE+INSERT")echo
truncate_sql($R).";\n";if($_POST["format"]=="sql")$l=fields($R);$H=$f->query($G,1);if($H){$Ic="";$Ga="";$Qc=array();$kf="";while($J=$H->fetch_row()){if(!$Qc){$dg=array();foreach($J
as$X){$k=$H->fetch_field();$Qc[]=$k->name;$w=idf_escape($k->name);$dg[]="$w = VALUES($w)";}$kf=($Q=="INSERT+UPDATE"?"\nON DUPLICATE KEY UPDATE ".implode(", ",$dg):"").";\n";}if($_POST["format"]!="sql"){if($Q=="table"){dump_csv($Qc);$Q="INSERT";}dump_csv($J);}else{if(!$Ic)$Ic="INSERT INTO ".table($R)." (".implode(", ",array_map('idf_escape',$Qc)).") VALUES";foreach($J
as$w=>$X)$J[$w]=($X!==null?(ereg('int|float|double|decimal|bit',$l[$Qc[$w]]["type"])?$X:q($X)):"NULL");$L=($jd?"\n":" ")."(".implode(",\t",$J).")";if(!$Ga)$Ga=$Ic.$L;elseif(strlen($Ga)+4+strlen($L)+strlen($kf)<$jd)$Ga.=",$L";else{echo$Ga.$kf;$Ga=$Ic.$L;}}}if($Ga)echo$Ga.$kf;}elseif($_POST["format"]=="sql")echo"-- ".str_replace("\n"," ",$f->error)."\n";}}function
dumpFilename($Ac){return
friendly_url($Ac!=""?$Ac:(SERVER!=""?SERVER:"localhost"));}function
dumpHeaders($Ac,$ud=false){$Xd=$_POST["output"];$ac=($_POST["format"]=="sql"?"sql":($ud?"tar":"csv"));header("Content-Type: ".($Xd=="bz2"?"application/x-bzip":($Xd=="gz"?"application/x-gzip":($ac=="tar"?"application/x-tar":($ac=="sql"||$Xd!="file"?"text/plain":"text/csv")."; charset=utf-8"))));if($Xd=="bz2")ob_start('bzcompress',1e6);if($Xd=="gz")ob_start('gzencode',1e6);return$ac;}function
homepage(){echo'<p>'.($_GET["ns"]==""?'<a href="'.h(ME).'database=">'.'Alter database'."</a>\n":""),(support("scheme")?"<a href='".h(ME)."scheme='>".($_GET["ns"]!=""?'Alter schema':'Create schema')."</a>\n":""),($_GET["ns"]!==""?'<a href="'.h(ME).'schema=">'.'Database schema'."</a>\n":""),(support("privileges")?"<a href='".h(ME)."privileges='>".'Privileges'."</a>\n":"");return
true;}function
navigation($td){global$ga,$T,$v,$zb;echo'<h1>
',$this->name(),' <span class="version">',$ga,'</span>
<a href="http://www.adminer.org/#download" id="version">',(version_compare($ga,$_COOKIE["adminer_version"])<0?h($_COOKIE["adminer_version"]):""),'</a>
</h1>
';if($td=="auth"){$hc=true;foreach((array)$_SESSION["pwds"]as$yb=>$Ze){foreach($Ze
as$N=>$cg){foreach($cg
as$V=>$he){if($he!==null){if($hc){echo"<p id='logins' onmouseover='menuOver(this, event);' onmouseout='menuOut(this);'>\n";$hc=false;}$ob=$_SESSION["db"][$yb][$N][$V];foreach(($ob?array_keys($ob):array(""))as$i)echo"<a href='".h(auth_url($yb,$N,$V,$i))."'>($zb[$yb]) ".h($V.($N!=""?"@$N":"").($i!=""?" - $i":""))."</a><br>\n";}}}}}else{echo'<form action="" method="post">
<p class="logout">
';if(DB==""||!$td){echo"<a href='".h(ME)."sql='".bold(isset($_GET["sql"])).">".'SQL command'."</a>\n";if(support("dump"))echo"<a href='".h(ME)."dump=".urlencode(isset($_GET["table"])?$_GET["table"]:$_GET["select"])."' id='dump'".bold(isset($_GET["dump"])).">".'Dump'."</a>\n";}echo'<input type="submit" name="logout" value="Logout" id="logout">
<input type="hidden" name="token" value="',$T,'">
</p>
</form>
';$this->databasesPrint($td);if($_GET["ns"]!==""&&!$td&&DB!=""){echo'<p><a href="'.h(ME).'create="'.bold($_GET["create"]==="").">".'Create new table'."</a>\n";$uf=tables_list();if(!$uf)echo"<p class='message'>".'No tables.'."\n";else{$this->tablesPrint($uf);$ed=array();foreach($uf
as$R=>$U)$ed[]=preg_quote($R,'/');echo"<script type='text/javascript'>\n","var jushLinks = { $v: [ '".js_escape(ME)."table=\$&', /\\b(".implode("|",$ed).")\\b/g ] };\n";foreach(array("bac","bra","sqlite_quo","mssql_bra")as$X)echo"jushLinks.$X = jushLinks.$v;\n";echo"</script>\n";}}}}function
databasesPrint($td){global$f;$h=$this->databases();echo'<form action="">
<p id="dbs">
';hidden_fields_get();echo($h?html_select("db",array(""=>"(".'database'.")")+$h,DB,"this.form.submit();"):'<input name="db" value="'.h(DB).'">'),'<input type="submit" value="Use"',($h?" class='hidden'":""),'>
';if($td!="db"&&DB!=""&&$f->select_db(DB)){}echo(isset($_GET["sql"])?'<input type="hidden" name="sql" value="">':(isset($_GET["schema"])?'<input type="hidden" name="schema" value="">':(isset($_GET["dump"])?'<input type="hidden" name="dump" value="">':""))),"</p></form>\n";}function
tablesPrint($uf){echo"<p id='tables' onmouseover='menuOver(this, event);' onmouseout='menuOut(this);'>\n";foreach($uf
as$R=>$U){echo'<a href="'.h(ME).'select='.urlencode($R).'"'.bold($_GET["select"]==$R).">".'select'."</a> ",'<a href="'.h(ME).'table='.urlencode($R).'"'.bold($_GET["table"]==$R)." title='".'Show structure'."'>".$this->tableName(array("Name"=>$R))."</a><br>\n";}}}$b=(function_exists('adminer_object')?adminer_object():new
Adminer);if($b->operators===null)$b->operators=$Ld;function
page_header($Cf,$j="",$Fa=array(),$Df=""){global$ca,$b,$f,$zb;header("Content-Type: text/html; charset=utf-8");if($b->headers()){header("X-Frame-Options: deny");header("X-XSS-Protection: 0");}$Ef=$Cf.($Df!=""?": ".h($Df):"");$Ff=strip_tags($Ef.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".$b->name());echo'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html lang="en" dir="ltr">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta name="robots" content="noindex">
<title>',$Ff,'</title>
<link rel="stylesheet" type="text/css" href="',h(preg_replace("~\\?.*~","",ME))."?file=default.css&amp;version=3.6.1",'">
<script type="text/javascript" src="',h(preg_replace("~\\?.*~","",ME))."?file=functions.js&amp;version=3.6.1",'"></script>
';if($b->head()){echo'<link rel="shortcut icon" type="image/x-icon" href="',h(preg_replace("~\\?.*~","",ME))."?file=favicon.ico&amp;version=3.6.1",'" id="favicon">
';if(file_exists("adminer.css")){echo'<link rel="stylesheet" type="text/css" href="adminer.css">
';}}echo'
<body class="ltr nojs" onkeydown="bodyKeydown(event);" onclick="bodyClick(event);" onload="bodyLoad(\'',(is_object($f)?substr($f->server_info,0,3):""),'\');',(isset($_COOKIE["adminer_version"])?"":" verifyVersion();"),'">
<script type="text/javascript">
document.body.className = document.body.className.replace(/ nojs/, \' js\');
</script>

<div id="content">
';if($Fa!==null){$y=substr(preg_replace('~(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($y?$y:".").'">'.$zb[DRIVER].'</a> &raquo; ';$y=substr(preg_replace('~(db|ns)=[^&]*&~','',ME),0,-1);$N=(SERVER!=""?h(SERVER):'Server');if($Fa===false)echo"$N\n";else{echo"<a href='".($y?h($y):".")."' accesskey='1' title='Alt+Shift+1'>$N</a> &raquo; ";if($_GET["ns"]!=""||(DB!=""&&is_array($Fa)))echo'<a href="'.h($y."&db=".urlencode(DB).(support("scheme")?"&ns=":"")).'">'.h(DB).'</a> &raquo; ';if(is_array($Fa)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1)).'">'.h($_GET["ns"]).'</a> &raquo; ';foreach($Fa
as$w=>$X){$sb=(is_array($X)?$X[1]:$X);if($sb!="")echo'<a href="'.h(ME."$w=").urlencode(is_array($X)?$X[0]:$X).'">'.h($sb).'</a> &raquo; ';}}echo"$Cf\n";}}echo"<h2>$Ef</h2>\n";restart_session();$Zf=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$rd=$_SESSION["messages"][$Zf];if($rd){echo"<div class='message'>".implode("</div>\n<div class='message'>",$rd)."</div>\n";unset($_SESSION["messages"][$Zf]);}$h=&get_session("dbs");if(DB!=""&&$h&&!in_array(DB,$h,true))$h=null;stop_session();if($j)echo"<div class='error'>$j</div>\n";define("PAGE_HEADER",1);}function
page_footer($td=""){global$b;echo'</div>

<div id="menu">
';$b->navigation($td);echo'</div>
';}function
int32($B){while($B>=2147483648)$B-=4294967296;while($B<=-2147483649)$B+=4294967296;return(int)$B;}function
long2str($W,$ig){$L='';foreach($W
as$X)$L.=pack('V',$X);if($ig)return
substr($L,0,end($W));return$L;}function
str2long($L,$ig){$W=array_values(unpack('V*',str_pad($L,4*ceil(strlen($L)/4),"\0")));if($ig)$W[]=strlen($L);return$W;}function
xxtea_mx($ng,$mg,$mf,$Pc){return
int32((($ng>>5&0x7FFFFFF)^$mg<<2)+(($mg>>3&0x1FFFFFFF)^$ng<<4))^int32(($mf^$mg)+($Pc^$ng));}function
encrypt_string($hf,$w){if($hf=="")return"";$w=array_values(unpack("V*",pack("H*",md5($w))));$W=str2long($hf,true);$B=count($W)-1;$ng=$W[$B];$mg=$W[0];$F=floor(6+52/($B+1));$mf=0;while($F-->0){$mf=int32($mf+0x9E3779B9);$Db=$mf>>2&3;for($Yd=0;$Yd<$B;$Yd++){$mg=$W[$Yd+1];$vd=xxtea_mx($ng,$mg,$mf,$w[$Yd&3^$Db]);$ng=int32($W[$Yd]+$vd);$W[$Yd]=$ng;}$mg=$W[0];$vd=xxtea_mx($ng,$mg,$mf,$w[$Yd&3^$Db]);$ng=int32($W[$B]+$vd);$W[$B]=$ng;}return
long2str($W,false);}function
decrypt_string($hf,$w){if($hf=="")return"";$w=array_values(unpack("V*",pack("H*",md5($w))));$W=str2long($hf,false);$B=count($W)-1;$ng=$W[$B];$mg=$W[0];$F=floor(6+52/($B+1));$mf=int32($F*0x9E3779B9);while($mf){$Db=$mf>>2&3;for($Yd=$B;$Yd>0;$Yd--){$ng=$W[$Yd-1];$vd=xxtea_mx($ng,$mg,$mf,$w[$Yd&3^$Db]);$mg=int32($W[$Yd]-$vd);$W[$Yd]=$mg;}$ng=$W[$B];$vd=xxtea_mx($ng,$mg,$mf,$w[$Yd&3^$Db]);$mg=int32($W[0]-$vd);$W[0]=$mg;$mf=int32($mf-0x9E3779B9);}return
long2str($W,true);}$f='';$T=$_SESSION["token"];if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);$ie=array();if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$X){list($w)=explode(":",$X);$ie[$w]=$X;}}$c=$_POST["auth"];if($c){session_regenerate_id();$_SESSION["pwds"][$c["driver"]][$c["server"]][$c["username"]]=$c["password"];$_SESSION["db"][$c["driver"]][$c["server"]][$c["username"]][$c["db"]]=true;if($c["permanent"]){$w=base64_encode($c["driver"])."-".base64_encode($c["server"])."-".base64_encode($c["username"])."-".base64_encode($c["db"]);$se=$b->permanentLogin();$ie[$w]="$w:".base64_encode($se?encrypt_string($c["password"],$se):"");cookie("adminer_permanent",implode(" ",$ie));}if(count($_POST)==1||DRIVER!=$c["driver"]||SERVER!=$c["server"]||$_GET["username"]!==$c["username"]||DB!=$c["db"])redirect(auth_url($c["driver"],$c["server"],$c["username"],$c["db"]));}elseif($_POST["logout"]){if($T&&$_POST["token"]!=$T){page_header('Logout','Invalid CSRF token. Send the form again.');page_footer("db");exit;}else{foreach(array("pwds","db","dbs","queries")as$w)set_session($w,null);unset_permanent();redirect(substr(preg_replace('~(username|db|ns)=[^&]*&~','',ME),0,-1),'Logout successful.');}}elseif($ie&&!$_SESSION["pwds"]){session_regenerate_id();$se=$b->permanentLogin();foreach($ie
as$w=>$X){list(,$Ma)=explode(":",$X);list($yb,$N,$V,$i)=array_map('base64_decode',explode("-",$w));$_SESSION["pwds"][$yb][$N][$V]=decrypt_string(base64_decode($Ma),$se);$_SESSION["db"][$yb][$N][$V][$i]=true;}}function
unset_permanent(){global$ie;foreach($ie
as$w=>$X){list($yb,$N,$V)=array_map('base64_decode',explode("-",$w));if($yb==DRIVER&&$N==SERVER&&$i==$_GET["username"])unset($ie[$w]);}cookie("adminer_permanent",implode(" ",$ie));}function
auth_error($Ub=null){global$f,$b,$T;$af=session_name();$j="";if(!$_COOKIE[$af]&&$_GET[$af]&&ini_bool("session.use_only_cookies"))$j='Session support must be enabled.';elseif(isset($_GET["username"])){if(($_COOKIE[$af]||$_GET[$af])&&!$T)$j='Session expired, please login again.';else{$he=&get_session("pwds");if($he!==null){$j=h($Ub?$Ub->getMessage():(is_string($f)?$f:'Invalid credentials.'));$he=null;}unset_permanent();}}page_header('Login',$j,null);echo"<form action='' method='post'>\n";$b->loginForm();echo"<div>";hidden_fields($_POST,array("auth"));echo"</div>\n","</form>\n";page_footer("auth");}if(isset($_GET["username"])){if(!class_exists("Min_DB")){unset($_SESSION["pwds"][DRIVER]);unset_permanent();page_header('No extension',sprintf('None of the supported PHP extensions (%s) are available.',implode(", ",$ne)),false);page_footer("auth");exit;}$f=connect();}if(is_string($f)||!$b->login($_GET["username"],get_session("pwds"))){auth_error();exit;}$T=$_SESSION["token"];if($c&&$_POST["token"])$_POST["token"]=$T;$j=($_POST?($_POST["token"]==$T?"":'Invalid CSRF token. Send the form again.'):($_SERVER["REQUEST_METHOD"]!="POST"?"":sprintf('Too big POST data. Reduce the data or increase the %s configuration directive.','"post_max_size"')));if(!ini_bool("session.use_cookies")||@ini_set("session.use_cookies",false)!==false){session_cache_limiter("");session_write_close();}function
connect_error(){global$b,$f,$T,$j,$zb;$h=array();if(DB!="")page_header('Database'.": ".h(DB),'Invalid database.',true);else{if($_POST["db"]&&!$j)queries_redirect(substr(ME,0,-1),'Databases have been dropped.',drop_databases($_POST["db"]));page_header('Select database',$j,false);echo"<p><a href='".h(ME)."database='>".'Create new database'."</a>\n";foreach(array('privileges'=>'Privileges','processlist'=>'Process list','variables'=>'Variables','status'=>'Status',)as$w=>$X){if(support($w))echo"<a href='".h(ME)."$w='>$X</a>\n";}echo"<p>".sprintf('%s version: %s through PHP extension %s',$zb[DRIVER],"<b>$f->server_info</b>","<b>$f->extension</b>")."\n","<p>".sprintf('Logged as: %s',"<b>".h(logged_user())."</b>")."\n";$Ee="<a href='".h(ME)."refresh=1'>".'Refresh'."</a>\n";$h=$b->databases();if($h){$Ue=support("scheme");$d=collations();echo"<form action='' method='post'>\n","<table cellspacing='0' class='checkable' onclick='tableClick(event);'>\n","<thead><tr><td>&nbsp;<th>".'Database'."<td>".'Collation'."<td>".'Tables'."</thead>\n";foreach($h
as$i){$Ne=h(ME)."db=".urlencode($i);echo"<tr".odd()."><td>".checkbox("db[]",$i,in_array($i,(array)$_POST["db"])),"<th><a href='$Ne'>".h($i)."</a>","<td><a href='$Ne".($Ue?"&amp;ns=":"")."&amp;database=' title='".'Alter database'."'>".nbsp(db_collation($i,$d))."</a>","<td align='right'><a href='$Ne&amp;schema=' id='tables-".h($i)."' title='".'Database schema'."'>?</a>","\n";}echo"</table>\n","<script type='text/javascript'>tableCheck();</script>\n","<p><input type='submit' name='drop' value='".'Drop'."'".confirm("formChecked(this, /db/)").">\n","<input type='hidden' name='token' value='$T'>\n",$Ee,"</form>\n";}else
echo"<p>$Ee";}page_footer("db");if($h)echo"<script type='text/javascript'>ajaxSetHtml('".js_escape(ME)."script=connect');</script>\n";}if(isset($_GET["status"]))$_GET["variables"]=$_GET["status"];if(!(DB!=""?$f->select_db(DB):isset($_GET["sql"])||isset($_GET["dump"])||isset($_GET["database"])||isset($_GET["processlist"])||isset($_GET["privileges"])||isset($_GET["user"])||isset($_GET["variables"])||$_GET["script"]=="connect"||$_GET["script"]=="kill")){if(DB!=""||$_GET["refresh"]){restart_session();set_session("dbs",null);}connect_error();exit;}function
select($H,$g=null,$_c="",$Rd=array()){$ed=array();$t=array();$e=array();$Da=array();$Qf=array();$I=array();odd('');for($q=0;$J=$H->fetch_row();$q++){if(!$q){echo"<table cellspacing='0' class='nowrap'>\n","<thead><tr>";for($u=0;$u<count($J);$u++){$k=$H->fetch_field();$C=$k->name;$Qd=$k->orgtable;$Pd=$k->orgname;$I[$k->table]=$Qd;if($_c)$ed[$u]=($C=="table"?"table=":($C=="possible_keys"?"indexes=":null));elseif($Qd!=""){if(!isset($t[$Qd])){$t[$Qd]=array();foreach(indexes($Qd,$g)as$s){if($s["type"]=="PRIMARY"){$t[$Qd]=array_flip($s["columns"]);break;}}$e[$Qd]=$t[$Qd];}if(isset($e[$Qd][$Pd])){unset($e[$Qd][$Pd]);$t[$Qd][$Pd]=$u;$ed[$u]=$Qd;}}if($k->charsetnr==63)$Da[$u]=true;$Qf[$u]=$k->type;$C=h($C);echo"<th".($Qd!=""||$k->name!=$Pd?" title='".h(($Qd!=""?"$Qd.":"").$Pd)."'":"").">".($_c?"<a href='$_c".strtolower($C)."' target='_blank' rel='noreferrer'>$C</a>":$C);}echo"</thead>\n";}echo"<tr".odd().">";foreach($J
as$w=>$X){if($X===null)$X="<i>NULL</i>";elseif($Da[$w]&&!is_utf8($X))$X="<i>".lang(array('%d byte','%d bytes'),strlen($X))."</i>";elseif(!strlen($X))$X="&nbsp;";else{$X=h($X);if($Qf[$w]==254)$X="<code>$X</code>";}if(isset($ed[$w])&&!$e[$ed[$w]]){if($_c){$R=$J[array_search("table=",$ed)];$y=$ed[$w].urlencode($Rd[$R]!=""?$Rd[$R]:$R);}else{$y="edit=".urlencode($ed[$w]);foreach($t[$ed[$w]]as$Pa=>$u)$y.="&where".urlencode("[".bracket_escape($Pa)."]")."=".urlencode($J[$u]);}$X="<a href='".h(ME.$y)."'>$X</a>";}echo"<td>$X";}}echo($q?"</table>":"<p class='message'>".'No rows.')."\n";return$I;}function
referencable_primary($We){$I=array();foreach(table_status()as$qf=>$R){if($qf!=$We&&fk_support($R)){foreach(fields($qf)as$k){if($k["primary"]){if($I[$qf]){unset($I[$qf]);break;}$I[$qf]=$k;}}}}return$I;}function
textarea($C,$Y,$K=10,$Sa=80){echo"<textarea name='$C' rows='$K' cols='$Sa' class='sqlarea' spellcheck='false' wrap='off' onkeydown='return textareaKeydown(this, event);'>";if(is_array($Y)){foreach($Y
as$X)echo
h($X[0])."\n\n\n";}else
echo
h($Y);echo"</textarea>";}function
format_time($ef,$Lb){return" <span class='time'>(".sprintf('%.3f s',max(0,array_sum(explode(" ",$Lb))-array_sum(explode(" ",$ef)))).")</span>";}function
edit_type($w,$k,$d,$n=array()){global$if,$Qf,$Xf,$Hd;echo'<td><select name="',$w,'[type]" class="type" onfocus="lastType = selectValue(this);" onchange="editingTypeChange(this);">',optionlist((!$k["type"]||isset($Qf[$k["type"]])?array():array($k["type"]))+$if+($n?array('Foreign keys'=>$n):array()),$k["type"]),'</select>
<td><input name="',$w,'[length]" value="',h($k["length"]),'" size="3" onfocus="editingLengthFocus(this);"><td class="options">',"<select name='$w"."[collation]'".(ereg('(char|text|enum|set)$',$k["type"])?"":" class='hidden'").'><option value="">('.'collation'.')'.optionlist($d,$k["collation"]).'</select>',($Xf?"<select name='$w"."[unsigned]'".(!$k["type"]||ereg('(int|float|double|decimal)$',$k["type"])?"":" class='hidden'").'><option>'.optionlist($Xf,$k["unsigned"]).'</select>':''),($n?"<select name='$w"."[on_delete]'".(ereg("`",$k["type"])?"":" class='hidden'")."><option value=''>(".'ON DELETE'.")".optionlist(explode("|",$Hd),$k["on_delete"])."</select> ":" ");}function
process_length($bd){global$Ob;return(preg_match("~^\\s*(?:$Ob)(?:\\s*,\\s*(?:$Ob))*\\s*\$~",$bd)&&preg_match_all("~$Ob~",$bd,$hd)?implode(",",$hd[0]):preg_replace('~[^0-9,+-]~','',$bd));}function
process_type($k,$Qa="COLLATE"){global$Xf;return" $k[type]".($k["length"]!=""?"(".process_length($k["length"]).")":"").(ereg('int|float|double|decimal',$k["type"])&&in_array($k["unsigned"],$Xf)?" $k[unsigned]":"").(ereg('char|text|enum|set',$k["type"])&&$k["collation"]?" $Qa ".q($k["collation"]):"");}function
process_field($k,$Of){return
array(idf_escape(trim($k["field"])),process_type($Of),($k["null"]?" NULL":" NOT NULL"),(isset($k["default"])?" DEFAULT ".(($k["type"]=="timestamp"&&eregi('^CURRENT_TIMESTAMP$',$k["default"]))||($k["type"]=="bit"&&ereg("^([0-9]+|b'[0-1]+')\$",$k["default"]))?$k["default"]:q($k["default"])):""),($k["on_update"]?" ON UPDATE $k[on_update]":""),(support("comment")&&$k["comment"]!=""?" COMMENT ".q($k["comment"]):""),($k["auto_increment"]?auto_increment():null),);}function
type_class($U){foreach(array('char'=>'text','date'=>'time|year','binary'=>'blob','enum'=>'set',)as$w=>$X){if(ereg("$w|$X",$U))return" class='$w'";}}function
edit_fields($l,$d,$U="TABLE",$ra=0,$n=array(),$Xa=false){global$Gc;echo'<thead><tr class="wrap">
';if($U=="PROCEDURE"){echo'<td>&nbsp;';}echo'<th>',($U=="TABLE"?'Column name':'Parameter name'),'<td>Type<textarea id="enum-edit" rows="4" cols="12" wrap="off" style="display: none;" onblur="editingLengthBlur(this);"></textarea>
<td>Length
<td>Options
';if($U=="TABLE"){echo'<td>NULL
<td><input type="radio" name="auto_increment_col" value=""><acronym title="Auto Increment">AI</acronym>
<td',($_POST["defaults"]?"":" class='hidden'"),'>Default values
',(support("comment")?"<td".($Xa?"":" class='hidden'").">".'Comment':"");}echo'<td>',"<input type='image' class='icon' name='add[".(support("move_col")?0:count($l))."]' src='".h(preg_replace("~\\?.*~","",ME))."?file=plus.gif&amp;version=3.6.1' alt='+' title='".'Add next'."'>",'<script type="text/javascript">row_count = ',count($l),';</script>
</thead>
<tbody onkeydown="return editingKeydown(event);">
';foreach($l
as$q=>$k){$q++;$Sd=$k[($_POST?"orig":"field")];$wb=(isset($_POST["add"][$q-1])||(isset($k["field"])&&!$_POST["drop_col"][$q]))&&(support("drop_col")||$Sd=="");echo'<tr',($wb?"":" style='display: none;'"),'>
',($U=="PROCEDURE"?"<td>".html_select("fields[$q][inout]",explode("|",$Gc),$k["inout"]):""),'<th>';if($wb){echo'<input name="fields[',$q,'][field]" value="',h($k["field"]),'" onchange="',($k["field"]!=""||count($l)>1?"":"editingAddRow(this, $ra); "),'editingNameChange(this);" maxlength="64">';}echo'<input type="hidden" name="fields[',$q,'][orig]" value="',h($Sd),'">
';edit_type("fields[$q]",$k,$d,$n);if($U=="TABLE"){echo'<td>',checkbox("fields[$q][null]",1,$k["null"]),'<td><input type="radio" name="auto_increment_col" value="',$q,'"';if($k["auto_increment"]){echo' checked';}?> onclick="var field = this.form['fields[' + this.value + '][field]']; if (!field.value) { field.value = 'id'; field.onchange(); }">
<td<?php echo($_POST["defaults"]?"":" class='hidden'"),'>',checkbox("fields[$q][has_default]",1,$k["has_default"]),'<input name="fields[',$q,'][default]" value="',h($k["default"]),'" onchange="this.previousSibling.checked = true;">
',(support("comment")?"<td".($Xa?"":" class='hidden'")."><input name='fields[$q][comment]' value='".h($k["comment"])."' maxlength='255'>":"");}echo"<td>",(support("move_col")?"<input type='image' class='icon' name='add[$q]' src='".h(preg_replace("~\\?.*~","",ME))."?file=plus.gif&amp;version=3.6.1' alt='+' title='".'Add next'."' onclick='return !editingAddRow(this, $ra, 1);'>&nbsp;"."<input type='image' class='icon' name='up[$q]' src='".h(preg_replace("~\\?.*~","",ME))."?file=up.gif&amp;version=3.6.1' alt='^' title='".'Move up'."'>&nbsp;"."<input type='image' class='icon' name='down[$q]' src='".h(preg_replace("~\\?.*~","",ME))."?file=down.gif&amp;version=3.6.1' alt='v' title='".'Move down'."'>&nbsp;":""),($Sd==""||support("drop_col")?"<input type='image' class='icon' name='drop_col[$q]' src='".h(preg_replace("~\\?.*~","",ME))."?file=cross.gif&amp;version=3.6.1' alt='x' title='".'Remove'."' onclick='return !editingRemoveRow(this);'>":""),"\n";}}function
process_fields(&$l){ksort($l);$Bd=0;if($_POST["up"]){$Vc=0;foreach($l
as$w=>$k){if(key($_POST["up"])==$w){unset($l[$w]);array_splice($l,$Vc,0,array($k));break;}if(isset($k["field"]))$Vc=$Bd;$Bd++;}}if($_POST["down"]){$mc=false;foreach($l
as$w=>$k){if(isset($k["field"])&&$mc){unset($l[key($_POST["down"])]);array_splice($l,$Bd,0,array($mc));break;}if(key($_POST["down"])==$w)$mc=$k;$Bd++;}}$l=array_values($l);if($_POST["add"])array_splice($l,key($_POST["add"]),0,array(array()));}function
normalize_enum($_){return"'".str_replace("'","''",addcslashes(stripcslashes(str_replace($_[0][0].$_[0][0],$_[0][0],substr($_[0],1,-1))),'\\'))."'";}function
grant($p,$ue,$e,$Gd){if(!$ue)return
true;if($ue==array("ALL PRIVILEGES","GRANT OPTION"))return($p=="GRANT"?queries("$p ALL PRIVILEGES$Gd WITH GRANT OPTION"):queries("$p ALL PRIVILEGES$Gd")&&queries("$p GRANT OPTION$Gd"));return
queries("$p ".preg_replace('~(GRANT OPTION)\\([^)]*\\)~','\\1',implode("$e, ",$ue).$e).$Gd);}function
drop_create($_b,$fb,$z,$qd,$od,$pd,$C){if($_POST["drop"])return
query_redirect($_b,$z,$qd,true,!$_POST["dropped"]);$Ab=$C!=""&&($_POST["dropped"]||queries($_b));$hb=queries($fb);if(!queries_redirect($z,($C!=""?$od:$pd),$hb)&&$Ab)redirect(null,$qd);return$Ab;}function
remove_definer($G){return
preg_replace('~^([A-Z =]+) DEFINER=`'.preg_replace('~@(.*)~','`@`(%|\\1)',logged_user()).'`~','\\1',$G);}function
tar_file($fc,$bb){$I=pack("a100a8a8a8a12a12",$fc,644,0,0,decoct(strlen($bb)),decoct(time()));$La=8*32;for($q=0;$q<strlen($I);$q++)$La+=ord($I[$q]);$I.=sprintf("%06o",$La)."\0 ";return$I.str_repeat("\0",512-strlen($I)).$bb.str_repeat("\0",511-(strlen($bb)+511)%512);}function
ini_bytes($Fc){$X=ini_get($Fc);switch(strtolower(substr($X,-1))){case'g':$X*=1024;case'm':$X*=1024;case'k':$X*=1024;}return$X;}$Hd="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";$Ob="'(?:''|[^'\\\\]|\\\\.)*+'";$Gc="IN|OUT|INOUT";if(isset($_GET["select"])&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["callf"]))$_GET["call"]=$_GET["callf"];if(isset($_GET["function"]))$_GET["procedure"]=$_GET["function"];if(isset($_GET["download"])){$a=$_GET["download"];header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$_GET["where"])).".".friendly_url($_GET["field"]));echo$f->result("SELECT".limit(idf_escape($_GET["field"])." FROM ".table($a)," WHERE ".where($_GET),1));exit;}elseif(isset($_GET["table"])){$a=$_GET["table"];$l=fields($a);if(!$l)$j=error();$S=($l?table_status($a):array());page_header(($l&&is_view($S)?'View':'Table').": ".h($a),$j);$b->selectLinks($S);$Wa=$S["Comment"];if($Wa!="")echo"<p>".'Comment'.": ".h($Wa)."\n";if($l){echo"<table cellspacing='0'>\n","<thead><tr><th>".'Column'."<td>".'Type'.(support("comment")?"<td>".'Comment':"")."</thead>\n";foreach($l
as$k){echo"<tr".odd()."><th>".h($k["field"]),"<td title='".h($k["collation"])."'>".h($k["full_type"]).($k["null"]?" <i>NULL</i>":"").($k["auto_increment"]?" <i>".'Auto Increment'."</i>":""),(isset($k["default"])?" [<b>".h($k["default"])."</b>]":""),(support("comment")?"<td>".nbsp($k["comment"]):""),"\n";}echo"</table>\n";if(!is_view($S)){echo"<h3>".'Indexes'."</h3>\n";$t=indexes($a);if($t){echo"<table cellspacing='0'>\n";foreach($t
as$C=>$s){ksort($s["columns"]);$re=array();foreach($s["columns"]as$w=>$X)$re[]="<i>".h($X)."</i>".($s["lengths"][$w]?"(".$s["lengths"][$w].")":"");echo"<tr title='".h($C)."'><th>$s[type]<td>".implode(", ",$re)."\n";}echo"</table>\n";}echo'<p><a href="'.h(ME).'indexes='.urlencode($a).'">'.'Alter indexes'."</a>\n";if(fk_support($S)){echo"<h3>".'Foreign keys'."</h3>\n";$n=foreign_keys($a);if($n){echo"<table cellspacing='0'>\n","<thead><tr><th>".'Source'."<td>".'Target'."<td>".'ON DELETE'."<td>".'ON UPDATE'.($v!="sqlite"?"<td>&nbsp;":"")."</thead>\n";foreach($n
as$C=>$m){echo"<tr title='".h($C)."'>","<th><i>".implode("</i>, <i>",array_map('h',$m["source"]))."</i>","<td><a href='".h($m["db"]!=""?preg_replace('~db=[^&]*~',"db=".urlencode($m["db"]),ME):($m["ns"]!=""?preg_replace('~ns=[^&]*~',"ns=".urlencode($m["ns"]),ME):ME))."table=".urlencode($m["table"])."'>".($m["db"]!=""?"<b>".h($m["db"])."</b>.":"").($m["ns"]!=""?"<b>".h($m["ns"])."</b>.":"").h($m["table"])."</a>","(<i>".implode("</i>, <i>",array_map('h',$m["target"]))."</i>)","<td>".nbsp($m["on_delete"])."\n","<td>".nbsp($m["on_update"])."\n",($v=="sqlite"?"":'<td><a href="'.h(ME.'foreign='.urlencode($a).'&name='.urlencode($C)).'">'.'Alter'.'</a>');}echo"</table>\n";}if($v!="sqlite")echo'<p><a href="'.h(ME).'foreign='.urlencode($a).'">'.'Add foreign key'."</a>\n";}if(support("trigger")){echo"<h3>".'Triggers'."</h3>\n";$Nf=triggers($a);if($Nf){echo"<table cellspacing='0'>\n";foreach($Nf
as$w=>$X)echo"<tr valign='top'><td>$X[0]<td>$X[1]<th>".h($w)."<td><a href='".h(ME.'trigger='.urlencode($a).'&name='.urlencode($w))."'>".'Alter'."</a>\n";echo"</table>\n";}echo'<p><a href="'.h(ME).'trigger='.urlencode($a).'">'.'Add trigger'."</a>\n";}}}}elseif(isset($_GET["schema"])){page_header('Database schema',"",array(),DB.($_GET["ns"]?".$_GET[ns]":""));$rf=array();$sf=array();$C="adminer_schema";$ea=($_GET["schema"]?$_GET["schema"]:$_COOKIE[($_COOKIE["$C-".DB]?"$C-".DB:$C)]);preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~',$ea,$hd,PREG_SET_ORDER);foreach($hd
as$q=>$_){$rf[$_[1]]=array($_[2],$_[3]);$sf[]="\n\t'".js_escape($_[1])."': [ $_[2], $_[3] ]";}$Gf=0;$Aa=-1;$Te=array();$De=array();$Zc=array();foreach(table_status()as$S){if(!isset($S["Engine"]))continue;$ke=0;$Te[$S["Name"]]["fields"]=array();foreach(fields($S["Name"])as$C=>$k){$ke+=1.25;$k["pos"]=$ke;$Te[$S["Name"]]["fields"][$C]=$k;}$Te[$S["Name"]]["pos"]=($rf[$S["Name"]]?$rf[$S["Name"]]:array($Gf,0));foreach($b->foreignKeys($S["Name"])as$X){if(!$X["db"]){$Xc=$Aa;if($rf[$S["Name"]][1]||$rf[$X["table"]][1])$Xc=min(floatval($rf[$S["Name"]][1]),floatval($rf[$X["table"]][1]))-1;else$Aa-=.1;while($Zc[(string)$Xc])$Xc-=.0001;$Te[$S["Name"]]["references"][$X["table"]][(string)$Xc]=array($X["source"],$X["target"]);$De[$X["table"]][$S["Name"]][(string)$Xc]=$X["target"];$Zc[(string)$Xc]=true;}}$Gf=max($Gf,$Te[$S["Name"]]["pos"][0]+2.5+$ke);}echo'<div id="schema" style="height: ',$Gf,'em;" onselectstart="return false;">
<script type="text/javascript">
var tablePos = {',implode(",",$sf)."\n",'};
var em = document.getElementById(\'schema\').offsetHeight / ',$Gf,';
document.onmousemove = schemaMousemove;
document.onmouseup = function (ev) {
	schemaMouseup(ev, \'',js_escape(DB),'\');
};
</script>
';foreach($Te
as$C=>$R){echo"<div class='table' style='top: ".$R["pos"][0]."em; left: ".$R["pos"][1]."em;' onmousedown='schemaMousedown(this, event);'>",'<a href="'.h(ME).'table='.urlencode($C).'"><b>'.h($C)."</b></a>";foreach($R["fields"]as$k){$X='<span'.type_class($k["type"]).' title="'.h($k["full_type"].($k["null"]?" NULL":'')).'">'.h($k["field"]).'</span>';echo"<br>".($k["primary"]?"<i>$X</i>":$X);}foreach((array)$R["references"]as$yf=>$Fe){foreach($Fe
as$Xc=>$Ae){$Yc=$Xc-$rf[$C][1];$q=0;foreach($Ae[0]as$bf)echo"\n<div class='references' title='".h($yf)."' id='refs$Xc-".($q++)."' style='left: $Yc"."em; top: ".$R["fields"][$bf]["pos"]."em; padding-top: .5em;'><div style='border-top: 1px solid Gray; width: ".(-$Yc)."em;'></div></div>";}}foreach((array)$De[$C]as$yf=>$Fe){foreach($Fe
as$Xc=>$e){$Yc=$Xc-$rf[$C][1];$q=0;foreach($e
as$xf)echo"\n<div class='references' title='".h($yf)."' id='refd$Xc-".($q++)."' style='left: $Yc"."em; top: ".$R["fields"][$xf]["pos"]."em; height: 1.25em; background: url(".h(preg_replace("~\\?.*~","",ME))."?file=arrow.gif) no-repeat right center;&amp;version=3.6.1'><div style='height: .5em; border-bottom: 1px solid Gray; width: ".(-$Yc)."em;'></div></div>";}}echo"\n</div>\n";}foreach($Te
as$C=>$R){foreach((array)$R["references"]as$yf=>$Fe){foreach($Fe
as$Xc=>$Ae){$sd=$Gf;$ld=-10;foreach($Ae[0]as$w=>$bf){$le=$R["pos"][0]+$R["fields"][$bf]["pos"];$me=$Te[$yf]["pos"][0]+$Te[$yf]["fields"][$Ae[1][$w]]["pos"];$sd=min($sd,$le,$me);$ld=max($ld,$le,$me);}echo"<div class='references' id='refl$Xc' style='left: $Xc"."em; top: $sd"."em; padding: .5em 0;'><div style='border-right: 1px solid Gray; margin-top: 1px; height: ".($ld-$sd)."em;'></div></div>\n";}}}echo'</div>
<p><a href="',h(ME."schema=".urlencode($ea)),'" id="schema-link">Permanent link</a>
';}elseif(isset($_GET["dump"])){$a=$_GET["dump"];if($_POST){$db="";foreach(array("output","format","db_style","routines","events","table_style","auto_increment","triggers","data_style")as$w)$db.="&$w=".urlencode($_POST[$w]);cookie("adminer_export",substr($db,1));$ac=dump_headers(($a!=""?$a:DB),(DB==""||count((array)$_POST["tables"]+(array)$_POST["data"])>1));$Lc=($_POST["format"]=="sql");if($Lc)echo"-- Adminer $ga ".$zb[DRIVER]." dump

".($v!="sql"?"":"SET NAMES utf8;
".($_POST["data_style"]?"SET foreign_key_checks = 0;
SET time_zone = ".q($f->result("SELECT @@time_zone")).";
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
":"")."
");$Q=$_POST["db_style"];$h=array(DB);if(DB==""){$h=$_POST["databases"];if(is_string($h))$h=explode("\n",rtrim(str_replace("\r","",$h),"\n"));}foreach((array)$h
as$i){if($f->select_db($i)){if($Lc&&ereg('CREATE',$Q)&&($fb=$f->result("SHOW CREATE DATABASE ".idf_escape($i),1))){if($Q=="DROP+CREATE")echo"DROP DATABASE IF EXISTS ".idf_escape($i).";\n";echo($Q=="CREATE+ALTER"?preg_replace('~^CREATE DATABASE ~','\\0IF NOT EXISTS ',$fb):$fb).";\n";}if($Lc){if($Q)echo
use_sql($i).";\n\n";if(in_array("CREATE+ALTER",array($Q,$_POST["table_style"])))echo"SET @adminer_alter = '';\n\n";$Wd="";if($_POST["routines"]){foreach(array("FUNCTION","PROCEDURE")as$Oe){foreach(get_rows("SHOW $Oe STATUS WHERE Db = ".q($i),null,"-- ")as$J)$Wd.=($Q!='DROP+CREATE'?"DROP $Oe IF EXISTS ".idf_escape($J["Name"]).";;\n":"").remove_definer($f->result("SHOW CREATE $Oe ".idf_escape($J["Name"]),2)).";;\n\n";}}if($_POST["events"]){foreach(get_rows("SHOW EVENTS",null,"-- ")as$J)$Wd.=($Q!='DROP+CREATE'?"DROP EVENT IF EXISTS ".idf_escape($J["Name"]).";;\n":"").remove_definer($f->result("SHOW CREATE EVENT ".idf_escape($J["Name"]),3)).";;\n\n";}if($Wd)echo"DELIMITER ;;\n\n$Wd"."DELIMITER ;\n\n";}if($_POST["table_style"]||$_POST["data_style"]){$gg=array();foreach(table_status()as$S){$R=(DB==""||in_array($S["Name"],(array)$_POST["tables"]));$kb=(DB==""||in_array($S["Name"],(array)$_POST["data"]));if($R||$kb){if(!is_view($S)){if($ac=="tar")ob_start();$b->dumpTable($S["Name"],($R?$_POST["table_style"]:""));if($kb)$b->dumpData($S["Name"],$_POST["data_style"],"SELECT * FROM ".table($S["Name"]));if($Lc&&$_POST["triggers"]&&$R&&($Nf=trigger_sql($S["Name"],$_POST["table_style"])))echo"\nDELIMITER ;;\n$Nf\nDELIMITER ;\n";if($ac=="tar")echo
tar_file((DB!=""?"":"$i/")."$S[Name].csv",ob_get_clean());elseif($Lc)echo"\n";}elseif($Lc)$gg[]=$S["Name"];}}foreach($gg
as$fg)$b->dumpTable($fg,$_POST["table_style"],true);if($ac=="tar")echo
pack("x512");}if($Q=="CREATE+ALTER"&&$Lc){$G="SELECT TABLE_NAME, ENGINE, TABLE_COLLATION, TABLE_COMMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE()";echo"DELIMITER ;;
CREATE PROCEDURE adminer_alter (INOUT alter_command text) BEGIN
	DECLARE _table_name, _engine, _table_collation varchar(64);
	DECLARE _table_comment varchar(64);
	DECLARE done bool DEFAULT 0;
	DECLARE tables CURSOR FOR $G;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
	OPEN tables;
	REPEAT
		FETCH tables INTO _table_name, _engine, _table_collation, _table_comment;
		IF NOT done THEN
			CASE _table_name";foreach(get_rows($G)as$J){$Wa=q($J["ENGINE"]=="InnoDB"?preg_replace('~(?:(.+); )?InnoDB free: .*~','\\1',$J["TABLE_COMMENT"]):$J["TABLE_COMMENT"]);echo"
				WHEN ".q($J["TABLE_NAME"])." THEN
					".(isset($J["ENGINE"])?"IF _engine != '$J[ENGINE]' OR _table_collation != '$J[TABLE_COLLATION]' OR _table_comment != $Wa THEN
						ALTER TABLE ".idf_escape($J["TABLE_NAME"])." ENGINE=$J[ENGINE] COLLATE=$J[TABLE_COLLATION] COMMENT=$Wa;
					END IF":"BEGIN END").";";}echo"
				ELSE
					SET alter_command = CONCAT(alter_command, 'DROP TABLE `', REPLACE(_table_name, '`', '``'), '`;\\n');
			END CASE;
		END IF;
	UNTIL done END REPEAT;
	CLOSE tables;
END;;
DELIMITER ;
CALL adminer_alter(@adminer_alter);
DROP PROCEDURE adminer_alter;
";}if(in_array("CREATE+ALTER",array($Q,$_POST["table_style"]))&&$Lc)echo"SELECT @adminer_alter;\n";}}if($Lc)echo"-- ".$f->result("SELECT NOW()")."\n";exit;}page_header('Export',"",($_GET["export"]!=""?array("table"=>$_GET["export"]):array()),DB);echo'
<form action="" method="post">
<table cellspacing="0">
';$nb=array('','USE','DROP+CREATE','CREATE');$tf=array('','DROP+CREATE','CREATE');$lb=array('','TRUNCATE+INSERT','INSERT');if($v=="sql"){$nb[]='CREATE+ALTER';$tf[]='CREATE+ALTER';$lb[]='INSERT+UPDATE';}parse_str($_COOKIE["adminer_export"],$J);if(!$J)$J=array("output"=>"text","format"=>"sql","db_style"=>(DB!=""?"":"CREATE"),"table_style"=>"DROP+CREATE","data_style"=>"INSERT");if(!isset($J["events"])){$J["routines"]=$J["events"]=($_GET["dump"]=="");$J["triggers"]=$J["table_style"];}echo"<tr><th>".'Output'."<td>".html_select("output",$b->dumpOutput(),$J["output"],0)."\n";echo"<tr><th>".'Format'."<td>".html_select("format",$b->dumpFormat(),$J["format"],0)."\n";echo($v=="sqlite"?"":"<tr><th>".'Database'."<td>".html_select('db_style',$nb,$J["db_style"]).(support("routine")?checkbox("routines",1,$J["routines"],'Routines'):"").(support("event")?checkbox("events",1,$J["events"],'Events'):"")),"<tr><th>".'Tables'."<td>".html_select('table_style',$tf,$J["table_style"]).checkbox("auto_increment",1,$J["auto_increment"],'Auto Increment').(support("trigger")?checkbox("triggers",1,$J["triggers"],'Triggers'):""),"<tr><th>".'Data'."<td>".html_select('data_style',$lb,$J["data_style"]),'</table>
<p><input type="submit" value="Export">

<table cellspacing="0">
';$pe=array();if(DB!=""){$Ka=($a!=""?"":" checked");echo"<thead><tr>","<th style='text-align: left;'><label><input type='checkbox' id='check-tables'$Ka onclick='formCheck(this, /^tables\\[/);'>".'Tables'."</label>","<th style='text-align: right;'><label>".'Data'."<input type='checkbox' id='check-data'$Ka onclick='formCheck(this, /^data\\[/);'></label>","</thead>\n";$gg="";foreach(table_status()as$S){$C=$S["Name"];$oe=ereg_replace("_.*","",$C);$Ka=($a==""||$a==(substr($a,-1)=="%"?"$oe%":$C));$re="<tr><td>".checkbox("tables[]",$C,$Ka,$C,"checkboxClick(event, this); formUncheck('check-tables');");if(is_view($S))$gg.="$re\n";else
echo"$re<td align='right'><label>".($S["Engine"]=="InnoDB"&&$S["Rows"]?"~ ":"").$S["Rows"].checkbox("data[]",$C,$Ka,"","checkboxClick(event, this); formUncheck('check-data');")."</label>\n";$pe[$oe]++;}echo$gg;}else{echo"<thead><tr><th style='text-align: left;'><label><input type='checkbox' id='check-databases'".($a==""?" checked":"")." onclick='formCheck(this, /^databases\\[/);'>".'Database'."</label></thead>\n";$h=$b->databases();if($h){foreach($h
as$i){if(!information_schema($i)){$oe=ereg_replace("_.*","",$i);echo"<tr><td>".checkbox("databases[]",$i,$a==""||$a=="$oe%",$i,"formUncheck('check-databases');")."</label>\n";$pe[$oe]++;}}}else
echo"<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";}echo'</table>
</form>
';$hc=true;foreach($pe
as$w=>$X){if($w!=""&&$X>1){echo($hc?"<p>":" ")."<a href='".h(ME)."dump=".urlencode("$w%")."'>".h($w)."</a>";$hc=false;}}}elseif(isset($_GET["privileges"])){page_header('Privileges');$H=$f->query("SELECT User, Host FROM mysql.".(DB==""?"user":"db WHERE ".q(DB)." LIKE Db")." ORDER BY Host, User");$p=$H;if(!$H)$H=$f->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");echo"<form action=''><p>\n";hidden_fields_get();echo"<input type='hidden' name='db' value='".h(DB)."'>\n",($p?"":"<input type='hidden' name='grant' value=''>\n"),"<table cellspacing='0'>\n","<thead><tr><th>".'Username'."<th>".'Server'."<th>&nbsp;</thead>\n";while($J=$H->fetch_assoc())echo'<tr'.odd().'><td>'.h($J["User"])."<td>".h($J["Host"]).'<td><a href="'.h(ME.'user='.urlencode($J["User"]).'&host='.urlencode($J["Host"])).'">'.'Edit'."</a>\n";if(!$p||DB!="")echo"<tr".odd()."><td><input name='user'><td><input name='host' value='localhost'><td><input type='submit' value='".'Edit'."'>\n";echo"</table>\n","</form>\n",'<p><a href="'.h(ME).'user=">'.'Create user'."</a>";}elseif(isset($_GET["sql"])){if(!$j&&$_POST["export"]){dump_headers("sql");$b->dumpTable("","");$b->dumpData("","table",$_POST["query"]);exit;}restart_session();$yc=&get_session("queries");$xc=&$yc[DB];if(!$j&&$_POST["clear"]){$xc=array();redirect(remove_from_uri("history"));}page_header('SQL command',$j);if(!$j&&$_POST){$oc=false;$G=$_POST["query"];if($_POST["webfile"]){$oc=@fopen((file_exists("adminer.sql")?"adminer.sql":(file_exists("adminer.sql.gz")?"compress.zlib://adminer.sql.gz":"compress.bzip2://adminer.sql.bz2")),"rb");$G=($oc?fread($oc,1e6):false);}elseif($_FILES&&$_FILES["sql_file"]["error"]!=UPLOAD_ERR_NO_FILE)$G=get_file("sql_file",true);if(is_string($G)){if(function_exists('memory_get_usage'))@ini_set("memory_limit",max(ini_bytes("memory_limit"),2*strlen($G)+memory_get_usage()+8e6));if($G!=""&&strlen($G)<1e6){$F=$G.(ereg(";[ \t\r\n]*\$",$G)?"":";");if(!$xc||reset(end($xc))!=$F){restart_session();$xc[]=array($F,time());set_session("queries",$yc);stop_session();}}$cf="(?:\\s|/\\*.*\\*/|(?:#|-- )[^\n]*\n|--\n)";$rb=";";$Bd=0;$Kb=true;$g=connect();if(is_object($g)&&DB!="")$g->select_db(DB);$Va=0;$Qb=array();$dd=0;$be='[\'"'.($v=="sql"?'`#':($v=="sqlite"?'`[':($v=="mssql"?'[':''))).']|/\\*|-- |$'.($v=="pgsql"?'|\\$[^$]*\\$':'');$Hf=microtime();parse_str($_COOKIE["adminer_export"],$ka);$Cb=$b->dumpFormat();unset($Cb["sql"]);while($G!=""){if(!$Bd&&preg_match("~^$cf*DELIMITER\\s+(\\S+)~i",$G,$_)){$rb=$_[1];$G=substr($G,strlen($_[0]));}else{preg_match('('.preg_quote($rb)."\\s*|$be)",$G,$_,PREG_OFFSET_CAPTURE,$Bd);list($mc,$ke)=$_[0];if(!$mc&&$oc&&!feof($oc))$G.=fread($oc,1e5);else{if(!$mc&&rtrim($G)=="")break;$Bd=$ke+strlen($mc);if($mc&&rtrim($mc)!=$rb){while(preg_match('('.($mc=='/*'?'\\*/':($mc=='['?']':(ereg('^-- |^#',$mc)?"\n":preg_quote($mc)."|\\\\."))).'|$)s',$G,$_,PREG_OFFSET_CAPTURE,$Bd)){$L=$_[0][0];if(!$L&&$oc&&!feof($oc))$G.=fread($oc,1e5);else{$Bd=$_[0][1]+strlen($L);if($L[0]!="\\")break;}}}else{$Kb=false;$F=substr($G,0,$ke);$Va++;$re="<pre id='sql-$Va'><code class='jush-$v'>".shorten_utf8(trim($F),1000)."</code></pre>\n";if(!$_POST["only_errors"]){echo$re;ob_flush();flush();}$ef=microtime();if($f->multi_query($F)&&is_object($g)&&preg_match("~^$cf*USE\\b~isU",$F))$g->query($F);do{$H=$f->store_result();$Lb=microtime();$Af=format_time($ef,$Lb).(strlen($F)<1000?" <a href='".h(ME)."sql=".urlencode(trim($F))."'>".'Edit'."</a>":"");if($f->error){echo($_POST["only_errors"]?$re:""),"<p class='error'>".'Error in query'.": ".error()."\n";$Qb[]=" <a href='#sql-$Va'>$Va</a>";if($_POST["error_stops"])break
2;}elseif(is_object($H)){$Rd=select($H,$g);if(!$_POST["only_errors"]){echo"<form action='' method='post'>\n","<p>".($H->num_rows?lang(array('%d row','%d rows'),$H->num_rows):"").$Af;$r="export-$Va";$Zb=", <a href='#$r' onclick=\"return !toggle('$r');\">".'Export'."</a><span id='$r' class='hidden'>: ".html_select("output",$b->dumpOutput(),$ka["output"])." ".html_select("format",$Cb,$ka["format"])."<input type='hidden' name='query' value='".h($F)."'>"." <input type='submit' name='export' value='".'Export'."'><input type='hidden' name='token' value='$T'></span>\n";if($g&&preg_match("~^($cf|\\()*SELECT\\b~isU",$F)&&($Yb=explain($g,$F))){$r="explain-$Va";echo", <a href='#$r' onclick=\"return !toggle('$r');\">EXPLAIN</a>$Zb","<div id='$r' class='hidden'>\n";select($Yb,$g,($v=="sql"?"http://dev.mysql.com/doc/refman/".substr($f->server_info,0,3)."/en/explain-output.html#explain_":""),$Rd);echo"</div>\n";}else
echo$Zb;echo"</form>\n";}}else{if(preg_match("~^$cf*(CREATE|DROP|ALTER)$cf+(DATABASE|SCHEMA)\\b~isU",$F)){restart_session();set_session("dbs",null);stop_session();}if(!$_POST["only_errors"])echo"<p class='message' title='".h($f->info)."'>".lang(array('Query executed OK, %d row affected.','Query executed OK, %d rows affected.'),$f->affected_rows)."$Af\n";}$ef=$Lb;}while($f->next_result());$dd+=substr_count($F.$mc,"\n");$G=substr($G,$Bd);$Bd=0;}}}}if($Kb)echo"<p class='message'>".'No commands to execute.'."\n";elseif($_POST["only_errors"])echo"<p class='message'>".lang(array('%d query executed OK.','%d queries executed OK.'),$Va-count($Qb)).format_time($Hf,microtime())."\n";elseif($Qb&&$Va>1)echo"<p class='error'>".'Error in query'.": ".implode("",$Qb)."\n";}else
echo"<p class='error'>".upload_error($G)."\n";}echo'
<form action="" method="post" enctype="multipart/form-data" id="form">
<p>';$F=$_GET["sql"];if($_POST)$F=$_POST["query"];elseif($_GET["history"]=="all")$F=$xc;elseif($_GET["history"]!="")$F=$xc[$_GET["history"]][0];textarea("query",$F,20);echo($_POST?"":"<script type='text/javascript'>document.getElementsByTagName('textarea')[0].focus();</script>\n"),"<p>".(ini_bool("file_uploads")?'File upload'.': <input type="file" name="sql_file"'.($_FILES&&$_FILES["sql_file"]["error"]!=4?'':' onchange="this.form[\'only_errors\'].checked = true;"').'> (&lt; '.ini_get("upload_max_filesize").'B)':'File uploads are disabled.'),'<p>
<input type="submit" value="Execute" title="Ctrl+Enter">
<input type="hidden" name="token" value="',$T,'">
',checkbox("error_stops",1,$_POST["error_stops"],'Stop on error')."\n",checkbox("only_errors",1,$_POST["only_errors"],'Show only errors')."\n";print_fieldset("webfile",'From server',$_POST["webfile"],"document.getElementById('form')['only_errors'].checked = true; ");$Ya=array();foreach(array("gz"=>"zlib","bz2"=>"bz2")as$w=>$X){if(extension_loaded($X))$Ya[]=".$w";}echo
sprintf('Webserver file %s',"<code>adminer.sql".($Ya?"[".implode("|",$Ya)."]":"")."</code>"),' <input type="submit" name="webfile" value="'.'Run file'.'">',"</div></fieldset>\n";if($xc){print_fieldset("history",'History',$_GET["history"]!="");foreach($xc
as$w=>$X){list($F,$Af)=$X;echo'<a href="'.h(ME."sql=&history=$w").'">'.'Edit'."</a> <span class='time'>".@date("H:i:s",$Af)."</span> <code class='jush-$v'>".shorten_utf8(ltrim(str_replace("\n"," ",str_replace("\r","",preg_replace('~^(#|-- ).*~m','',$F)))),80,"</code>")."<br>\n";}echo"<input type='submit' name='clear' value='".'Clear'."'>\n","<a href='".h(ME."sql=&history=all")."'>".'Edit all'."</a>\n","</div></fieldset>\n";}echo'
</form>
';}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$Z=(isset($_GET["select"])?(count($_POST["check"])==1?where_check($_POST["check"][0]):""):where($_GET));$Yf=(isset($_GET["select"])?$_POST["edit"]:$Z);$l=fields($a);foreach($l
as$C=>$k){if(!isset($k["privileges"][$Yf?"update":"insert"])||$b->fieldName($k)=="")unset($l[$C]);}if($_POST&&!$j&&!isset($_GET["select"])){$z=$_POST["referer"];if($_POST["insert"])$z=($Yf?null:$_SERVER["REQUEST_URI"]);elseif(!ereg('^.+&select=.+$',$z))$z=ME."select=".urlencode($a);if(isset($_POST["delete"]))query_redirect("DELETE".limit1("FROM ".table($a)," WHERE $Z"),$z,'Item has been deleted.');else{$O=array();foreach($l
as$C=>$k){$X=process_input($k);if($X!==false&&$X!==null)$O[idf_escape($C)]=($Yf?"\n".idf_escape($C)." = $X":$X);}if($Yf){if(!$O)redirect($z);query_redirect("UPDATE".limit1(table($a)." SET".implode(",",$O),"\nWHERE $Z"),$z,'Item has been updated.');}else{$H=insert_into($a,$O);$Wc=($H?last_id():0);queries_redirect($z,sprintf('Item%s has been inserted.',($Wc?" $Wc":"")),$H);}}}$qf=$b->tableName(table_status($a));page_header(($Yf?'Edit':'Insert'),$j,array("select"=>array($a,$qf)),$qf);$J=null;if($_POST["save"])$J=(array)$_POST["fields"];elseif($Z){$M=array();foreach($l
as$C=>$k){if(isset($k["privileges"]["select"])){$ta=convert_field($k);if($_POST["clone"]&&$k["auto_increment"])$ta="''";if($v=="sql"&&ereg("enum|set",$k["type"]))$ta="1*".idf_escape($C);$M[]=($ta?"$ta AS ":"").idf_escape($C);}}$J=array();if($M){$K=get_rows("SELECT".limit(implode(", ",$M)." FROM ".table($a)," WHERE $Z",(isset($_GET["select"])?2:1)));$J=(isset($_GET["select"])&&count($K)!=1?null:reset($K));}}if($J===false)echo"<p class='error'>".'No rows.'."\n";echo'
<form action="" method="post" enctype="multipart/form-data" id="form">
';if($l){echo"<table cellspacing='0' onkeydown='return editingKeydown(event);'>\n";foreach($l
as$C=>$k){echo"<tr><th>".$b->fieldName($k);$qb=$_GET["set"][bracket_escape($C)];$Y=($J!==null?($J[$C]!=""&&$v=="sql"&&ereg("enum|set",$k["type"])?(is_array($J[$C])?array_sum($J[$C]):+$J[$C]):$J[$C]):(!$Yf&&$k["auto_increment"]?"":(isset($_GET["select"])?false:($qb!==null?$qb:$k["default"]))));if(!$_POST["save"]&&is_string($Y))$Y=$b->editVal($Y,$k);$o=($_POST["save"]?(string)$_POST["function"][$C]:($Yf&&$k["on_update"]=="CURRENT_TIMESTAMP"?"now":($Y===false?null:($Y!==null?'':'NULL'))));if($k["type"]=="timestamp"&&$Y=="CURRENT_TIMESTAMP"){$Y="";$o="now";}input($k,$Y,$o);echo"\n";}echo"</table>\n";}echo'<p>
';if($l){echo"<input type='submit' value='".'Save'."'>\n";if(!isset($_GET["select"]))echo"<input type='submit' name='insert' value='".($Yf?'Save and continue edit':'Save and insert next')."' title='Ctrl+Shift+Enter'>\n";}echo($Yf?"<input type='submit' name='delete' value='".'Delete'."' onclick=\"return confirm('".'Are you sure?'."');\">\n":($_POST||!$l?"":"<script type='text/javascript'>document.getElementById('form').getElementsByTagName('td')[1].firstChild.focus();</script>\n"));if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo'<input type="hidden" name="referer" value="',h(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"]),'">
<input type="hidden" name="save" value="1">
<input type="hidden" name="token" value="',$T,'">
</form>
';}elseif(isset($_GET["create"])){$a=$_GET["create"];$ce=array('HASH','LINEAR HASH','KEY','LINEAR KEY','RANGE','LIST');$Ce=referencable_primary($a);$n=array();foreach($Ce
as$qf=>$k)$n[str_replace("`","``",$qf)."`".str_replace("`","``",$k["field"])]=$qf;$Ud=array();$Vd=array();if($a!=""){$Ud=fields($a);$Vd=table_status($a);}if($_POST&&!$_POST["fields"])$_POST["fields"]=array();if($_POST&&!$j&&!$_POST["add"]&&!$_POST["drop_col"]&&!$_POST["up"]&&!$_POST["down"]){if($_POST["drop"])query_redirect("DROP TABLE ".table($a),substr(ME,0,-1),'Table has been dropped.');else{$l=array();$qa=array();$ag=false;$jc=array();ksort($_POST["fields"]);$Td=reset($Ud);$oa=" FIRST";foreach($_POST["fields"]as$w=>$k){$m=$n[$k["type"]];$Of=($m!==null?$Ce[$m]:$k);if($k["field"]!=""){if(!$k["has_default"])$k["default"]=null;$qb=eregi_replace(" *on update CURRENT_TIMESTAMP","",$k["default"]);if($qb!=$k["default"]){$k["on_update"]="CURRENT_TIMESTAMP";$k["default"]=$qb;}if($w==$_POST["auto_increment_col"])$k["auto_increment"]=true;$we=process_field($k,$Of);$qa[]=array($k["orig"],$we,$oa);if($we!=process_field($Td,$Td)){$l[]=array($k["orig"],$we,$oa);if($k["orig"]!=""||$oa)$ag=true;}if($m!==null)$jc[idf_escape($k["field"])]=($a!=""&&$v!="sqlite"?"ADD":" ")." FOREIGN KEY (".idf_escape($k["field"]).") REFERENCES ".table($n[$k["type"]])." (".idf_escape($Of["field"]).")".(ereg("^($Hd)\$",$k["on_delete"])?" ON DELETE $k[on_delete]":"");$oa=" AFTER ".idf_escape($k["field"]);}elseif($k["orig"]!=""){$ag=true;$l[]=array($k["orig"]);}if($k["orig"]!=""){$Td=next($Ud);if(!$Td)$oa="";}}$ee="";if(in_array($_POST["partition_by"],$ce)){$fe=array();if($_POST["partition_by"]=='RANGE'||$_POST["partition_by"]=='LIST'){foreach(array_filter($_POST["partition_names"])as$w=>$X){$Y=$_POST["partition_values"][$w];$fe[]="\nPARTITION ".idf_escape($X)." VALUES ".($_POST["partition_by"]=='RANGE'?"LESS THAN":"IN").($Y!=""?" ($Y)":" MAXVALUE");}}$ee.="\nPARTITION BY $_POST[partition_by]($_POST[partition])".($fe?" (".implode(",",$fe)."\n)":($_POST["partitions"]?" PARTITIONS ".(+$_POST["partitions"]):""));}elseif(support("partitioning")&&ereg("partitioned",$Vd["Create_options"]))$ee.="\nREMOVE PARTITIONING";$A='Table has been altered.';if($a==""){cookie("adminer_engine",$_POST["Engine"]);$A='Table has been created.';}$C=trim($_POST["name"]);queries_redirect(ME."table=".urlencode($C),$A,alter_table($a,$C,($v=="sqlite"&&($ag||$jc)?$qa:$l),$jc,$_POST["Comment"],($_POST["Engine"]&&$_POST["Engine"]!=$Vd["Engine"]?$_POST["Engine"]:""),($_POST["Collation"]&&$_POST["Collation"]!=$Vd["Collation"]?$_POST["Collation"]:""),($_POST["Auto_increment"]!=""?+$_POST["Auto_increment"]:""),$ee));}}page_header(($a!=""?'Alter table':'Create table'),$j,array("table"=>$a),$a);$J=array("Engine"=>$_COOKIE["adminer_engine"],"fields"=>array(array("field"=>"","type"=>(isset($Qf["int"])?"int":(isset($Qf["integer"])?"integer":"")))),"partition_names"=>array(""),);if($_POST){$J=$_POST;if($J["auto_increment_col"])$J["fields"][$J["auto_increment_col"]]["auto_increment"]=true;process_fields($J["fields"]);}elseif($a!=""){$J=$Vd;$J["name"]=$a;$J["fields"]=array();if(!$_GET["auto_increment"])$J["Auto_increment"]="";foreach($Ud
as$k){$k["has_default"]=isset($k["default"]);if($k["on_update"])$k["default"].=" ON UPDATE $k[on_update]";$J["fields"][]=$k;}if(support("partitioning")){$pc="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($a);$H=$f->query("SELECT PARTITION_METHOD, PARTITION_ORDINAL_POSITION, PARTITION_EXPRESSION $pc ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");list($J["partition_by"],$J["partitions"],$J["partition"])=$H->fetch_row();$J["partition_names"]=array();$J["partition_values"]=array();foreach(get_rows("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $pc AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION")as$Re){$J["partition_names"][]=$Re["PARTITION_NAME"];$J["partition_values"][]=$Re["PARTITION_DESCRIPTION"];}$J["partition_names"][]="";}}$d=collations();$lf=floor(extension_loaded("suhosin")?(min(ini_get("suhosin.request.max_vars"),ini_get("suhosin.post.max_vars"))-13)/10:0);if($lf&&count($J["fields"])>$lf)echo"<p class='error'>".h(sprintf('Maximum number of allowed fields exceeded. Please increase %s and %s.','suhosin.post.max_vars','suhosin.request.max_vars'))."\n";$Nb=engines();foreach($Nb
as$Mb){if(!strcasecmp($Mb,$J["Engine"])){$J["Engine"]=$Mb;break;}}echo'
<form action="" method="post" id="form">
<p>
Table name: <input name="name" maxlength="64" value="',h($J["name"]),'">
';if($a==""&&!$_POST){?><script type='text/javascript'>document.getElementById('form')['name'].focus();</script><?php }echo($Nb?html_select("Engine",array(""=>"(".'engine'.")")+$Nb,$J["Engine"]):""),' ',($d&&!ereg("sqlite|mssql",$v)?html_select("Collation",array(""=>"(".'collation'.")")+$d,$J["Collation"]):""),' <input type="submit" value="Save">
<table cellspacing="0" id="edit-fields" class="nowrap">
';$Xa=($_POST?$_POST["comments"]:$J["Comment"]!="");if(!$_POST&&!$Xa){foreach($J["fields"]as$k){if($k["comment"]!=""){$Xa=true;break;}}}edit_fields($J["fields"],$d,"TABLE",$lf,$n,$Xa);echo'</table>
<p>
Auto Increment: <input name="Auto_increment" size="6" value="',h($J["Auto_increment"]),'">
<label class="jsonly"><input type="checkbox" name="defaults" value="1"',($_POST["defaults"]?" checked":""),' onclick="columnShow(this.checked, 5);">Default values</label>
',(support("comment")?checkbox("comments",1,$Xa,'Comment',"columnShow(this.checked, 6); toggle('Comment'); if (this.checked) this.form['Comment'].focus();",true).' <input id="Comment" name="Comment" value="'.h($J["Comment"]).'" maxlength="60"'.($Xa?'':' class="hidden"').'>':''),'<p>
<input type="submit" value="Save">
';if($_GET["create"]!=""){echo'<input type="submit" name="drop" value="Drop"',confirm(),'>';}echo'<input type="hidden" name="token" value="',$T,'">
';if(support("partitioning")){$de=ereg('RANGE|LIST',$J["partition_by"]);print_fieldset("partition",'Partition by',$J["partition_by"]);echo'<p>
',html_select("partition_by",array(-1=>"")+$ce,$J["partition_by"],"partitionByChange(this);"),'(<input name="partition" value="',h($J["partition"]),'">)
Partitions: <input name="partitions" size="2" value="',h($J["partitions"]),'"',($de||!$J["partition_by"]?" class='hidden'":""),'>
<table cellspacing="0" id="partition-table"',($de?"":" class='hidden'"),'>
<thead><tr><th>Partition name<th>Values</thead>
';foreach($J["partition_names"]as$w=>$X){echo'<tr>','<td><input name="partition_names[]" value="'.h($X).'"'.($w==count($J["partition_names"])-1?' onchange="partitionNameChange(this);"':'').'>','<td><input name="partition_values[]" value="'.h($J["partition_values"][$w]).'">';}echo'</table>
</div></fieldset>
';}echo'</form>
';}elseif(isset($_GET["indexes"])){$a=$_GET["indexes"];$Ec=array("PRIMARY","UNIQUE","INDEX");$S=table_status($a);if(eregi("MyISAM|M?aria",$S["Engine"]))$Ec[]="FULLTEXT";$t=indexes($a);if($v=="sqlite"){unset($Ec[0]);unset($t[""]);}if($_POST&&!$j&&!$_POST["add"]){$sa=array();foreach($_POST["indexes"]as$s){$C=$s["name"];if(in_array($s["type"],$Ec)){$e=array();$cd=array();$O=array();ksort($s["columns"]);foreach($s["columns"]as$w=>$Ta){if($Ta!=""){$bd=$s["lengths"][$w];$O[]=idf_escape($Ta).($bd?"(".(+$bd).")":"");$e[]=$Ta;$cd[]=($bd?$bd:null);}}if($e){$Xb=$t[$C];if($Xb){ksort($Xb["columns"]);ksort($Xb["lengths"]);if($s["type"]==$Xb["type"]&&array_values($Xb["columns"])===$e&&(!$Xb["lengths"]||array_values($Xb["lengths"])===$cd)){unset($t[$C]);continue;}}$sa[]=array($s["type"],$C,"(".implode(", ",$O).")");}}}foreach($t
as$C=>$Xb)$sa[]=array($Xb["type"],$C,"DROP");if(!$sa)redirect(ME."table=".urlencode($a));queries_redirect(ME."table=".urlencode($a),'Indexes have been altered.',alter_indexes($a,$sa));}page_header('Indexes',$j,array("table"=>$a),$a);$l=array_keys(fields($a));$J=array("indexes"=>$t);if($_POST){$J=$_POST;if($_POST["add"]){foreach($J["indexes"]as$w=>$s){if($s["columns"][count($s["columns"])]!="")$J["indexes"][$w]["columns"][]="";}$s=end($J["indexes"]);if($s["type"]||array_filter($s["columns"],'strlen')||array_filter($s["lengths"],'strlen'))$J["indexes"][]=array("columns"=>array(1=>""));}}else{foreach($J["indexes"]as$w=>$s){$J["indexes"][$w]["name"]=$w;$J["indexes"][$w]["columns"][]="";}$J["indexes"][]=array("columns"=>array(1=>""));}echo'
<form action="" method="post">
<table cellspacing="0" class="nowrap">
<thead><tr><th>Index Type<th>Column (length)<th>Name</thead>
';$u=1;foreach($J["indexes"]as$s){echo"<tr><td>".html_select("indexes[$u][type]",array(-1=>"")+$Ec,$s["type"],($u==count($J["indexes"])?"indexesAddRow(this);":1))."<td>";ksort($s["columns"]);$q=1;foreach($s["columns"]as$w=>$Ta){echo"<span>".html_select("indexes[$u][columns][$q]",array(-1=>"")+$l,$Ta,($q==count($s["columns"])?"indexesAddColumn":"indexesChangeColumn")."(this, '".js_escape($v=="sql"?"":$_GET["indexes"]."_")."');"),"<input name='indexes[$u][lengths][$q]' size='2' value='".h($s["lengths"][$w])."'> </span>";$q++;}echo"<td><input name='indexes[$u][name]' value='".h($s["name"])."'>\n";$u++;}echo'</table>
<p>
<input type="submit" value="Save">
<noscript><p><input type="submit" name="add" value="Add next"></noscript>
<input type="hidden" name="token" value="',$T,'">
</form>
';}elseif(isset($_GET["database"])){if($_POST&&!$j&&!isset($_POST["add_x"])){restart_session();$C=trim($_POST["name"]);if($_POST["drop"]){$_GET["db"]="";queries_redirect(remove_from_uri("db|database"),'Database has been dropped.',drop_databases(array(DB)));}elseif(DB!==$C){if(DB!=""){$_GET["db"]=$C;queries_redirect(preg_replace('~db=[^&]*&~','',ME)."db=".urlencode($C),'Database has been renamed.',rename_database($C,$_POST["collation"]));}else{$h=explode("\n",str_replace("\r","",$C));$jf=true;$Vc="";foreach($h
as$i){if(count($h)==1||$i!=""){if(!create_database($i,$_POST["collation"]))$jf=false;$Vc=$i;}}queries_redirect(ME."db=".urlencode($Vc),'Database has been created.',$jf);}}else{if(!$_POST["collation"])redirect(substr(ME,0,-1));query_redirect("ALTER DATABASE ".idf_escape($C).(eregi('^[a-z0-9_]+$',$_POST["collation"])?" COLLATE $_POST[collation]":""),substr(ME,0,-1),'Database has been altered.');}}page_header(DB!=""?'Alter database':'Create database',$j,array(),DB);$d=collations();$C=DB;$Qa=null;if($_POST){$C=$_POST["name"];$Qa=$_POST["collation"];}elseif(DB!="")$Qa=db_collation(DB,$d);elseif($v=="sql"){foreach(get_vals("SHOW GRANTS")as$p){if(preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\\.\\*)?~',$p,$_)&&$_[1]){$C=stripcslashes(idf_unescape("`$_[2]`"));break;}}}echo'
<form action="" method="post">
<p>
',($_POST["add_x"]||strpos($C,"\n")?'<textarea id="name" name="name" rows="10" cols="40">'.h($C).'</textarea><br>':'<input id="name" name="name" value="'.h($C).'" maxlength="64">')."\n".($d?html_select("collation",array(""=>"(".'collation'.")")+$d,$Qa):"");?>
<script type='text/javascript'>document.getElementById('name').focus();</script>
<input type="submit" value="Save">
<?php
if(DB!="")echo"<input type='submit' name='drop' value='".'Drop'."'".confirm().">\n";elseif(!$_POST["add_x"]&&$_GET["db"]=="")echo"<input type='image' name='add' src='".h(preg_replace("~\\?.*~","",ME))."?file=plus.gif&amp;version=3.6.1' alt='+' title='".'Add next'."'>\n";echo'<input type="hidden" name="token" value="',$T,'">
</form>
';}elseif(isset($_GET["call"])){$da=$_GET["call"];page_header('Call'.": ".h($da),$j);$Oe=routine($da,(isset($_GET["callf"])?"FUNCTION":"PROCEDURE"));$Dc=array();$Wd=array();foreach($Oe["fields"]as$q=>$k){if(substr($k["inout"],-3)=="OUT")$Wd[$q]="@".idf_escape($k["field"])." AS ".idf_escape($k["field"]);if(!$k["inout"]||substr($k["inout"],0,2)=="IN")$Dc[]=$q;}if(!$j&&$_POST){$Ha=array();foreach($Oe["fields"]as$w=>$k){if(in_array($w,$Dc)){$X=process_input($k);if($X===false)$X="''";if(isset($Wd[$w]))$f->query("SET @".idf_escape($k["field"])." = $X");}$Ha[]=(isset($Wd[$w])?"@".idf_escape($k["field"]):$X);}$G=(isset($_GET["callf"])?"SELECT":"CALL")." ".idf_escape($da)."(".implode(", ",$Ha).")";echo"<p><code class='jush-$v'>".h($G)."</code> <a href='".h(ME)."sql=".urlencode($G)."'>".'Edit'."</a>\n";if(!$f->multi_query($G))echo"<p class='error'>".error()."\n";else{$g=connect();if(is_object($g))$g->select_db(DB);do{$H=$f->store_result();if(is_object($H))select($H,$g);else
echo"<p class='message'>".lang(array('Routine has been called, %d row affected.','Routine has been called, %d rows affected.'),$f->affected_rows)."\n";}while($f->next_result());if($Wd)select($f->query("SELECT ".implode(", ",$Wd)));}}echo'
<form action="" method="post">
';if($Dc){echo"<table cellspacing='0'>\n";foreach($Dc
as$w){$k=$Oe["fields"][$w];$C=$k["field"];echo"<tr><th>".$b->fieldName($k);$Y=$_POST["fields"][$C];if($Y!=""){if($k["type"]=="enum")$Y=+$Y;if($k["type"]=="set")$Y=array_sum($Y);}input($k,$Y,(string)$_POST["function"][$C]);echo"\n";}echo"</table>\n";}echo'<p>
<input type="submit" value="Call">
<input type="hidden" name="token" value="',$T,'">
</form>
';}elseif(isset($_GET["foreign"])){$a=$_GET["foreign"];if($_POST&&!$j&&!$_POST["add"]&&!$_POST["change"]&&!$_POST["change-js"]){if($_POST["drop"])query_redirect("ALTER TABLE ".table($a)."\nDROP ".($v=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($_GET["name"]),ME."table=".urlencode($a),'Foreign key has been dropped.');else{$bf=array_filter($_POST["source"],'strlen');ksort($bf);$xf=array();foreach($bf
as$w=>$X)$xf[$w]=$_POST["target"][$w];query_redirect("ALTER TABLE ".table($a).($_GET["name"]!=""?"\nDROP ".($v=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($_GET["name"]).",":"")."\nADD FOREIGN KEY (".implode(", ",array_map('idf_escape',$bf)).") REFERENCES ".table($_POST["table"])." (".implode(", ",array_map('idf_escape',$xf)).")".(ereg("^($Hd)\$",$_POST["on_delete"])?" ON DELETE $_POST[on_delete]":"").(ereg("^($Hd)\$",$_POST["on_update"])?" ON UPDATE $_POST[on_update]":""),ME."table=".urlencode($a),($_GET["name"]!=""?'Foreign key has been altered.':'Foreign key has been created.'));$j='Source and target columns must have the same data type, there must be an index on the target columns and referenced data must exist.'."<br>$j";}}page_header('Foreign key',$j,array("table"=>$a),$a);$J=array("table"=>$a,"source"=>array(""));if($_POST){$J=$_POST;ksort($J["source"]);if($_POST["add"])$J["source"][]="";elseif($_POST["change"]||$_POST["change-js"])$J["target"]=array();}elseif($_GET["name"]!=""){$n=foreign_keys($a);$J=$n[$_GET["name"]];$J["source"][]="";}$bf=array_keys(fields($a));$xf=($a===$J["table"]?$bf:array_keys(fields($J["table"])));$Be=array();foreach(table_status()as$C=>$S){if(fk_support($S))$Be[]=$C;}echo'
<form action="" method="post">
<p>
';if($J["db"]==""&&$J["ns"]==""){echo'Target table:
',html_select("table",$Be,$J["table"],"this.form['change-js'].value = '1'; this.form.submit();"),'<input type="hidden" name="change-js" value="">
<noscript><p><input type="submit" name="change" value="Change"></noscript>
<table cellspacing="0">
<thead><tr><th>Source<th>Target</thead>
';$u=0;foreach($J["source"]as$w=>$X){echo"<tr>","<td>".html_select("source[".(+$w)."]",array(-1=>"")+$bf,$X,($u==count($J["source"])-1?"foreignAddRow(this);":1)),"<td>".html_select("target[".(+$w)."]",$xf,$J["target"][$w]);$u++;}echo'</table>
<p>
ON DELETE: ',html_select("on_delete",array(-1=>"")+explode("|",$Hd),$J["on_delete"]),' ON UPDATE: ',html_select("on_update",array(-1=>"")+explode("|",$Hd),$J["on_update"]),'<p>
<input type="submit" value="Save">
<noscript><p><input type="submit" name="add" value="Add column"></noscript>
';}if($_GET["name"]!=""){echo'<input type="submit" name="drop" value="Drop"',confirm(),'>';}echo'<input type="hidden" name="token" value="',$T,'">
</form>
';}elseif(isset($_GET["view"])){$a=$_GET["view"];$Ab=false;if($_POST&&!$j){$C=trim($_POST["name"]);$Ab=drop_create("DROP VIEW ".table($a),"CREATE VIEW ".table($C)." AS\n$_POST[select]",($_POST["drop"]?substr(ME,0,-1):ME."table=".urlencode($C)),'View has been dropped.','View has been altered.','View has been created.',$a);}page_header(($a!=""?'Alter view':'Create view'),$j,array("table"=>$a),$a);$J=$_POST;if(!$J&&$a!=""){$J=view($a);$J["name"]=$a;}echo'
<form action="" method="post">
<p>Name: <input name="name" value="',h($J["name"]),'" maxlength="64">
<p>';textarea("select",$J["select"]);echo'<p>
';if($Ab){echo'<input type="hidden" name="dropped" value="1">';}echo'<input type="submit" value="Save">
';if($_GET["view"]!=""){echo'<input type="submit" name="drop" value="Drop"',confirm(),'>';}echo'<input type="hidden" name="token" value="',$T,'">
</form>
';}elseif(isset($_GET["event"])){$aa=$_GET["event"];$Jc=array("YEAR","QUARTER","MONTH","DAY","HOUR","MINUTE","WEEK","SECOND","YEAR_MONTH","DAY_HOUR","DAY_MINUTE","DAY_SECOND","HOUR_MINUTE","HOUR_SECOND","MINUTE_SECOND");$gf=array("ENABLED"=>"ENABLE","DISABLED"=>"DISABLE","SLAVESIDE_DISABLED"=>"DISABLE ON SLAVE");if($_POST&&!$j){if($_POST["drop"])query_redirect("DROP EVENT ".idf_escape($aa),substr(ME,0,-1),'Event has been dropped.');elseif(in_array($_POST["INTERVAL_FIELD"],$Jc)&&isset($gf[$_POST["STATUS"]])){$Se="\nON SCHEDULE ".($_POST["INTERVAL_VALUE"]?"EVERY ".q($_POST["INTERVAL_VALUE"])." $_POST[INTERVAL_FIELD]".($_POST["STARTS"]?" STARTS ".q($_POST["STARTS"]):"").($_POST["ENDS"]?" ENDS ".q($_POST["ENDS"]):""):"AT ".q($_POST["STARTS"]))." ON COMPLETION".($_POST["ON_COMPLETION"]?"":" NOT")." PRESERVE";queries_redirect(substr(ME,0,-1),($aa!=""?'Event has been altered.':'Event has been created.'),queries(($aa!=""?"ALTER EVENT ".idf_escape($aa).$Se.($aa!=$_POST["EVENT_NAME"]?"\nRENAME TO ".idf_escape($_POST["EVENT_NAME"]):""):"CREATE EVENT ".idf_escape($_POST["EVENT_NAME"]).$Se)."\n".$gf[$_POST["STATUS"]]." COMMENT ".q($_POST["EVENT_COMMENT"]).rtrim(" DO\n$_POST[EVENT_DEFINITION]",";").";"));}}page_header(($aa!=""?'Alter event'.": ".h($aa):'Create event'),$j);$J=$_POST;if(!$J&&$aa!=""){$K=get_rows("SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = ".q(DB)." AND EVENT_NAME = ".q($aa));$J=reset($K);}echo'
<form action="" method="post">
<table cellspacing="0">
<tr><th>Name<td><input name="EVENT_NAME" value="',h($J["EVENT_NAME"]),'" maxlength="64">
<tr><th>Start<td><input name="STARTS" value="',h("$J[EXECUTE_AT]$J[STARTS]"),'">
<tr><th>End<td><input name="ENDS" value="',h($J["ENDS"]),'">
<tr><th>Every<td><input name="INTERVAL_VALUE" value="',h($J["INTERVAL_VALUE"]),'" size="6"> ',html_select("INTERVAL_FIELD",$Jc,$J["INTERVAL_FIELD"]),'<tr><th>Status<td>',html_select("STATUS",$gf,$J["STATUS"]),'<tr><th>Comment<td><input name="EVENT_COMMENT" value="',h($J["EVENT_COMMENT"]),'" maxlength="64">
<tr><th>&nbsp;<td>',checkbox("ON_COMPLETION","PRESERVE",$J["ON_COMPLETION"]=="PRESERVE",'On completion preserve'),'</table>
<p>';textarea("EVENT_DEFINITION",$J["EVENT_DEFINITION"]);echo'<p>
<input type="submit" value="Save">
';if($aa!=""){echo'<input type="submit" name="drop" value="Drop"',confirm(),'>';}echo'<input type="hidden" name="token" value="',$T,'">
</form>
';}elseif(isset($_GET["procedure"])){$da=$_GET["procedure"];$Oe=(isset($_GET["function"])?"FUNCTION":"PROCEDURE");$Pe=routine_languages();$Ab=false;if($_POST&&!$j&&!$_POST["add"]&&!$_POST["drop_col"]&&!$_POST["up"]&&!$_POST["down"]){$O=array();$l=(array)$_POST["fields"];ksort($l);foreach($l
as$k){if($k["field"]!="")$O[]=(ereg("^($Gc)\$",$k["inout"])?"$k[inout] ":"").idf_escape($k["field"]).process_type($k,"CHARACTER SET");}$Ab=drop_create("DROP $Oe ".idf_escape($da),"CREATE $Oe ".idf_escape(trim($_POST["name"]))." (".implode(", ",$O).")".(isset($_GET["function"])?" RETURNS".process_type($_POST["returns"],"CHARACTER SET"):"").(in_array($_POST["language"],$Pe)?" LANGUAGE $_POST[language]":"").rtrim("\n$_POST[definition]",";").";",substr(ME,0,-1),'Routine has been dropped.','Routine has been altered.','Routine has been created.',$da);}page_header(($da!=""?(isset($_GET["function"])?'Alter function':'Alter procedure').": ".h($da):(isset($_GET["function"])?'Create function':'Create procedure')),$j);$d=get_vals("SHOW CHARACTER SET");sort($d);$J=array("fields"=>array());if($_POST){$J=$_POST;$J["fields"]=(array)$J["fields"];process_fields($J["fields"]);}elseif($da!=""){$J=routine($da,$Oe);$J["name"]=$da;}echo'
<form action="" method="post" id="form">
<p>Name: <input name="name" value="',h($J["name"]),'" maxlength="64">
',($Pe?'Language'.": ".html_select("language",$Pe,$J["language"]):""),'<table cellspacing="0" class="nowrap">
';edit_fields($J["fields"],$d,$Oe);if(isset($_GET["function"])){echo"<tr><td>".'Return type';edit_type("returns",$J["returns"],$d);}echo'</table>
<p>';textarea("definition",$J["definition"]);echo'<p>
<input type="submit" value="Save">
';if($da!=""){echo'<input type="submit" name="drop" value="Drop"',confirm(),'>';}if($Ab){echo'<input type="hidden" name="dropped" value="1">';}echo'<input type="hidden" name="token" value="',$T,'">
</form>
';}elseif(isset($_GET["trigger"])){$a=$_GET["trigger"];$Mf=trigger_options();$Lf=array("INSERT","UPDATE","DELETE");$Ab=false;if($_POST&&!$j&&in_array($_POST["Timing"],$Mf["Timing"])&&in_array($_POST["Event"],$Lf)&&in_array($_POST["Type"],$Mf["Type"])){$Bf=" $_POST[Timing] $_POST[Event]";$Gd=" ON ".table($a);$Ab=drop_create("DROP TRIGGER ".idf_escape($_GET["name"]).($v=="pgsql"?$Gd:""),"CREATE TRIGGER ".idf_escape($_POST["Trigger"]).($v=="mssql"?$Gd.$Bf:$Bf.$Gd).rtrim(" $_POST[Type]\n$_POST[Statement]",";").";",ME."table=".urlencode($a),'Trigger has been dropped.','Trigger has been altered.','Trigger has been created.',$_GET["name"]);}page_header(($_GET["name"]!=""?'Alter trigger'.": ".h($_GET["name"]):'Create trigger'),$j,array("table"=>$a));$J=$_POST;if(!$J)$J=trigger($_GET["name"])+array("Trigger"=>$a."_bi");echo'
<form action="" method="post" id="form">
<table cellspacing="0">
<tr><th>Time<td>',html_select("Timing",$Mf["Timing"],$J["Timing"],"if (/^".preg_quote($a,"/")."_[ba][iud]$/.test(this.form['Trigger'].value)) this.form['Trigger'].value = '".js_escape($a)."_' + selectValue(this).charAt(0).toLowerCase() + selectValue(this.form['Event']).charAt(0).toLowerCase();"),'<tr><th>Event<td>',html_select("Event",$Lf,$J["Event"],"this.form['Timing'].onchange();"),'<tr><th>Type<td>',html_select("Type",$Mf["Type"],$J["Type"]),'</table>
<p>Name: <input name="Trigger" value="',h($J["Trigger"]),'" maxlength="64">
<p>';textarea("Statement",$J["Statement"]);echo'<p>
<input type="submit" value="Save">
';if($_GET["name"]!=""){echo'<input type="submit" name="drop" value="Drop"',confirm(),'>';}if($Ab){echo'<input type="hidden" name="dropped" value="1">';}echo'<input type="hidden" name="token" value="',$T,'">
</form>
';}elseif(isset($_GET["user"])){$fa=$_GET["user"];$ue=array(""=>array("All privileges"=>""));foreach(get_rows("SHOW PRIVILEGES")as$J){foreach(explode(",",($J["Privilege"]=="Grant option"?"":$J["Context"]))as$cb)$ue[$cb][$J["Privilege"]]=$J["Comment"];}$ue["Server Admin"]+=$ue["File access on server"];$ue["Databases"]["Create routine"]=$ue["Procedures"]["Create routine"];unset($ue["Procedures"]["Create routine"]);$ue["Columns"]=array();foreach(array("Select","Insert","Update","References")as$X)$ue["Columns"][$X]=$ue["Tables"][$X];unset($ue["Server Admin"]["Usage"]);foreach($ue["Tables"]as$w=>$X)unset($ue["Databases"][$w]);$xd=array();if($_POST){foreach($_POST["objects"]as$w=>$X)$xd[$X]=(array)$xd[$X]+(array)$_POST["grants"][$w];}$sc=array();$Ed="";if(isset($_GET["host"])&&($H=$f->query("SHOW GRANTS FOR ".q($fa)."@".q($_GET["host"])))){while($J=$H->fetch_row()){if(preg_match('~GRANT (.*) ON (.*) TO ~',$J[0],$_)&&preg_match_all('~ *([^(,]*[^ ,(])( *\\([^)]+\\))?~',$_[1],$hd,PREG_SET_ORDER)){foreach($hd
as$X){if($X[1]!="USAGE")$sc["$_[2]$X[2]"][$X[1]]=true;if(ereg(' WITH GRANT OPTION',$J[0]))$sc["$_[2]$X[2]"]["GRANT OPTION"]=true;}}if(preg_match("~ IDENTIFIED BY PASSWORD '([^']+)~",$J[0],$_))$Ed=$_[1];}}if($_POST&&!$j){$Fd=(isset($_GET["host"])?q($fa)."@".q($_GET["host"]):"''");$yd=q($_POST["user"])."@".q($_POST["host"]);$ge=q($_POST["pass"]);if($_POST["drop"])query_redirect("DROP USER $Fd",ME."privileges=",'User has been dropped.');else{$hb=false;if($Fd!=$yd){$hb=queries(($f->server_info<5?"GRANT USAGE ON *.* TO":"CREATE USER")." $yd IDENTIFIED BY".($_POST["hashed"]?" PASSWORD":"")." $ge");$j=!$hb;}elseif($_POST["pass"]!=$Ed||!$_POST["hashed"])queries("SET PASSWORD FOR $yd = ".($_POST["hashed"]?$ge:"PASSWORD($ge)"));if(!$j){$Le=array();foreach($xd
as$Ad=>$p){if(isset($_GET["grant"]))$p=array_filter($p);$p=array_keys($p);if(isset($_GET["grant"]))$Le=array_diff(array_keys(array_filter($xd[$Ad],'strlen')),$p);elseif($Fd==$yd){$Dd=array_keys((array)$sc[$Ad]);$Le=array_diff($Dd,$p);$p=array_diff($p,$Dd);unset($sc[$Ad]);}if(preg_match('~^(.+)\\s*(\\(.*\\))?$~U',$Ad,$_)&&(!grant("REVOKE",$Le,$_[2]," ON $_[1] FROM $yd")||!grant("GRANT",$p,$_[2]," ON $_[1] TO $yd"))){$j=true;break;}}}if(!$j&&isset($_GET["host"])){if($Fd!=$yd)queries("DROP USER $Fd");elseif(!isset($_GET["grant"])){foreach($sc
as$Ad=>$Le){if(preg_match('~^(.+)(\\(.*\\))?$~U',$Ad,$_))grant("REVOKE",array_keys($Le),$_[2]," ON $_[1] FROM $yd");}}}queries_redirect(ME."privileges=",(isset($_GET["host"])?'User has been altered.':'User has been created.'),!$j);if($hb)$f->query("DROP USER $yd");}}page_header((isset($_GET["host"])?'Username'.": ".h("$fa@$_GET[host]"):'Create user'),$j,array("privileges"=>array('','Privileges')));if($_POST){$J=$_POST;$sc=$xd;}else{$J=$_GET+array("host"=>$f->result("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)"));$J["pass"]=$Ed;if($Ed!="")$J["hashed"]=true;$sc[(DB!=""&&!isset($_GET["host"])?idf_escape(addcslashes(DB,"%_")):"").".*"]=array();}echo'<form action="" method="post">
<table cellspacing="0">
<tr><th>Server<td><input name="host" maxlength="60" value="',h($J["host"]),'">
<tr><th>Username<td><input name="user" maxlength="16" value="',h($J["user"]),'">
<tr><th>Password<td><input id="pass" name="pass" value="',h($J["pass"]),'">
';if(!$J["hashed"]){echo'<script type="text/javascript">typePassword(document.getElementById(\'pass\'));</script>';}echo
checkbox("hashed",1,$J["hashed"],'Hashed',"typePassword(this.form['pass'], this.checked);"),'</table>

';echo"<table cellspacing='0'>\n","<thead><tr><th colspan='2'><a href='http://dev.mysql.com/doc/refman/".substr($f->server_info,0,3)."/en/grant.html#priv_level' target='_blank' rel='noreferrer'>".'Privileges'."</a>";$q=0;foreach($sc
as$Ad=>$p){echo'<th>'.($Ad!="*.*"?"<input name='objects[$q]' value='".h($Ad)."' size='10'>":"<input type='hidden' name='objects[$q]' value='*.*' size='10'>*.*");$q++;}echo"</thead>\n";foreach(array(""=>"","Server Admin"=>'Server',"Databases"=>'Database',"Tables"=>'Table',"Columns"=>'Column',"Procedures"=>'Routine',)as$cb=>$sb){foreach((array)$ue[$cb]as$te=>$Wa){echo"<tr".odd()."><td".($sb?">$sb<td":" colspan='2'").' lang="en" title="'.h($Wa).'">'.h($te);$q=0;foreach($sc
as$Ad=>$p){$C="'grants[$q][".h(strtoupper($te))."]'";$Y=$p[strtoupper($te)];if($cb=="Server Admin"&&$Ad!=(isset($sc["*.*"])?"*.*":".*"))echo"<td>&nbsp;";elseif(isset($_GET["grant"]))echo"<td><select name=$C><option><option value='1'".($Y?" selected":"").">".'Grant'."<option value='0'".($Y=="0"?" selected":"").">".'Revoke'."</select>";else
echo"<td align='center'><input type='checkbox' name=$C value='1'".($Y?" checked":"").($te=="All privileges"?" id='grants-$q-all'":($te=="Grant option"?"":" onclick=\"if (this.checked) formUncheck('grants-$q-all');\"")).">";$q++;}}}echo"</table>\n",'<p>
<input type="submit" value="Save">
';if(isset($_GET["host"])){echo'<input type="submit" name="drop" value="Drop"',confirm(),'>';}echo'<input type="hidden" name="token" value="',$T,'">
</form>
';}elseif(isset($_GET["processlist"])){if(support("kill")&&$_POST&&!$j){$Sc=0;foreach((array)$_POST["kill"]as$X){if(queries("KILL ".(+$X)))$Sc++;}queries_redirect(ME."processlist=",lang(array('%d process has been killed.','%d processes have been killed.'),$Sc),$Sc||!$_POST["kill"]);}page_header('Process list',$j);echo'
<form action="" method="post">
<table cellspacing="0" onclick="tableClick(event);" class="nowrap checkable">
';$q=-1;foreach(process_list()as$q=>$J){if(!$q)echo"<thead><tr lang='en'>".(support("kill")?"<th>&nbsp;":"")."<th>".implode("<th>",array_keys($J))."</thead>\n";echo"<tr".odd().">".(support("kill")?"<td>".checkbox("kill[]",$J["Id"],0):"");foreach($J
as$w=>$X)echo"<td>".(($v=="sql"&&$w=="Info"&&ereg("Query|Killed",$J["Command"])&&$X!="")||($v=="pgsql"&&$w=="current_query"&&$X!="<IDLE>")||($v=="oracle"&&$w=="sql_text"&&$X!="")?"<code class='jush-$v'>".shorten_utf8($X,100,"</code>").' <a href="'.h(ME.($J["db"]!=""?"db=".urlencode($J["db"])."&":"")."sql=".urlencode($X)).'">'.'Edit'.'</a>':nbsp($X));echo"\n";}echo'</table>
<script type=\'text/javascript\'>tableCheck();</script>
<p>
';if(support("kill")){echo($q+1)."/".sprintf('%d in total',$f->result("SELECT @@max_connections")),"<p><input type='submit' value='".'Kill'."'>\n";}echo'<input type="hidden" name="token" value="',$T,'">
</form>
';}elseif(isset($_GET["select"])){$a=$_GET["select"];$S=table_status($a);$t=indexes($a);$l=fields($a);$n=column_foreign_keys($a);$Cd="";if($S["Oid"]=="t"){$Cd=($v=="sqlite"?"rowid":"oid");$t[]=array("type"=>"PRIMARY","columns"=>array($Cd));}parse_str($_COOKIE["adminer_import"],$la);$Me=array();$e=array();$_f=null;foreach($l
as$w=>$k){$C=$b->fieldName($k);if(isset($k["privileges"]["select"])&&$C!=""){$e[$w]=html_entity_decode(strip_tags($C));if(ereg('text|lob|geometry|point|linestring|polygon',$k["type"]))$_f=$b->selectLengthProcess();}$Me+=$k["privileges"];}list($M,$tc)=$b->selectColumnsProcess($e,$t);$Kc=count($tc)<count($M);$Z=$b->selectSearchProcess($l,$t);$Od=$b->selectOrderProcess($l,$t);$x=$b->selectLimitProcess();$pc=($M?implode(", ",$M):"*".($Cd?", $Cd":""));if($v=="sql"){foreach($e
as$w=>$X){$ta=convert_field($l[$w]);if($ta)$pc.=", $ta AS ".idf_escape($w);}}$pc.="\nFROM ".table($a);$uc=($tc&&$Kc?"\nGROUP BY ".implode(", ",$tc):"").($Od?"\nORDER BY ".implode(", ",$Od):"");if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$Uf=>$J){$ta=convert_field($l[key($J)]);echo$f->result("SELECT".limit(($ta?$ta:idf_escape(key($J)))." FROM ".table($a)," WHERE ".where_check($Uf).($Z?" AND ".implode(" AND ",$Z):"").($Od?" ORDER BY ".implode(", ",$Od):""),1));}exit;}if($_POST&&!$j){$kg="(".implode(") OR (",array_map('where_check',(array)$_POST["check"])).")";$qe=$Wf=null;foreach($t
as$s){if($s["type"]=="PRIMARY"){$qe=array_flip($s["columns"]);$Wf=($M?$qe:array());break;}}foreach((array)$Wf
as$w=>$X){if(in_array(idf_escape($w),$M))unset($Wf[$w]);}if($_POST["export"]){cookie("adminer_import","output=".urlencode($_POST["output"])."&format=".urlencode($_POST["format"]));dump_headers($a);$b->dumpTable($a,"");if(!is_array($_POST["check"])||$Wf===array()){$jg=$Z;if(is_array($_POST["check"]))$jg[]="($kg)";$G="SELECT $pc".($jg?"\nWHERE ".implode(" AND ",$jg):"").$uc;}else{$Sf=array();foreach($_POST["check"]as$X)$Sf[]="(SELECT".limit($pc,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X).$uc,1).")";$G=implode(" UNION ALL ",$Sf);}$b->dumpData($a,"table",$G);exit;}if(!$b->selectEmailProcess($Z,$n)){if($_POST["save"]||$_POST["delete"]){$H=true;$ma=0;$G=table($a);$O=array();if(!$_POST["delete"]){foreach($e
as$C=>$X){$X=process_input($l[$C]);if($X!==null){if($_POST["clone"])$O[idf_escape($C)]=($X!==false?$X:idf_escape($C));elseif($X!==false)$O[]=idf_escape($C)." = $X";}}$G.=($_POST["clone"]?" (".implode(", ",array_keys($O)).")\nSELECT ".implode(", ",$O)."\nFROM ".table($a):" SET\n".implode(",\n",$O));}if($_POST["delete"]||$O){$Ua="UPDATE";if($_POST["delete"]){$Ua="DELETE";$G="FROM $G";}if($_POST["clone"]){$Ua="INSERT";$G="INTO $G";}if($_POST["all"]||($Wf===array()&&$_POST["check"])||$Kc){$H=queries("$Ua $G".($_POST["all"]?($Z?"\nWHERE ".implode(" AND ",$Z):""):"\nWHERE $kg"));$ma=$f->affected_rows;}else{foreach((array)$_POST["check"]as$X){$H=queries($Ua.limit1($G,"\nWHERE ".where_check($X)));if(!$H)break;$ma+=$f->affected_rows;}}}$A=lang(array('%d item has been affected.','%d items have been affected.'),$ma);if($_POST["clone"]&&$H&&$ma==1){$Wc=last_id();if($Wc)$A=sprintf('Item%s has been inserted.'," $Wc");}queries_redirect(remove_from_uri("page"),$A,$H);}elseif(!$_POST["import"]){if(!$_POST["val"])$j='Double click on a value to modify it.';else{$H=true;$ma=0;foreach($_POST["val"]as$Uf=>$J){$O=array();foreach($J
as$w=>$X){$w=bracket_escape($w,1);$O[]=idf_escape($w)." = ".(ereg('char|text',$l[$w]["type"])||$X!=""?$b->processInput($l[$w],$X):"NULL");}$G=table($a)." SET ".implode(", ",$O);$jg=" WHERE ".where_check($Uf).($Z?" AND ".implode(" AND ",$Z):"");$H=queries("UPDATE".($Kc?" $G$jg":limit1($G,$jg)));if(!$H)break;$ma+=$f->affected_rows;}queries_redirect(remove_from_uri(),lang(array('%d item has been affected.','%d items have been affected.'),$ma),$H);}}elseif(is_string($ec=get_file("csv_file",true))){cookie("adminer_import","output=".urlencode($la["output"])."&format=".urlencode($_POST["separator"]));$H=true;$Sa=array_keys($l);preg_match_all('~(?>"[^"]*"|[^"\\r\\n]+)+~',$ec,$hd);$ma=count($hd[0]);begin();$Xe=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));foreach($hd[0]as$w=>$X){preg_match_all("~((\"[^\"]*\")+|[^$Xe]*)$Xe~",$X.$Xe,$id);if(!$w&&!array_diff($id[1],$Sa)){$Sa=$id[1];$ma--;}else{$O=array();foreach($id[1]as$q=>$Pa)$O[idf_escape($Sa[$q])]=($Pa==""&&$l[$Sa[$q]]["null"]?"NULL":q(str_replace('""','"',preg_replace('~^"|"$~','',$Pa))));$H=insert_update($a,$O,$qe);if(!$H)break;}}if($H)queries("COMMIT");queries_redirect(remove_from_uri("page"),lang(array('%d row has been imported.','%d rows have been imported.'),$ma),$H);queries("ROLLBACK");}else$j=upload_error($ec);}}$qf=$b->tableName($S);if(is_ajax())ob_start();page_header('Select'.": $qf",$j);$O=null;if(isset($Me["insert"])){$O="";foreach((array)$_GET["where"]as$X){if(count($n[$X["col"]])==1&&($X["op"]=="="||(!$X["op"]&&!ereg('[_%]',$X["val"]))))$O.="&set".urlencode("[".bracket_escape($X["col"])."]")."=".urlencode($X["val"]);}}$b->selectLinks($S,$O);if(!$e)echo"<p class='error'>".'Unable to select the table'.($l?".":": ".error())."\n";else{echo"<form action='' id='form'>\n","<div style='display: none;'>";hidden_fields_get();echo(DB!=""?'<input type="hidden" name="db" value="'.h(DB).'">'.(isset($_GET["ns"])?'<input type="hidden" name="ns" value="'.h($_GET["ns"]).'">':""):"");echo'<input type="hidden" name="select" value="'.h($a).'">',"</div>\n";$b->selectColumnsPrint($M,$e);$b->selectSearchPrint($Z,$e,$t);$b->selectOrderPrint($Od,$e,$t);$b->selectLimitPrint($x);$b->selectLengthPrint($_f);$b->selectActionPrint($t);echo"</form>\n";$D=$_GET["page"];if($D=="last"){$nc=$f->result("SELECT COUNT(*) FROM ".table($a).($Z?" WHERE ".implode(" AND ",$Z):""));$D=floor(max(0,$nc-1)/$x);}$G=$b->selectQueryBuild($M,$Z,$tc,$Od,$x,$D);if(!$G)$G="SELECT".limit((+$x&&$tc&&$Kc&&$v=="sql"?"SQL_CALC_FOUND_ROWS ":"").$pc,($Z?"\nWHERE ".implode(" AND ",$Z):"").$uc,($x!=""?+$x:null),($D?$x*$D:0),"\n");echo$b->selectQuery($G);$H=$f->query($G);if(!$H)echo"<p class='error'>".error()."\n";else{if($v=="mssql")$H->seek($x*$D);$Jb=array();echo"<form action='' method='post' enctype='multipart/form-data'>\n";$K=array();while($J=$H->fetch_assoc()){if($D&&$v=="oracle")unset($J["RNUM"]);$K[]=$J;}if($_GET["page"]!="last")$nc=(+$x&&$tc&&$Kc?($v=="sql"?$f->result(" SELECT FOUND_ROWS()"):$f->result("SELECT COUNT(*) FROM ($G) x")):count($K));if(!$K)echo"<p class='message'>".'No rows.'."\n";else{$_a=$b->backwardKeys($a,$qf);echo"<table id='table' cellspacing='0' class='nowrap checkable' onclick='tableClick(event);' onkeydown='return editingKeydown(event);'>\n","<thead><tr>".(!$tc&&$M?"":"<td><input type='checkbox' id='all-page' onclick='formCheck(this, /check/);'> <a href='".h($_GET["modify"]?remove_from_uri("modify"):$_SERVER["REQUEST_URI"]."&modify=1")."'>".'edit'."</a>");$wd=array();$rc=array();reset($M);$ze=1;foreach($K[0]as$w=>$X){if($w!=$Cd){$X=$_GET["columns"][key($M)];$k=$l[$M?($X?$X["col"]:current($M)):$w];$C=($k?$b->fieldName($k,$ze):"*");if($C!=""){$ze++;$wd[$w]=$C;$Ta=idf_escape($w);$_c=remove_from_uri('(order|desc)[^=]*|page').'&order%5B0%5D='.urlencode($w);$sb="&desc%5B0%5D=1";echo'<th onmouseover="columnMouse(this);" onmouseout="columnMouse(this, \' hidden\');">','<a href="'.h($_c.($Od[0]==$Ta||$Od[0]==$w||(!$Od&&$Kc&&$tc[0]==$Ta)?$sb:'')).'">';echo(!$M||$X?apply_sql_function($X["fun"],$C):h(current($M)))."</a>";echo"<span class='column hidden'>","<a href='".h($_c.$sb)."' title='".'descending'."' class='text'> ‚Üì</a>";if(!$X["fun"])echo'<a href="#fieldset-search" onclick="selectSearch(\''.h(js_escape($w)).'\'); return false;" title="'.'Search'.'" class="text jsonly"> =</a>';echo"</span>";}$rc[$w]=$X["fun"];next($M);}}$cd=array();if($_GET["modify"]){foreach($K
as$J){foreach($J
as$w=>$X)$cd[$w]=max($cd[$w],min(40,strlen(utf8_decode($X))));}}echo($_a?"<th>".'Relations':"")."</thead>\n";if(is_ajax()){if($x%2==1&&$D%2==1)odd();ob_end_clean();}foreach($b->rowDescriptions($K,$n)as$B=>$J){$Tf=unique_array($K[$B],$t);$Uf="";foreach($Tf
as$w=>$X)$Uf.="&".($X!==null?urlencode("where[".bracket_escape($w)."]")."=".urlencode($X):"null%5B%5D=".urlencode($w));echo"<tr".odd().">".(!$tc&&$M?"":"<td>".checkbox("check[]",substr($Uf,1),in_array(substr($Uf,1),(array)$_POST["check"]),"","this.form['all'].checked = false; formUncheck('all-page');").($Kc||information_schema(DB)?"":" <a href='".h(ME."edit=".urlencode($a).$Uf)."'>".'edit'."</a>"));foreach($J
as$w=>$X){if(isset($wd[$w])){$k=$l[$w];if($X!=""&&(!isset($Jb[$w])||$Jb[$w]!=""))$Jb[$w]=(is_mail($X)?$wd[$w]:"");$y="";$X=$b->editVal($X,$k);if($X!==null){if(ereg('blob|bytea|raw|file',$k["type"])&&$X!="")$y=h(ME.'download='.urlencode($a).'&field='.urlencode($w).$Uf);if($X==="")$X="&nbsp;";elseif(is_utf8($X)){if($_f!=""&&ereg('text|lob|geometry|point|linestring|polygon',$k["type"]))$X=shorten_utf8($X,max(0,+$_f));else$X=h($X);}if(!$y){foreach((array)$n[$w]as$m){if(count($n[$w])==1||end($m["source"])==$w){$y="";foreach($m["source"]as$q=>$bf)$y.=where_link($q,$m["target"][$q],$K[$B][$bf]);$y=h(($m["db"]!=""?preg_replace('~([?&]db=)[^&]+~','\\1'.urlencode($m["db"]),ME):ME).'select='.urlencode($m["table"]).$y);if(count($m["source"])==1)break;}}}if($w=="COUNT(*)"){$y=h(ME."select=".urlencode($a));$q=0;foreach((array)$_GET["where"]as$W){if(!array_key_exists($W["col"],$Tf))$y.=h(where_link($q++,$W["col"],$W["val"],$W["op"]));}foreach($Tf
as$Pc=>$W)$y.=h(where_link($q++,$Pc,$W));}}if(!$y){if(is_mail($X))$y="mailto:$X";if($xe=is_url($J[$w]))$y=($xe=="http"&&$ba?$J[$w]:"$xe://www.adminer.org/redirect/?url=".urlencode($J[$w]));}$r=h("val[$Uf][".bracket_escape($w)."]");$Y=$_POST["val"][$Uf][bracket_escape($w)];$wc=h($Y!==null?$Y:$J[$w]);$gd=strpos($X,"<i>...</i>");$Fb=is_utf8($X)&&$K[$B][$w]==$J[$w]&&!$rc[$w];$zf=ereg('text|lob',$k["type"]);echo(($_GET["modify"]&&$Fb)||$Y!==null?"<td>".($zf?"<textarea name='$r' cols='30' rows='".(substr_count($J[$w],"\n")+1)."'>$wc</textarea>":"<input name='$r' value='$wc' size='$cd[$w]'>"):"<td id='$r' ondblclick=\"".($Fb?"selectDblClick(this, event".($gd?", 2":($zf?", 1":"")).")":"alert('".h('Use edit link to modify this value.')."')").";\">".$b->selectVal($X,$y,$k));}}if($_a)echo"<td>";$b->backwardKeysPrint($_a,$K[$B]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n",(!$tc&&$M?"":"<script type='text/javascript'>tableCheck();</script>\n");}if(($K||$D)&&!is_ajax()){$Tb=true;if($_GET["page"]!="last"&&+$x&&!$Kc&&($nc>=$x||$D)){$nc=found_rows($S,$Z);if($nc<max(1e4,2*($D+1)*$x))$nc=reset(slow_query("SELECT COUNT(*) FROM ".table($a).($Z?" WHERE ".implode(" AND ",$Z):"")));else$Tb=false;}echo"<p class='pages'>";if(+$x&&($nc===false||$nc>$x)){$kd=($nc===false?$D+(count($K)>=$x?2:1):floor(($nc-1)/$x));echo'<a href="'.h(remove_from_uri("page"))."\" onclick=\"pageClick(this.href, +prompt('".'Page'."', '".($D+1)."'), event); return false;\">".'Page'."</a>:",pagination(0,$D).($D>5?" ...":"");for($q=max(1,$D-4);$q<min($kd,$D+5);$q++)echo
pagination($q,$D);echo($D+5<$kd?" ...":"").($Tb&&$nc!==false?pagination($kd,$D):' <a href="'.h(remove_from_uri("page")."&page=last").'">'.'last'."</a>");}echo($nc!==false?" (".($Tb?"":"~ ").lang(array('%d row','%d rows'),$nc).")":""),(+$x&&($nc===false?count($K)+1:$nc-$D*$x)>$x?' <a href="'.h(remove_from_uri("page")."&page=".($D+1)).'" onclick="return !selectLoadMore(this, '.(+$x).', \''.'Loading'.'\');">'.'Load more data'.'</a>':'')," ".checkbox("all",1,0,'whole result')."\n";if($b->selectCommandPrint()){echo'<fieldset><legend>Edit</legend><div>
<input type="submit" value="Save"',($_GET["modify"]?'':' title="'.'Double click on a value to modify it.'.'" class="jsonly"');?>>
<input type="submit" name="edit" value="Edit">
<input type="submit" name="clone" value="Clone">
<input type="submit" name="delete" value="Delete" onclick="return confirm('Are you sure? (' + (this.form['all'].checked ? <?php echo$nc,' : formChecked(this, /check/)) + \')\');">
</div></fieldset>
';}$lc=$b->dumpFormat();if($lc){print_fieldset("export",'Export');$Xd=$b->dumpOutput();echo($Xd?html_select("output",$Xd,$la["output"])." ":""),html_select("format",$lc,$la["format"])," <input type='submit' name='export' value='".'Export'."'>\n","</div></fieldset>\n";}}if($b->selectImportPrint()){print_fieldset("import",'Import',!$K);echo"<input type='file' name='csv_file'> ",html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$la["format"],1);echo" <input type='submit' name='import' value='".'Import'."'>","<input type='hidden' name='token' value='$T'>\n","</div></fieldset>\n";}$b->selectEmailPrint(array_filter($Jb,'strlen'),$e);echo"</form>\n";}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["variables"])){$ff=isset($_GET["status"]);page_header($ff?'Status':'Variables');$eg=($ff?show_status():show_variables());if(!$eg)echo"<p class='message'>".'No rows.'."\n";else{echo"<table cellspacing='0'>\n";foreach($eg
as$w=>$X){echo"<tr>","<th><code class='jush-".$v.($ff?"status":"set")."'>".h($w)."</code>","<td>".nbsp($X);}echo"</table>\n";}}elseif(isset($_GET["script"])){header("Content-Type: text/javascript; charset=utf-8");if($_GET["script"]=="db"){$nf=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach(table_status()as$S){$r=js_escape($S["Name"]);json_row("Comment-$r",nbsp($S["Comment"]));if(!is_view($S)){foreach(array("Engine","Collation")as$w)json_row("$w-$r",nbsp($S[$w]));foreach($nf+array("Auto_increment"=>0,"Rows"=>0)as$w=>$X){if($S[$w]!=""){$X=number_format($S[$w],0,'.',',');json_row("$w-$r",($w=="Rows"&&$X&&$S["Engine"]==($df=="pgsql"?"table":"InnoDB")?"~ $X":$X));if(isset($nf[$w]))$nf[$w]+=($S["Engine"]!="InnoDB"||$w!="Data_free"?$S[$w]:0);}elseif(array_key_exists($w,$S))json_row("$w-$r");}}}foreach($nf
as$w=>$X)json_row("sum-$w",number_format($X,0,'.',','));json_row("");}elseif($_GET["script"]=="kill")$f->query("KILL ".(+$_POST["kill"]));else{foreach(count_tables($b->databases())as$i=>$X)json_row("tables-".js_escape($i),$X);json_row("");}exit;}else{$wf=array_merge((array)$_POST["tables"],(array)$_POST["views"]);if($wf&&!$j&&!$_POST["search"]){$H=true;$A="";if($v=="sql"&&count($_POST["tables"])>1&&($_POST["drop"]||$_POST["truncate"]||$_POST["copy"]))queries("SET foreign_key_checks = 0");if($_POST["truncate"]){if($_POST["tables"])$H=truncate_tables($_POST["tables"]);$A='Tables have been truncated.';}elseif($_POST["move"]){$H=move_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$A='Tables have been moved.';}elseif($_POST["copy"]){$H=copy_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$A='Tables have been copied.';}elseif($_POST["drop"]){if($_POST["views"])$H=drop_views($_POST["views"]);if($H&&$_POST["tables"])$H=drop_tables($_POST["tables"]);$A='Tables have been dropped.';}elseif($v!="sql"){$H=($v=="sqlite"?queries("VACUUM"):apply_queries("VACUUM".($_POST["optimize"]?"":" ANALYZE"),$_POST["tables"]));$A='Tables have been optimized.';}elseif($_POST["tables"]&&($H=queries(($_POST["optimize"]?"OPTIMIZE":($_POST["check"]?"CHECK":($_POST["repair"]?"REPAIR":"ANALYZE")))." TABLE ".implode(", ",array_map('idf_escape',$_POST["tables"]))))){while($J=$H->fetch_assoc())$A.="<b>".h($J["Table"])."</b>: ".h($J["Msg_text"])."<br>";}queries_redirect(substr(ME,0,-1),$A,$H);}page_header(($_GET["ns"]==""?'Database'.": ".h(DB):'Schema'.": ".h($_GET["ns"])),$j,true);if($b->homepage()){if($_GET["ns"]!==""){echo"<h3>".'Tables and views'."</h3>\n";$vf=tables_list();if(!$vf)echo"<p class='message'>".'No tables.'."\n";else{echo"<form action='' method='post'>\n","<p>".'Search data in tables'.": <input name='query' value='".h($_POST["query"])."'> <input type='submit' name='search' value='".'Search'."'>\n";if($_POST["search"]&&$_POST["query"]!="")search_tables();echo"<table cellspacing='0' class='nowrap checkable' onclick='tableClick(event);'>\n",'<thead><tr class="wrap"><td><input id="check-all" type="checkbox" onclick="formCheck(this, /^(tables|views)\[/);">','<th>'.'Table','<td>'.'Engine','<td>'.'Collation','<td>'.'Data Length','<td>'.'Index Length','<td>'.'Data Free','<td>'.'Auto Increment','<td>'.'Rows',(support("comment")?'<td>'.'Comment':''),"</thead>\n";foreach($vf
as$C=>$U){$fg=($U!==null&&!eregi("table",$U));echo'<tr'.odd().'><td>'.checkbox(($fg?"views[]":"tables[]"),$C,in_array($C,$wf,true),"","formUncheck('check-all');"),'<th><a href="'.h(ME).'table='.urlencode($C).'" title="'.'Show structure'.'">'.h($C).'</a>';if($fg){echo'<td colspan="6"><a href="'.h(ME)."view=".urlencode($C).'" title="'.'Alter view'.'">'.'View'.'</a>','<td align="right"><a href="'.h(ME)."select=".urlencode($C).'" title="'.'Select data'.'">?</a>';}else{foreach(array("Engine"=>array(),"Collation"=>array(),"Data_length"=>array("create",'Alter table'),"Index_length"=>array("indexes",'Alter indexes'),"Data_free"=>array("edit",'New item'),"Auto_increment"=>array("auto_increment=1&create",'Alter table'),"Rows"=>array("select",'Select data'),)as$w=>$y)echo($y?"<td align='right'><a href='".h(ME."$y[0]=").urlencode($C)."' id='$w-".h($C)."' title='$y[1]'>?</a>":"<td id='$w-".h($C)."'>&nbsp;");}echo(support("comment")?"<td id='Comment-".h($C)."'>&nbsp;":"");}echo"<tr><td>&nbsp;<th>".sprintf('%d in total',count($vf)),"<td>".nbsp($v=="sql"?$f->result("SELECT @@storage_engine"):""),"<td>".nbsp(db_collation(DB,collations()));foreach(array("Data_length","Index_length","Data_free")as$w)echo"<td align='right' id='sum-$w'>&nbsp;";echo"</table>\n","<script type='text/javascript'>tableCheck();</script>\n";if(!information_schema(DB)){echo"<p>".(ereg('^(sql|sqlite|pgsql)$',$v)?($v!="sqlite"?"<input type='submit' value='".'Analyze'."'> ":"")."<input type='submit' name='optimize' value='".'Optimize'."'> ":"").($v=="sql"?"<input type='submit' name='check' value='".'Check'."'> <input type='submit' name='repair' value='".'Repair'."'> ":"")."<input type='submit' name='truncate' value='".'Truncate'."'".confirm("formChecked(this, /tables/)")."> <input type='submit' name='drop' value='".'Drop'."'".confirm("formChecked(this, /tables|views/)").">\n";$h=(support("scheme")?schemas():$b->databases());if(count($h)!=1&&$v!="sqlite"){$i=(isset($_POST["target"])?$_POST["target"]:(support("scheme")?$_GET["ns"]:DB));echo"<p>".'Move to other database'.": ",($h?html_select("target",$h,$i):'<input name="target" value="'.h($i).'">')," <input type='submit' name='move' value='".'Move'."'>",(support("copy")?" <input type='submit' name='copy' value='".'Copy'."'>":""),"\n";}echo"<input type='hidden' name='token' value='$T'>\n";}echo"</form>\n";}echo'<p><a href="'.h(ME).'create=">'.'Create table'."</a>\n";if(support("view"))echo'<a href="'.h(ME).'view=">'.'Create view'."</a>\n";if(support("routine")){echo"<h3>".'Routines'."</h3>\n";$Qe=routines();if($Qe){echo"<table cellspacing='0'>\n",'<thead><tr><th>'.'Name'.'<td>'.'Type'.'<td>'.'Return type'."<td>&nbsp;</thead>\n";odd('');foreach($Qe
as$J){echo'<tr'.odd().'>','<th><a href="'.h(ME).($J["ROUTINE_TYPE"]!="PROCEDURE"?'callf=':'call=').urlencode($J["ROUTINE_NAME"]).'">'.h($J["ROUTINE_NAME"]).'</a>','<td>'.h($J["ROUTINE_TYPE"]),'<td>'.h($J["DTD_IDENTIFIER"]),'<td><a href="'.h(ME).($J["ROUTINE_TYPE"]!="PROCEDURE"?'function=':'procedure=').urlencode($J["ROUTINE_NAME"]).'">'.'Alter'."</a>";}echo"</table>\n";}echo'<p>'.(support("procedure")?'<a href="'.h(ME).'procedure=">'.'Create procedure'.'</a> ':'').'<a href="'.h(ME).'function=">'.'Create function'."</a>\n";}if(support("event")){echo"<h3>".'Events'."</h3>\n";$K=get_rows("SHOW EVENTS");if($K){echo"<table cellspacing='0'>\n","<thead><tr><th>".'Name'."<td>".'Schedule'."<td>".'Start'."<td>".'End'."</thead>\n";foreach($K
as$J){echo"<tr>",'<th><a href="'.h(ME).'event='.urlencode($J["Name"]).'">'.h($J["Name"])."</a>","<td>".($J["Execute at"]?'At given time'."<td>".$J["Execute at"]:'Every'." ".$J["Interval value"]." ".$J["Interval field"]."<td>$J[Starts]"),"<td>$J[Ends]";}echo"</table>\n";$Sb=$f->result("SELECT @@event_scheduler");if($Sb&&$Sb!="ON")echo"<p class='error'><code class='jush-sqlset'>event_scheduler</code>: ".h($Sb)."\n";}echo'<p><a href="'.h(ME).'event=">'.'Create event'."</a>\n";}if($vf)echo"<script type='text/javascript'>ajaxSetHtml('".js_escape(ME)."script=db');</script>\n";}}}page_footer();