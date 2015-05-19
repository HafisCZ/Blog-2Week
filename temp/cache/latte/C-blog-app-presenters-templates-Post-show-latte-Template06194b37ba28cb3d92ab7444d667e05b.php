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
?><a href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("edit", array($post->id)), ENT_COMPAT) ?>
">Upravit</a><br>
<a href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("remove", array($post->id)), ENT_COMPAT) ?>
">Smazat</a>
<div class="date"><?php echo Latte\Runtime\Filters::escapeHtml($template->date($post->created_at, 'F j, Y'), ENT_NOQUOTES) ?></div>
<?php call_user_func(reset($_b->blocks['title']), $_b, get_defined_vars())  ?>
<div class="post"><?php echo Latte\Runtime\Filters::escapeHtml($post->content, ENT_NOQUOTES) ?></div>


<hr>
<h2>Comments</h2>
<div class="comments">
<?php $iterations = 0; foreach ($comments as $comment) { ?>
    <p><b><?php if ($_l->ifs[] = ($comment->email)) { ?><a href="mailto:<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($comment->email), ENT_COMPAT) ?>
"><?php } echo Latte\Runtime\Filters::escapeHtml($comment->name, ENT_NOQUOTES) ;if (array_pop($_l->ifs)) { ?>
</a><?php } ?>
</b> said:</p>
    <div><?php echo Latte\Runtime\Filters::escapeHtml($comment->content, ENT_NOQUOTES) ?></div>
<?php $iterations++; } ?>
</div>

<hr>
<h2>Post new comment</h2>
<?php $_l->tmp = $_control->getComponent("commentForm"); if ($_l->tmp instanceof Nette\Application\UI\IRenderable) $_l->tmp->redrawControl(NULL, FALSE); $_l->tmp->render() ;
}}

//
// block title
//
if (!function_exists($_b->blocks['title'][] = '_lb12c573ae0d_title')) { function _lb12c573ae0d_title($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?><h2><b><?php echo Latte\Runtime\Filters::escapeHtml($post->title, ENT_NOQUOTES) ?>
</b> od uživatele <b><?php echo Latte\Runtime\Filters::escapeHtml($post->author, ENT_NOQUOTES) ?></b></h2>
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