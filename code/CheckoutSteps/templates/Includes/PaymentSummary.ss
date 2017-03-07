<% if $Items %>
    <table class="cart" summary="<%t ShoppingCart.TableSummary "Current contents of your cart." %>">
        <colgroup>
            <col class="product title"/>
            <col class="quantity"/>
            <col class="total"/>
        </colgroup>
        <thead>
        <tr>
            <th scope="col"><%t Product.SINGULARNAME "Product" %></th>
            <th scope="col"><%t Order.Quantity "Quantity" %></th>
            <th scope="col"><%t Order.TotalPriceWithCurrency "Total Price ({Currency})" Currency=$Currency %></th>
        </tr>
        </thead>
        <tbody>
            <% loop $Items %>
                <% if $ShowInTable %>
                <tr id="$TableID" class="$Classes $EvenOdd $FirstLast">
                    <td id="$TableTitleID">
                        <h3>
                            <% if $Link %>
                                <a href="$Link" title="<%t Shop.ReadMoreTitle "Click here to read more on &quot;{Title}&quot;" Title=$TableTitle %>">$TableTitle</a>
                            <% else %>
                                $TableTitle
                            <% end_if %>
                        </h3>
                        <% if $SubTitle %><p class="subtitle">$SubTitle</p><% end_if %>
                        <% if $Product.Variations && $Up.Editable %>
                            <%t Shop.Change "Change" %>: $VariationField
                        <% end_if %>
                    </td>
                    <td><% if $Up.Editable %>$QuantityField<% else %>$Quantity<% end_if %></td>
                    <td id="$TableTotalID">$Total.Nice</td>
                </tr>
                <% end_if %>
            <% end_loop %>
        </tbody>
        <tfoot>
        <tr class="subtotal">
            <th colspan="2" scope="row"><%t Order.SubTotal "Sub-total" %></th>
            <td id="$TableSubTotalID">$SubTotal.Nice</td>
            <% if $Editable %>
                <td>&nbsp;</td><% end_if %>
        </tr>
            <% if $ShowSubtotals %>
                <% if $Modifiers %>
                    <% loop $Modifiers %>
                        <% if $ShowInTable %>
                        <tr id="$TableID" class="$Classes">
                            <th id="$TableTitleID" colspan="2" scope="row">
                                <% if $Link %>
                                    <a href="$Link" title="<%t Shop.ReadMoreTitle "Click here to read more on &quot;{Title}&quot;" Title=$TableTitle %>">$TableTitle</a>
                                <% else %>
                                    $TableTitle
                                <% end_if %>
                            </th>
                            <td id="$TableTotalID">$TableValue.Nice</td>
                        </tr>

                        <% end_if %>
                    <% end_loop %>
                <% end_if %>
            <tr class="gap Total">
                <th colspan="2" scope="row"><%t Order.Total "Total" %></th>
                <td id="$TableTotalID"><span class="value">$Total.Nice</span> <span class="currency">$Currency</span>
                </td>
            </tr>
            <% end_if %>
        </tfoot>
    </table>
<% else %>
    <p class="message warning">
        <%t ShoppingCart.NoItems "There are no items in your cart." %>
    </p>
<% end_if %>
