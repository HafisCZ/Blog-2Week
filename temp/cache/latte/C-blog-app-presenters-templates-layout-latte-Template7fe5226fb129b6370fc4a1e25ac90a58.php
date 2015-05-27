<?php
// source: C:\blog\app\presenters/templates/@layout.latte

class Template7fe5226fb129b6370fc4a1e25ac90a58 extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('0c613938d8', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block head
//
if (!function_exists($_b->blocks['head'][] = '_lbb95ee4daf4_head')) { function _lbb95ee4daf4_head($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
;
}}

//
// block scripts
//
if (!function_exists($_b->blocks['scripts'][] = '_lbac0284ea02_scripts')) { function _lbac0284ea02_scripts($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?>	<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
	<script src="//nette.github.io/resources/js/netteForms.min.js"></script>
	<script src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/js/main.js"></script>
  <script type="text/javascript" src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/js/nette.ajax.js"></script>
  <script type="text/javascript">
    $(function () {
      $.nette.init();
    });
    $(document).ready(function(){
      $("#remove_flash_message").click(function(){
        $("div").remove(".flash");
      });
    });
    $(document).ready(function(){
      $("#comment").click(function(){
        $("p").remove("#comment");
        $("body").css('color', '#FFFFFF');
        $("body").css('background', '#FFFFFF');
        $("h1").css('color', '#FFFFFF');
        $("h1").css('background', '#FFFFFF');
        $("nav").css('color', '#FFFFFF');
        $("nav").css('background', '#FFFFFF');
      });
    });
  </script>
  <script src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/js/ajax.js"></script>
<?php
}}

//
// end of blocks
//

// template extending

$_l->extends = empty($_g->extended) && isset($_control) && $_control instanceof Nette\Application\UI\Presenter ? $_control->findLayoutTemplateFile() : NULL; $_g->extended = TRUE;

if ($_l->extends) { ob_start();}

// prolog Nette\Bridges\ApplicationLatte\UIMacros

// snippets support
if (empty($_l->extends) && !empty($_control->snippetMode)) {
	return Nette\Bridges\ApplicationLatte\UIMacros::renderSnippets($_control, $_b, get_defined_vars());
}

//
// main template
//
?>
<!-- Hlavní layout pro stránku -->
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  
    <title><?php if (isset($_b->blocks["title"])) { ob_start(); Latte\Macros\BlockMacrosRuntime::callBlock($_b, 'title', $template->getParameters()); echo $template->striptags(ob_get_clean()) ;} ?></title>

    <link rel="stylesheet" href="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/css/style.css">
    <link rel="shortcut icon" href="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/favicon.ico">    
    
    <script type="text/javascript" src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/js/edit.js"></script>        
                 
    <?php if ($_l->extends) { ob_end_clean(); return $template->renderChildTemplate($_l->extends, get_defined_vars()); }
call_user_func(reset($_b->blocks['head']), $_b, get_defined_vars())  ?>

    
  </head>

  <body>
    <nav class="menu">
      <div>
        <ul>
        <li><a href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Homepage:"), ENT_COMPAT) ?>
"><span>Úvodní strana</span></a></li>
        <li><?php if ($user->loggedIn) { ?><a href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Post:create"), ENT_COMPAT) ?>
"><span>Přidat příspěvek</span></a><?php } ?>
</li>
        <li><a href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Post:list"), ENT_COMPAT) ?>
"><span>Seznam článků</span></a></li>
        <li><a type="application/rss+xml" href="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/feed.xml"><span>RSS feed</span></a></li>
<?php if (($user->loggedIn)) { ?>
          <li style="float: right"><a href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Sign:out"), ENT_COMPAT) ?>
"><span>Odhlásit</span></a></li>
          <li style="float: right"><a href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Sign:settings"), ENT_COMPAT) ?>
"><span>Nastavení</span></a></li>
          <li style="float: right"><a href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Post:author", array($user->getId())), ENT_COMPAT) ?>"><span>Profil</span></a></li>
          <li style="float: right" class="menu-text"><span><?php if (($user->getIdentity()->displayname != NULL)) { echo Latte\Runtime\Filters::escapeHtml($user->getIdentity()->displayname, ENT_NOQUOTES) ;} else { echo Latte\Runtime\Filters::escapeHtml($user->getId(), ENT_NOQUOTES) ;} ?></span></li>
<?php } else { ?>
          <li style="float: right"><a href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Sign:in"), ENT_COMPAT) ?>
"><span>Registrace</span></a></li>
          <li style="float: right"><a href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Sign:in"), ENT_COMPAT) ?>
"><span>Přihlášení</span></a></li>
          <li style="float: right" class="menu-text"><span><?php echo Latte\Runtime\Filters::escapeHtml(getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR'), ENT_NOQUOTES) ?></span></li>
<?php } ?>
        </ul>
      </div>
    </nav>
  
<?php Latte\Macros\BlockMacrosRuntime::callBlock($_b, 'content', $template->getParameters()) ?>
  
<?php $iterations = 0; foreach ($flashes as $flash) { ?>  <div<?php if ($_l->tmp = array_filter(array('flash'))) echo ' class="' . Latte\Runtime\Filters::escapeHtml(implode(" ", array_unique($_l->tmp)), ENT_COMPAT) . '"' ?>
><button id="remove_flash_message" style="cursor:default">✖</button> <?php echo Latte\Runtime\Filters::escapeHtml($flash->message, ENT_NOQUOTES) ?></div>
<?php $iterations++; } ?>
                                                                              
<?php call_user_func(reset($_b->blocks['scripts']), $_b, get_defined_vars())  ?>
</body>
</html>
<?php
}}