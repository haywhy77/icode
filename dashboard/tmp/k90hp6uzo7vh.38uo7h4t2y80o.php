</div>
<!-- //END PAGE WRAPPER -->

<!-- TEMPLATE SETTINGS -->
<div class="fixed-panel" id="fixed_panel">
    <div class="fixed-panel__header">
        <h5 class="title">Template settings</h5>
        <button class="btn btn-light btn-icon" data-action="fixedpanel-toggle"><span class="li-cross"></span></button>
    </div>

    <div class="fixed-panel__content scroll" style="max-height: 100%">

        <h5>Information</h5>
        <p class="margin-bottom-20">Use this panel to configure template settings and layout options.</p>

        <div class="form-group">
           <div class="form-check margin-bottom-30">
             <input class="form-check-input" type="checkbox" id="rw_settings_show">
             <label class="form-check-label" for="rw_settings_show">Disable auto show template settings</label>
           </div>            
        </div>

        <div class="divider divider--sm"></div>

        <h5 class="margin-top-30">Layout option</h5>
        <div class="form-group">
            <select class="form-select margin-bottom-20" id="rw_settings_layout">
                <option value="default">Default</option>
                <option value="boxed">Boxed</option>
                <option value="indent">Indent</option>
            </select>
        </div>

        <div class="d-none" id="rw_settings_layout_boxed_group">
            <div class="form-check margin-bottom-10">
               <input class="form-check-input" type="checkbox" id="rw_settings_layout_boxed_vspace">
               <label class="form-check-label" for="rw_settings_layout_boxed_vspace">Vertical spacing</label>
            </div>
            <div class="form-check margin-bottom-10">
               <input class="form-check-input" type="checkbox" id="rw_settings_layout_boxed_rounded">
               <label class="form-check-label" for="rw_settings_layout_boxed_rounded">Rounded corners</label>
            </div>
            <div class="form-check">
               <input class="form-check-input" type="checkbox" id="rw_settings_layout_boxed_shadowed">
               <label class="form-check-label" for="rw_settings_layout_boxed_shadowed">Add shadows</label>
            </div>
        </div>
        <div class="d-none" id="rw_settings_layout_indent_group">
            <div class="d-none" id="rw_settings_layout_indent_header_group">
               <div class="form-check margin-bottom-10">
                  <input class="form-check-input" type="checkbox" id="rw_settings_layout_indent_header">
                  <label class="form-check-label" for="rw_settings_layout_indent_header">Single header</label>
               </div>
               <div class="form-check margin-bottom-10">
                  <input class="form-check-input" type="checkbox" id="rw_settings_layout_indent_header_relative">
                  <label class="form-check-label" for="rw_settings_layout_indent_header_relative">Relative header</label>
               </div>
            </div>
            <div class="d-none" id="rw_settings_layout_indent_container">
               <div class="form-check margin-bottom-10">
                  <input class="form-check-input" type="checkbox" id="rw_settings_layout_indent_container_single">
                  <label class="form-check-label" for="rw_settings_layout_indent_container_single">Single page container</label>
               </div>
            </div>
            <div class="form-check margin-bottom-10">
               <input class="form-check-input" type="checkbox" id="rw_settings_layout_indent_rounded">
               <label class="form-check-label" for="rw_settings_layout_indent_rounded">Rounded corners</label>
            </div>
            <div class="form-check margin-bottom-10">
               <input class="form-check-input" type="checkbox" id="rw_settings_layout_indent_shadowed">
               <label class="form-check-label" for="rw_settings_layout_indent_shadowed">Add shadows</label>
            </div>
            <div class="form-check">
               <input class="form-check-input" type="checkbox" id="rw_settings_layout_indent_move_heading">
               <label class="form-check-label" for="rw_settings_layout_indent_move_heading">Heading under header (relative header)</label>
            </div>
        </div>

        <div class="d-none" id="rw_settings_layout_bgs_group">
            <h5 class="margin-top-30">Backgrounds</h5>
            <div class="bg-examples  margin-bottom-20" id="rw_settings_layout_bgs">
                <div class="bg-gradient-1" data-toggle="tooltip" data-placement="top" title="bg-gradient-1"></div>
                <div class="bg-gradient-2" data-toggle="tooltip" data-placement="top" title="bg-gradient-2"></div>
                <div class="bg-gradient-3" data-toggle="tooltip" data-placement="top" title="bg-gradient-3"></div>
                <div class="bg-gradient-4" data-toggle="tooltip" data-placement="top" title="bg-gradient-4"></div>
                <div class="bg-gradient-5" data-toggle="tooltip" data-placement="top" title="bg-gradient-5"></div>
                <div class="bg-gradient-6" data-toggle="tooltip" data-placement="top" title="bg-gradient-6"></div>
                <div class="bg-gradient-7" data-toggle="tooltip" data-placement="top" title="bg-gradient-7"></div>
                <div class="bg-gradient-8" data-toggle="tooltip" data-placement="top" title="bg-gradient-8"></div>
                <div class="bg-gradient-9" data-toggle="tooltip" data-placement="top" title="bg-gradient-9"></div>
                <div class="bg-gradient-10" data-toggle="tooltip" data-placement="top" title="bg-gradient-10"></div>
            </div>
        </div>

        <div class="d-none" id="rw_settings_header_opt_group">

            <div class="divider divider--sm"></div>

            <h5 class="margin-top-30">Header options</h5>
            <div class="form-check">
               <input class="form-check-input" type="checkbox" id="rw_settings_header_fixed">
               <label class="form-check-label" for="rw_settings_header_fixed">Fixed header</label>
            </div>
            <div class="form-check margin-bottom-20">
               <input class="form-check-input" type="checkbox" id="rw_settings_header_invert">
               <label class="form-check-label" for="rw_settings_header_invert">Invert style</label>
            </div>
        </div>

        <div class="d-none" id="rw_settings_container_opt_group">
            <div class="divider divider--sm"></div>

            <h5 class="margin-top-30">Container</h5>
            <div class="form-check">
               <input class="form-check-input" type="checkbox" id="rw_settings_container_invert">
               <label class="form-check-label" for="rw_settings_container_invert">Invert style</label>
            </div>
        </div>

        <div class="d-none" id="rw_settings_navigation_opt_group">
            <div class="divider divider--sm"></div>

            <h5 class="margin-top-30">Navigation options</h5>
            <div class="form-check">
               <input class="form-check-input" type="checkbox" id="rw_settings_nav_minimized">
               <label class="form-check-label" for="rw_settings_nav_minimized">Minimized navigation</label>
            </div>
            <div class="form-check">
               <input class="form-check-input" type="checkbox" id="rw_settings_nav_hidden">
               <label class="form-check-label" for="rw_settings_nav_hidden">Hidden navigation</label>
            </div>
            <div class="form-check">
               <input class="form-check-input" type="checkbox" id="rw_settings_nav_fixed" disabled>
               <label class="form-check-label" for="rw_settings_nav_fixed">Fixed navigation</label>
            </div>
            <div class="form-check">
               <input class="form-check-input" type="checkbox" id="rw_settings_nav_invert">
               <label class="form-check-label" for="rw_settings_nav_invert">Invert style</label>
            </div>
            <div class="form-check">
               <input class="form-check-input" type="checkbox" id="rw_settings_nav_vmiddle">
               <label class="form-check-label" for="rw_settings_nav_vmiddle">Vertical middle position</label>
            </div>
            <div class="form-check">
               <input class="form-check-input" type="checkbox" id="rw_settings_nav_condensed">
               <label class="form-check-label" for="rw_settings_nav_condensed">Condensed navigation</label>
            </div>
            <div class="form-check">
               <input class="form-check-input" type="checkbox" id="rw_settings_nav_custom">
               <label class="form-check-label" for="rw_settings_nav_custom">Custom navigation</label>
            </div>
            <div class="form-check margin-bottom-20">
               <input class="form-check-input" type="checkbox" id="rw_settings_nav_cpanel">
               <label class="form-check-label" for="rw_settings_nav_cpanel">Remove control panel</label>
            </div>
        </div>

        <div class="d-none" id="rw_settings_sidepanel_opt_group">
            <div class="divider divider--sm"></div>

            <h5 class="margin-top-30">Sidepanel options</h5>
            <div class="form-check">
               <input class="form-check-input" type="checkbox" id="rw_settings_sidepanel_hidden">
               <label class="form-check-label" for="rw_settings_sidepanel_hidden">Hide sidepanel</label>
            </div>
            <div class="form-check margin-bottom-20">
               <input class="form-check-input" type="checkbox" id="rw_settings_sidepanel_invert">
               <label class="form-check-label" for="rw_settings_sidepanel_invert">Invert style</label>
            </div>
        </div>

            <div class="divider divider--sm"></div>

            <h5 class="margin-top-30">Content options</h5>
            <div class="form-check">
               <input class="form-check-input" type="checkbox" id="rw_settings_content_fluid">
               <label class="form-check-label" for="rw_settings_content_fluid">Fluid container</label>
            </div>
            <div class="form-check margin-bottom-20">
               <input class="form-check-input" type="checkbox" id="rw_settings_content_invert">
               <label class="form-check-label" for="rw_settings_content_invert">Invert content</label>
            </div>
    </div>
