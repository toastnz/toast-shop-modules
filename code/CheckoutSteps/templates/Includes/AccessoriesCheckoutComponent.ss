<% include CheckoutComponentHeader %>

<% with $Order %>
    <% if $RelatedProducts %>
        <div class="related-products">
            <% loop $RelatedProducts %>
                <div class="related-products--item">
                    <% if $Image %>
                        <a href="{$Link}">
                            {$Image.Fit(180,200)}
                        </a>
                    <% end_if %>
                    <a href="{$Link}">{$Title}</a>
                    <p>
                        {$Price.Nice}
                        <a href="{$AddUrl}" class="js-add-to-cart" data-id="$ID">+</a>
                    </p>
                </div>
            <% end_loop %>
        </div>
    <% end_if %>
<% end_with %>
