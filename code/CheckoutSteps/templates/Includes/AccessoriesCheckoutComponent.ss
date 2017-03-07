<% include CheckoutComponentHeader %>

<% with $Order %>
    <% if $RelatedProducts %>
        <ul>
            <% loop $RelatedProducts %>
                <li><a href="{$Link}">{$Title}</a></li>
            <% end_loop %>
        </ul>
    <% end_if %>
<% end_with %>
