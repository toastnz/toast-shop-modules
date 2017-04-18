<% if $DisplayModal %>
    <div class="popup__overlay wysiwyg">
        <div class="popup__content">
            <% with $SiteConfig %>
                <div class="popup__content__header" style="background-image:url('{$SignUpHeader.Fill(1640,420).URL}');">
                    <% if $Heading %>
                        <h2>{$Heading}</h2>
                    <% else %>
                        <h2>REGISTER AND SAVE</h2>
                    <% end_if %>
                    <a href="#" class="close [ js-close-popup ]">$SVG('close')</a>
                </div>
            <% end_with %>

            <div class="popup__content__details">
                <% with $SiteConfig %>
                    <% if $SignUpModalText %>
                        $SignUpModalText
                    <% else %>
                        <p>Register now and receive <strong>10% off</strong> you next purchase</p>
                        <hr>
                    <% end_if %>
                <% end_with %>
                $SignUpForm
            </div>
        </div>
    </div>
<% end_if %>
