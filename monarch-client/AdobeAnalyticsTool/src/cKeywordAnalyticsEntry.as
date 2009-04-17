/**
 * 
 * 
 * 
 */

package
{
	import mx.collections.ArrayCollection;
	
	/**
	 * 
	 */
	public class cKeywordAnalyticsEntry extends cKeywordEntry
	{
		public function cKeywordAnalyticsEntry(id:int,
											   name:String,
											   color:uint,
											   timeCollection:ArrayCollection,
											   countCollection:ArrayCollection,
											   goodnessCollection:ArrayCollection,
											   englishProficiencyCollection:ArrayCollection)
		{
			super(id, name, color, 0);
				
			mTimeCollection = timeCollection;
			mCountCollection = countCollection;
			mGoodnessCollection = goodnessCollection;
			mEnglishProficiencyCollection = englishProficiencyCollection;	
		}
		
		
		///////////////////////////////////////////////////////////////////////////////////
		//
		//						   DATA MEMBERS
		//
		///////////////////////////////////////////////////////////////////////////////////

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