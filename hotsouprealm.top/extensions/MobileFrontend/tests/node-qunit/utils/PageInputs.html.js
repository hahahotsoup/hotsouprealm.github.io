/* eslint-env es6 */
const referencesPage = `<div id="mfe-test-references">
<sup id="cite_ref-1" class="reference">
	<a href="#cite_note-1">[1]</a>
</sup>
<p>
	sup with encoded href attribute
	<sup id="cite_ref-Obama_1995,_2004,_pp._9–10_11-0" class="reference">
		<a href="#cite_note-Obama_1995,_2004,_pp._9%E2%80%9310-11">[11]</a>
	</sup>
</p>
<ol class="references">
	<li id="cite_note-1">
		<span class="mw-cite-backlink">
			<a href="#cite_ref-1">↑</a>
		</span> <span class="reference-text">hello</span>
	</li>
	<li id="cite_note-Obama_1995,_2004,_pp._9–10-11">
		<span class="mw-cite-backlink">
			<a href="#cite_ref-1">↑</a>
		</span> <span class="reference-text">found</span>
	</li>
	<li id="cite_note-2">
		<span class="mw-cite-backlink">
			<a href="#cite_ref-1">^</a>
		</span>
		<span class="reference-text">
			<a class="external text" href="">Hi there</a>
		</span>
	</li>
</ol>
</div>`;

module.exports = {
	// A page with references mirroring PHP Parser output
	referencesPage: referencesPage
};
