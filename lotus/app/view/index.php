<header class="page-header">
	<h1 class="page-title"><?php echo $title?> </h1>
</header>


<div id='primary' class='content-area'>
	<div id='content' class='site-content full' role='main'>
		<article id="post-<?php the_ID(); ?>" class='page type-page status-publish hentry instock'>
			<div class="entry-content">
				<?php
				echo $this->bootstrapHelper->displayMessage();

				$this->render($template);

				?>
			</div>
		</article><!-- #post -->
	</div>
</div>