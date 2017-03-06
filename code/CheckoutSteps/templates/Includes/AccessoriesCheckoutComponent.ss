<% include CheckoutComponentHeader %>

<% if $Category %>
    <% with $Category %>
        <ul>
            <% loop $ProductsShowable %>
                <li><a href="{$Link}">{$Title}</a></li>
            <% end_loop %>
        </ul>
    <% end_with %>
<% end_if %>
