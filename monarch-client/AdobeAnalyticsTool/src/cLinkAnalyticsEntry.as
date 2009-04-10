/**
 * 
 * 
 * 
 * 
 */

package
{
	public class cLinkAnalyticsEntry extends cKeywordEntry
	{
		import mx.collections.ArrayCollection;
		
		
		public function cLinkAnalyticsEntry(id:int,
											name:String,
											timeCollection:ArrayCollection,
											countCollection:ArrayCollection,
											goodnessCollection:ArrayCollection,
											englishProficiencyCollection:ArrayCollection)
		{
			super(id, name);
				
			mTimeCollection = timeCollection;
			mCountCollection = countCollection;
			mGoodnessCollection = goodnessCollection;
			mEnglishProficiencyCollection = englishProficiencyCollection;	
		}

		/** collection of time data **/
		public var mTimeCollection:ArrayCollection;
		/** collection of count data **/
		public var mCountCollection:ArrayCollection;
		/** collection of goodness data **/
		public var mGoodnessCollection:ArrayCollection;
		/** collection of english proficiency data **/
		public var mEnglishProficiencyCollection:ArrayCollection;
	}
}