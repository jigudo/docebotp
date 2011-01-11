
<?php foreach ($nodes as $item): ?>
  <div class="node node-page node-teaser clearfix">

    <h2><?php // Title:  ?>
      <a href="<?php echo $item->course_link; ?>&amp;auth=<?php echo $auth; ?>"><?php echo $item->course_name; ?></a>
    </h2>

  <?php // Description: ?>
  <div class="content clearfix">
    <div class="field field-name-body field-type-text-with-summary field-label-hidden">
      <div class="field-items">
        <div property="content:encoded" class="field-item even">
          <p><?php echo $item->course_description; ?></p>
        </div>
      </div>
    </div>
  </div>

  <?php // Enter course link: ?>
  <div class="link-wrapper">
    <ul class="links inline">
      <li class="node-readmore first last">
        <a title="<?php echo $item->course_name; ?>" href="<?php echo $item->course_link; ?>&amp;auth=<?php echo $auth; ?>"><?php echo t('Enter course'); ?></a>
      </li>
    </ul>
  </div>

</div>
<?php endforeach; ?>