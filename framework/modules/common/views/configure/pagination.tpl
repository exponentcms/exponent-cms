<h2>{"Pagination"|gettext}</h2>
{control type=text name=limit label="Items per page"|gettext value=$config.limit}
{control type=dropdown name=pagelinks label="Show page links" items="Top and Bottom,Top Only,Bottom Only,Don't show page links" checked=$config.pagelinks}
{control type="checkbox" name="multipageonly" label="Disable page links unless more than one page" value=1 checked=$config.multipageonly}
