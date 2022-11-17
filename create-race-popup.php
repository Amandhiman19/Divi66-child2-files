<?php
$users = getUnassociatedMembers($id, $associate_team);
$title = 'race-' . $associate_team . '-' . $id;
$post_id = post_exists($title, '', '', 'racecircuit', 'publish');

$racePosition = [
    'avant_tribord' => ['label' => 'Avant tribord', 'value' => []],
    'avant_babord' => ['label' => 'Avant babord', 'value' => []],
    'arriere_tribord' => ['label' => 'Arrière tribord', 'value' => []],
    'arriere_babord' => ['label' => 'Arrière babord', 'value' => []],
    'barreur' => ['label' => 'Barreur', 'value' => []],
];

if ($post_id) {
    foreach ($racePosition as $key => $value) {
        $getField = get_field($key, $post_id);
        if ($getField) {
            $racePosition[$key]['value']['id'] = $getField['ID'];
            $racePosition[$key]['value']['name'] = $getField['user_firstname'] . ' ' . $getField['user_lastname'];
        }
        $user = [];
        $user['ID'] = $getField['ID'];
        $user['first_name'] = $getField['user_firstname'];
        $user['last_name'] = $getField['user_lastname'];

        array_push($users, (object)$user);
    }
}

?>
<tr id="race-<?php echo $id; ?>" class="race-form-fields" style="display:none;max-width:700px;z-index:9">
    <td class="race-details" colspan="100">
        <h3 class="mb-10 text-center"><?php the_title(); ?></h3>
        <form method="POST" id="register-race" autocomplete="off" class="register-race">

            <input type="hidden" name="race_type" value="<?php echo $id; ?>">
            <input type="hidden" name="action" value="greenplay_register_race">
            <input type="hidden" name="associate_team" value="<?php echo $associate_team; ?>">
            <div class="table-race">
                <table>
                    <thead>
                        <tr>
                            <td class="position"><?php echo esc_html__("Position", "divi-child"); ?></td>
                            <td class="canoers"><?php echo esc_html__("Canoers", "divi-child"); ?></td>
                            <td class="captain"><?php echo esc_html__("Captain", "divi-child"); ?></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($racePosition as $key => $position) { ?>
                            <tr>
                                <td class="title"><?php echo $position['label']; ?></td>
                                <td class="users dropdown">
                                    <span class="select-user-button"><?php echo !empty($position['value']) ? $position['value']['name'] : esc_html__("Select User", "divi-child"); ?></span>
                                    <div class="select-box-list hidden">
                                        <span class="select-user-hide" title="close popup"><b>X</b></span>
                                        <input type="text" class="js-filter-input" placeholder="<?php echo esc_html__("Search user", "divi-child"); ?>" name="name-<?php echo $key; ?>" class="users-dropdown" onPaste="return false" />
                                        <ul>
                                            <?php
                                            foreach ($users as $user) {
                                                echo '<li data-value="' . $user->ID . '">' . $user->first_name . ' ' . $user->last_name . '</li>';
                                            } ?>

                                        </ul>
                                    </div>
                                    <select class="users-dropdown hidden" name="<?php echo $key; ?>" id="<?php echo $key; ?>">
                                        <option value="">Select User</option>
                                        <?php
                                        foreach ($users as $user) {
                                            $selected = !empty($position['value']) ? $position['value']['id'] == $user->ID ? 'selected' : '' : '';
                                            echo '<option value="' . $user->ID . '" ' .  $selected . '>' . $user->first_name . ' ' . $user->last_name . '</option>';
                                        } ?>
                                    </select>
                                </td>
                                <td class="captain">
                                    <label>
                                        <input type="radio" name="race_captain" class="mepr-form-radio" value="<?php echo $key; ?>">
                                        <span class="mepr-payment-method-label-text"></span>
                                    </label>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <script type="module">
                jQuery(".select-user-button").click(function() {
                    jQuery(this).next().show()
                });
                jQuery(".select-user-hide").click(function() {
                    jQuery('.select-box-list').hide()
                });

                jQuery(document).on('input', '.js-filter-input', function() {
                    var inpVal = this.value.toLowerCase();
                    var reg = new RegExp(inpVal, "i");
                    var selector = $(this).next().children('li');
                    if (inpVal) {
                        selector.hide().filter(function() {
                            return $(this).text().toLowerCase().match(reg);
                        }).show();
                    } else {
                        selector.show();
                    }
                });

                // on list select								
                jQuery('#race-<?php echo $id; ?> .users.dropdown ul').on('click', 'li', function(e) {
                    e.preventDefault();
                    const value = jQuery(this).attr('data-value');
                    const name = jQuery(this).html();
                    const classname = 'selected-usr-btn';
                    jQuery('#race-<?php echo $id; ?> .users.dropdown .select-box-list').hide();
                    jQuery(this).parent().parent().parent().children('.select-user-button').html(name);
                    jQuery(this).parent().parent().parent().children('.select-user-button').addClass(classname);
                    jQuery(this).parent().parent().parent().children('select.users-dropdown').val(value);
                    changeUsersSelect();
                });

                const users = <?php echo json_encode($users); ?>;
                const selectLists = document.querySelectorAll('#race-<?php echo $id; ?> .users-dropdown');
                let values = {};


                selectLists.forEach((lists) => {
                    jQuery(lists).on('change', function() {
                        changeUsersSelect();
                    });
                    values[lists.name] = users;
                });

                function changeUsersSelect() {
                    let selectedValue = [];
                    selectLists.forEach((lists) => {
                        if (lists.value) {
                            selectedValue.push(lists.value);
                        } else {
                            const filteredArray = users.filter(user => !selectedValue.includes(user.ID.toString()));

                            addSelectedOption(filteredArray, lists.name);
                        }
                    });
                }

                function addSelectedOption(datas = [], selector) {
                    let optionsData = '<option value="">Select User</option>';
                    if (datas.length > 0) {
                        datas.forEach(data => {
                            optionsData += "<option value='" + data.ID + "'>" + data.first_name + " " + data.last_name + "</option>"
                        })
                    }

                    let ulData = ''
                    if (datas.length > 0) {
                        datas.forEach(data => {
                            ulData += "<li data-value='" + data.ID + "'>" + data.first_name + " " + data.last_name + "</li>";
                        })
                    }

                    jQuery('#race-<?php echo $id; ?> select[name="' + selector + '"]').html(optionsData);
                    jQuery('#race-<?php echo $id; ?> ul').html(ulData);
                }
            </script>

            <input type="submit" value="<?php echo esc_html__('Confirm', 'divi-child'); ?>">
            <img src=" <?php echo get_home_url(); ?>/wp-admin/images/loading.gif" alt="Loading..." style="display: none;" class="mepr-loading-gif is-loading" title="Loading icon">
            <div class="message"></div>
        </form>
    </td>
    <?php
    include get_stylesheet_directory() . '/my-account/subscription/payment.php';
    ?>
</tr>