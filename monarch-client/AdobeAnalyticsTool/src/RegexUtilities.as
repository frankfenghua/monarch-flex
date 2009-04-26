// ActionScript file
package {
	public class RegexUtilities {
		
		// This is needed unless we support dynamic loading of regular expression types from the database. At this point
		// that doesn't look likely
		static public var ENG_TO_FIELDNAME:Object = {"next page of threads":"nextPageOfThreads", "number of views for thread":"threadNumViews", "thread url":"threadUrl", 
												"next page of posts":"nextPageOfPosts", "thread title":"threadTitle", "first post author":"firstPostAuthor", "first post author URL":"firstPostAuthorUrl",
												  "first post time":"firstPostTime", "first post message":"firstPostMessage", "reply author":"replyAuthor", "reply author URL":"replyAuthorUrl", "reply time":"replyTime",
												  "reply message":"replyMessage"};
												  
		static public var FIELDNAME_TO_ENG:Object = {"nextPageOfThreads":"next page of threads", "threadNumViews":"number of views for thread", "threadUrl":"thread url", 
												"nextPageOfPosts":"next page of posts", "threadTitle":"thread title", "firstPostAuthor":"first post author", "firstPostAuthorUrl":"first post author URL",
												  "firstPostTime":"first post time", "firstPostMessage":"first post message", "replyAuthor":"reply author", "replyAuthorUrl":"reply author URL", "replyTime":"reply time",
												  "replyMessage":"reply message"};
												  
		static public function englishToFieldname(englishName:String):String {
			return ENG_TO_FIELDNAME[englishName];
		}

		static public function fieldnameToEnglish(fieldName:String):String {
			return FIELDNAME_TO_ENG[fieldName];
		}
		
		static public function getAllRegexFieldnames():Array {
			return new Array("nextPageOfThreads", "threadNumViews", "threadUrl", 
												"nextPageOfPosts", "threadTitle", "firstPostAuthor", "firstPostAuthorUrl",
												  "firstPostTime", "firstPostMessage", "replyAuthor", "replyAuthorUrl", "replyTime",
												  "replyMessage");
		}
		
		static public function perlSyntaxToNative(perlRegex:String):RegExp {
			// The modifiers for the ActionScript regular expression
			var modifiers:String = "g";
			
			if(perlRegex.indexOf("/") != 0) {
				trace("No opening slash");
				return null;
			}
			// Find closing '/'
			var closingIndex:int = perlRegex.lastIndexOf("/");
			if(closingIndex <= 0) {
				trace("No closing slash");
				return null;
			}
			
			// Look for modifier at the end of the Regular Expression
			var modifierSyntax:RegExp = /[imsx]*/;
			var result:Object = perlRegex.substr(closingIndex+1).match(modifierSyntax);
			if(!result || result[0] != perlRegex.substr(closingIndex+1)) {
				return null;
			}
			
			modifiers += result[0];
		
			var asRegexString:String = perlRegex.substring(1, closingIndex);
			
			// Look for unescaped '/'
			var invalidSlash:RegExp = /[^\\]\//;
			if(asRegexString.match(invalidSlash) != null) {
				trace("Unescaped slash");
				return null;
			}
		
			// TODO: translate unsupported Perl features
		
			return new RegExp(asRegexString, modifiers); 
		}
	}
}
