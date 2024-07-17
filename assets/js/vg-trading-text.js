class vgTextHandling {
	constructor() {
		// todo
	}

	extractStrings(input) {
		// Use a regular expression to match all strings inside curly braces
		const regex = /{([^}]+)}/g;
		let matches = [];
		let match;

		// Use a while loop to find all matches
		while ((match = regex.exec(input)) !== null) {
			matches.push(match[1]);
		}
		return matches;
	}

	replaceMultiple(str, replacements) {
		console.log(str);
		for (let [oldStr, newStr] of Object.entries(replacements)) {
			console.log(str.split(oldStr));
			str = str.split(oldStr).join(newStr);
		}
		return str;
	}

	replacePlaceholders(htmlString, mapKeys) {
		// Create a regular expression to match all the keys in the mapKeys object
		const regex = new RegExp(Object.keys(mapKeys).map(key => key.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')).join('|'), 'g');
		// Replace each placeholder with the corresponding value from mapKeys
		const result = htmlString.replace(regex, function (matched) {
			return mapKeys[matched];
		});

		return result;
	}

	runTest() {
		console.log('Loaded successfully!');
	}

}