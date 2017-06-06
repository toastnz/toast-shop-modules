<ul class="steps">
    <% loop $Steps %>
        <li data-api-url="{$Link}" data-pos="{$Pos}">{$Pos} {$Title} <% if $IsCurrent %>(active)<% end_if %></li>
    <% end_loop %>
</ul>