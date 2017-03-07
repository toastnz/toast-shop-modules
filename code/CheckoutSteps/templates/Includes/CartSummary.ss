<% if $Items %>
    <div class="cart-summary">
        <h3>Summary</h3>
        <% loop $Items %>
            <div class="cart-summary__item $FirstLast">
                <p>$TableTitle</p>
                <% if $Image %>
                    {$Image.Fit(100,100)}
                <% end_if %>
                <div class="wrap">
                    <input class="quantity" value="$Quantity">
                    <div class="total">$Total.Nice</div>
                    <a href="$getSetQuantity" class="js-augment augment" data-id="$ID">Update</a>
                </div>
                <div class="clearfix"></div>
            </div>
        <% end_loop %>
        <hr>

        <div class="totals">
            <div class="subtotal">
                <p>
                    <%t Order.SubTotal "Sub-total" %>
                    <span>{$SubTotal.Nice}</span>
                </p>
            </div>
            <% if $ShowSubtotals %>
                <% if $Modifiers %>
                    <% loop $Modifiers %>
                        <% if $ShowInTable %>
                            <div class="subtotal {$Classes}">
                                <p>
                                    <% if $Link %>
                                        <a href="$Link" title="<%t Shop.ReadMoreTitle "Click here to read more on &quot;{Title}&quot;" Title=$TableTitle %>">$TableTitle</a>
                                    <% else %>
                                        {$TableTitle}
                                    <% end_if %>
                                    <span>{$TableValue.Nice}</span>
                                </p>
                            </div>
                        <% end_if %>
                    <% end_loop %>
                <% end_if %>

                <div class="total">
                    <p>
                        <%t Order.Total "Total" %>
                        <span>{$Total.Nice}{$Currency}</span>
                    </p>
                </div>
            <% end_if %>
        </div>
    </div>
<% else %>
    <p class="message warning">
        <%t ShoppingCart.NoItems "There are no items in your cart." %>
    </p>
<% end_if %>
