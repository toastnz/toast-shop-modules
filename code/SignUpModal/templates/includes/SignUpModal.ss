<% if $DisplayModal %>
    <div id="signUp_modal">
        <div class="innerWrap">
            <div class="js-close-me" onClick="hideModal();"></div>
            <div class="innerWrapContent">
                <% with $SiteConfig %>
                    $SignUpModalText
                <% end_with %>

                $SignUpForm
            </div>
        </div>
    </div>
<% end_if %>
