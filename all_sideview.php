<?php
if (txpinterface === 'admin') {
    register_callback('all_sideview_css', 'admin_side', 'head_end');
    register_callback('all_sideview_js', 'admin_side', 'head_end');
}

function all_sideview_css() {
  $plugin_css = <<<EOCSS
    body:has(#sideview) {
      padding-inline: 2em;
    }

    .txp-body:has(#sideview) {
      max-width: 100%;
      display: grid;
      gap: clamp(1em, 4%, 4rem);
      grid-template-columns: minmax(720px, 1280px) minmax(360px, 1280px);
    }

    #sideview {
      height: 100%;
      width: 100%;
      resize: both;
      overflow: auto;
      border: 0;
      outline: 1px solid var(--clr-brdr);
    }

    .sideview__button {
      .ui-icon:last-of-type {
        margin-left: -.125em;
        transform: rotateZ(0);
        margin-right: .375em;
      }
    }

    .sideview--active,
    .txp-body:has(#sideview) .sideview__button {
      .ui-icon:last-of-type {
        transform: rotateZ(180deg);
      }
    }
  EOCSS;

  if (class_exists('\Textpattern\UI\Style')) {
    echo Txp::get('\Textpattern\UI\Style')->setContent($plugin_css);
  } else {
    echo '<style>' . $plugin_css . '</style>';
  }
}

function all_sideview_js() {
  $plugin_js = <<<EOJS
    $(document).ready(function() {
      if($(window).width() < 1280) {
        exit;
      }

      if ($('#page-article').length) {
            const sectionExcluded = '';
            const sectionSelect = $('#section')[0];

            const articleSection = sectionSelect.value;
            var currentUrl = $('#article_partial_article_view').attr('href');

            if (articleSection != sectionExcluded && currentUrl) {
              sideView();
            } else {
              $('#sideview').remove();
            }

            $("form").on("change", function() {
              sideViewReloadIframe();
            });

            if (currentUrl) {
              sideViewAppendBtn();
            }

            textpattern.Relay.register("txpAsyncForm.success", sideViewAppendBtn);
            textpattern.Relay.register("txpAsyncForm.success", sideViewReloadIframe);
      }

      function sideViewAppendBtn() {
        $('.txp-save-zone .txp-actions').append('<a href="" title="View article alongside" class="sideview__button"><span class="ui-icon ui-icon-notice"></span> <span class="ui-icon ui-icon-copy"></span>Sideview</a>');
        $('.sideview__button').click(function() {
          if (localStorage.getItem('sideView') != 'true') {
            localStorage.setItem('sideView', 'true');
            $(this).addClass('sideview--active');
          } else {
            localStorage.setItem('sideView', 'false');
            $(this).removeClass('sideview--active');
          }
          sideView();
          return false;
        });
      }

      function sideViewReloadIframe() {
          if ($('#sideview').length) {
                document.getElementById('sideview').contentDocument.location.reload(true);
                console.log('form changed');
          }
      }

      function sideView() {
        if (localStorage.getItem('sideView') == 'true') {
          $('.txp-body').append('<iframe id="sideview" src="' + currentUrl + '"></iframe>');
        } else {
          $('#sideview').remove();
        }
      }

    });
EOJS;

    if (class_exists('\Textpattern\UI\Script')) {
        echo Txp::get('\Textpattern\UI\Script')->setContent($plugin_js);
    } else {
        echo '<script>' . $plugin_js . '</script>';
    }
}
