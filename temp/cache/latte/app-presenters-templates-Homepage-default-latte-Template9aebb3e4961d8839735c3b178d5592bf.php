<?php
// source: C:\blog\app\presenters/templates/Homepage/default.latte

class Template9aebb3e4961d8839735c3b178d5592bf extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('6d76583dba', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block content
//
if (!function_exists($_b->blocks['content'][] = '_lb40653099bb_content')) { function _lb40653099bb_content($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
;call_user_func(reset($_b->blocks['title']), $_b, get_defined_vars())  ?>

<?php $iterations = 0; foreach ($posts as $post) { ?>  <div class="post">
    <!--@deprecated usage of <table> tag-->
    <table>
      <tr>
          <td rowspan="5" id="post-image" style="<?php if (($post->link != NULL)) { ?>
background-image: url(<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::escapeCss($post->link), ENT_COMPAT) ?>
)<?php } else { ?>background: <?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::escapeCss(App\Presenters\PostPresenter::randomColor()), ENT_COMPAT) ;} ?>">
          </td>
        <td id="nd">Titulek:</td>
        <td><a href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Post:show", array($post->id)), ENT_COMPAT) ?>
"><?php echo Latte\Runtime\Filters::escapeHtml($post->title, ENT_NOQUOTES) ?></a></td>
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
        <td>Komentáře:</td>
        <td><?php echo Latte\Runtime\Filters::escapeHtml($post->related('comments')->where('post_id', $post->id)->count(), ENT_NOQUOTES) ?></td>
      </tr>
      <tr>
        <td>Anotace:</td>
        <td><?php echo Latte\Runtime\Filters::escapeHtml(Nette\Utils\Strings::truncate($post->subtitle, 250, ' ...'), ENT_NOQUOTES) ?></td>
      </tr>      
    </table>

  </div>
<?php $iterations++; } ?>
  
  <h1>RSS čtečka <a style="cursor:default" onclick="getFileContent('rssOutput', 'getRSS.php')">▼</a></h1>

  <div id="rssOutput"></div><?php
}}

//
// block title
//
if (!function_exists($_b->blocks['title'][] = '_lbef05467be2_title')) { function _lbef05467be2_title($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?>  <h1>Můj blog</h1>
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
if ($_l->extends) { ob_end_clean(); return $template->renderChildTemplate($_l->extends, get_defined_vars()); }
call_user_func(reset($_b->blocks['content']), $_b, get_defined_vars()) ; 
}}