</div>


<!-- //END TEMPLATE SETTINGS -->
<!-- IMPORTANT SCRIPTS -->
<script type="text/javascript" src="<?= ($BASE) ?>/<?= ($ASSETS) ?>js/vendors/jquery/jquery.min.js"></script>
<script type="text/javascript" src="<?= ($BASE) ?>/<?= ($ASSETS) ?>js/vendors/jquery/jquery-migrate.min.js"></script>
<script type="text/javascript" src="<?= ($BASE) ?>/<?= ($ASSETS) ?>js/vendors/bootstrap/bootstrap.bundle.min.js"></script>

<script type="text/javascript" src="<?= ($BASE) ?>/<?= ($ASSETS) ?>js/vendors/mcustomscrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
<!-- END IMPORTANT SCRIPTS -->
<!-- THIS PAGE SCRIPTS ONLY -->
<!--Load page specifics -->
<?php if (isset($extra) && array_key_exists('js', $extra)): ?>
   
       <?php foreach (($extra['js']?:[]) as $item): ?>
           <script src="<?= ($BASE) ?>/<?= ($ASSETS) ?><?= ($item) ?>"></script>
       <?php endforeach; ?>
   
<?php endif; ?>

<!-- //END THIS PAGE SCRIPTS ONLY -->
<!-- TEMPLATE SCRIPTS -->
<script type="text/javascript" src="<?= ($BASE) ?>/<?= ($ASSETS) ?>js/app.js"></script>
<script type="text/javascript" src="<?= ($BASE) ?>/<?= ($ASSETS) ?>js/plugins.js"></script>
<script type="text/javascript" src="<?= ($BASE) ?>/<?= ($ASSETS) ?>js/demo.js"></script>
<script type="text/javascript" src="<?= ($BASE) ?>/<?= ($ASSETS) ?>js/settings.js"></script>
<!-- END TEMPLATE SCRIPTS -->
<!--My custom js-->
<script type="text/javascript" src="<?= ($BASE) ?>/<?= ($ASSETS) ?>js/btn.js"></script>
<script type="importmap">
   {
       "imports": {
           "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/42.0.2/ckeditor5.js",
           "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/42.0.2/"
       }
   }
   </script>
   <script type="module" src="<?= ($BASE) ?>/<?= ($ASSETS) ?>js/editor/main.js"></script>  

</body>
</html>