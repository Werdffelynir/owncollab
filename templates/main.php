<?php
/**
 * @var OCP\Template $this
 * @var array $_
 */

?>

<div id="app">
	<div id="app-content">
        <div id="app-content-error"></div>
		<div id="app-content-wrapper">
            <div class="tbl clb_dashboard">
                <div class="tbl_cell"><?php print_unescaped($this->inc('part.leftmenu')); ?></div>
                <div class="tbl_cell"><?php print_unescaped($this->inc('part.content')); ?></div>
            </div>
		</div>
	</div>
    <div id="app-sidebar" class="disappear">
		<?php print_unescaped($this->inc('part.sidebar')); ?>
	</div>
</div>
