<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 8/4/17
 * Time: 8:47 PM
 */


namespace View;

class AdminLTE
{

    public static function direct_messages(string $from)
    { ?>
        <!-- DIRECT CHAT SUCCESS -->
        <div id="hierarchical" class="box box-success direct-chat direct-chat-success"></div>

        <!--/.direct-chat -->
        <script>

            function Messages() {
                $.get('<?= SITE . 'Messages/' . $from ?>/', function (data) {
                    MustacheWidgets(data, {
                        widget: $('#hierarchical'),
                        scroll: '#messages'
                    })
                }, "json");

                $(document).on('submit', 'form#data-hierarchical', function (event) {
                    $("#data-hierarchical").ajaxSubmit({
                        url: '<?= SITE . 'Messages/' . $from ?>/',
                        type: 'post',
                        dataType: 'json',
                        success: function (data) {
                            console.log(data);
                            MustacheWidgets(data, {
                                widget: $('#hierarchical'),
                                scroll: '#messages'
                            });
                            return false;
                        }
                    });
                    event.preventDefault();
                });
            }
            Messages();


        </script>

        <?php
    }

}


