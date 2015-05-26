<?php
// source: C:\blog\app\presenters/templates/Post/show.latte

class Template06194b37ba28cb3d92ab7444d667e05b extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('c6cdb40c69', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block content
//
if (!function_exists($_b->blocks['content'][] = '_lb13870af431_content')) { function _lb13870af431_content($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
;call_user_func(reset($_b->blocks['title']), $_b, get_defined_vars())  ?>
<table class="post">
  <tr>
    <td>
<?php if ($user->loggedIn) { ?>      <a name="edit" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("edit", array($post->id)), ENT_COMPAT) ?>
">Upravit</a>  
<?php } ?>
    </td>
    <td>
<?php if ($user->loggedIn) { ?>      <a name="remove" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("remove", array($post->id)), ENT_COMPAT) ?>
">Smazat</a>
<?php } ?>
    </td>
  </tr>
</table>

<div class="post">
  <table>
    <tr>
      <td rowspan="4" id="post-image-2" style="<?php if (($post->link != NULL)) { ?>
background-image: url(<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::escapeCss($post->link), ENT_COMPAT) ?>
)<?php } else { ?>background: <?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::escapeCss(App\Presenters\PostPresenter::randomColor()), ENT_COMPAT) ;} ?>"></td>
      <td id="nd">Titulek:</td>
      <td><?php echo Latte\Runtime\Filters::escapeHtml($post->title, ENT_NOQUOTES) ?></td>
    </tr>
    <tr>
      <td>Anotace:</td>
      <td><?php echo Latte\Runtime\Filters::escapeHtml($post->subtitle, ENT_NOQUOTES) ?></td>
    </tr>
    <tr>
      <td>Autor:</td>
      <td><?php if (($post->username == NULL)) { ?>Neznámý<?php } else { ?><a href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Post:author", array($post->username)), ENT_COMPAT) ?>
"><?php echo Latte\Runtime\Filters::escapeHtml($post->username, ENT_NOQUOTES) ?>
</a><?php } ?></td>
    </tr>
    <tr>
      <td>Datum:</td>
      <td><?php echo Latte\Runtime\Filters::escapeHtml($template->date($post->created_at, 'F j, Y H:m'), ENT_NOQUOTES) ?></td>
    </tr>
    <tr>
      <td colspan="3">
        <div class="post-div">
          <?php echo Latte\Runtime\Filters::escapeHtml($post->content, ENT_NOQUOTES) ?>

        </div>
      </td>
    </tr>      
  </table>
</div>

<h1>Komentáře k příspěvku</h1>
<div class="comments">
<?php $iterations = 0; foreach ($comments as $comment) { ?>
    <table>
      <tr>
        <td>
<?php if (($user->loggedIn)) { ?>
            <a href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("removeComment", array($comment->id)), ENT_COMPAT) ?>
">Odstranit</a>   
<?php } if (($comment->username == NULL)) { ?>
            Neznámý
<?php } else { ?>
            <a href="mailto:<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($comment->email), ENT_COMPAT) ?>
"><?php echo Latte\Runtime\Filters::escapeHtml($comment->username, ENT_NOQUOTES) ?></a>
<?php } ?>
        </td>
        <td><?php echo Latte\Runtime\Filters::escapeHtml($comment->ip, ENT_NOQUOTES) ?></td>
        <td><?php echo Latte\Runtime\Filters::escapeHtml($template->date($comment->created_at, 'F j, Y H:m'), ENT_NOQUOTES) ?></td>
      </tr>
      <tr>
        <td colspan="3">
          <?php echo Latte\Runtime\Filters::escapeHtml($comment->content, ENT_NOQUOTES) ?>

        </td>  
      </tr>
    </table>
<?php $iterations++; } ?>
</div>

<?php if (($user->isLoggedIn())) { ?>
  <h1>Přidat komentář</h1>
    <div class="standard-form">
<?php $_l->tmp = $_control->getComponent("commentForm"); if ($_l->tmp instanceof Nette\Application\UI\IRenderable) $_l->tmp->redrawControl(NULL, FALSE); $_l->tmp->render() ?>
  </div>
<?php } 
}}

//
// block title
//
if (!function_exists($_b->blocks['title'][] = '_lb12c573ae0d_title')) { function _lb12c573ae0d_title($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?><h1><?php echo Latte\Runtime\Filters::escapeHtml($post->title, ENT_NOQUOTES) ?></h1>
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

<?php if ($_l->extends) { ob_end_clean(); return $template->renderChildTemplate($_l->extends, get_defined_vars()); }
call_user_func(reset($_b->blocks['content']), $_b, get_defined_vars()) ; 
}}