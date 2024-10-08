
class VgInsertValues {
	constructor() {
		// init something
	}

	insertMultipleByPointer(editor, task, positionData) {
		// const strPositionData = JSON.stringify(positionData);
		const strPositionData = positionData.innerHTML;
		const position = editor.selection.getRng();
		const selectedContent = editor.selection.getContent();
		const startPos = position.startOffset;
		const endPos = position.endOffset;
		let newContent = '';
		if (task === 'pointer') {
			newContent = selectedContent + strPositionData;
		} else if (task === 'text_selected') {
			newContent = strPositionData;
		} else {
			alert(`Task ${task} not found.`);
			return;
		}
		editor.selection.setContent(newContent);
		editor.undoManager.add();
	}

	insertOneByShortCode(editor, data, task) {
		const shortCode = data.short_code;

		// if (shortCode.length === 0 || insertedShortCode) {
		if (shortCode.length === 0) {
			alert('Reload to insert again!');
			return;
		}

		let content = '';
		let shortCodeData = {};
		const bodyEditor = getEditorBody();
		const rawContent = data.raw_content;
		const allDataMapKeys = data.all_data_map_keys;

		for (const property in shortCode) {
			const data = allDataMapKeys[shortCode[property]];
			const a = '{' + shortCode[property] + '}';
			if (allDataMapKeys[shortCode[property]]) {
				const colVal = data.val;
				const colName = data.key;
				if (shortCode[property].includes('link')) {
					const link = `${colName}: <a style="color: #007CD2FF" href="${colVal}">${colVal}</a>`;
					if (task === 'insert_one_by_shortcode_value') {
						var strLink = `${colVal}`;
					} else {
						strLink = link;
					}
					// const strLink = `<p>${colVal}</p>`;
					content += link;
					shortCodeData[a] = strLink;
				} else {
					// const strContent = `<p>${colVal}</p>`;
					if (task === 'insert_one_by_shortcode_value') {
						var strContent = `${colVal}`;
					} else {
						if (!colName) {
							strContent = `${colVal}`;
						} else {
							strContent = `${colName}: ${colVal}`;
						}
					}
					const htmlContent = `<p>${colName}: ${colVal}</p>`;
					content += htmlContent;
					shortCodeData[a] = strContent;
				}
			} else {
				content += '<p>{' + shortCode[property] + '}</p>';
				// shortCodeData[a] = '<p>{' + shortCode[property] + '}</p>';
				shortCodeData[a] = '{' + shortCode[property] + '}';
			}
		}
		let textHandling = new vgTextHandling();
		// const newContent = textHandling.replaceMultiple(rawShortCode, shortCodeData);
		const newContent = textHandling.replacePlaceholders(rawContent, shortCodeData);
		if (!bodyEditor) return;
		// bodyEditor.innerHTML = content;
		bodyEditor.innerHTML = newContent;
		// insertedShortCode = true;
	}
}