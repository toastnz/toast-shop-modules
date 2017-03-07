<div class="innerWrap">

    <div id="Product" class="typography">
        <h1 class="pageTitle">$Title</h1>
        <div class="breadcrumbs">$Breadcrumbs</div>
        <div class="productDetails">
            <% if $Image.ContentImage %>
                <img class="productImage" src="$Image.ContentImage.URL" alt="<%t Product.ImageAltText "{Title} image" Title=$Title %>"/>
            <% else %>
                <div class="noimage"><%t Product.NoImage "no image" %></div>
            <% end_if %>
            <% if $InternalItemID %>
                <p class="InternalItemID">
                    <span class="title"><%t Product.Code "Product Code" %>:</span>
                    <span class="value">{$InternalItemID}</span>
                </p>
            <% end_if %>
            <% if $Model %>
                <p class="Model">
                    <span class="title"><%t Product.Model "Model" %>:</span>
                    <span class="value">$Model.XML</span>
                </p>
            <% end_if %>
            <% if $Size %>
                <p class="Size">
                    <span class="title"><%t Product.Size "Size" %>:</span>
                    <span class="value">$Size.XML</span>
                </p>
            <% end_if %>
            <% if $PriceRange %>
                <div class="price">
                    <strong class="value">$PriceRange.Min.Nice</strong>
                    <% if $PriceRange.HasRange %>
                        - <strong class="value">$PriceRange.Max.Nice</strong>
                    <% end_if %>
                    <span class="currency">$Price.Currency</span>
                </div>
            <% else %>
                <% if $Price %>
                    <div class="price">
                        <strong class="value">$Price.Nice</strong> <span class="currency">$Price.Currency</span>
                    </div>
                <% end_if %>
            <% end_if %>
            <% if $IsInCart %>
                <p class="NumItemsInCart">
                    <% if $Item.Quantity == 1 %>
                        <%t Product.NumItemsInCartSingular "You have this item in your cart" %>
                    <% else %>
                        <%t Product.NumItemsInCartPlural "You have {Quantity} items in your cart" Quantity=$Item.Quantity %>
                    <% end_if %>
                </p>
            <% end_if %>
            $Form
        </div>
        <% if $Content %>
            <div class="productContent typography">
                $Content
            </div>
        <% end_if %>
    </div>
    <aside>
        <% include SideBar %>
    </aside>
</div>
