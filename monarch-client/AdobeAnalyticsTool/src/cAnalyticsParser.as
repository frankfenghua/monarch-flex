/**
 * 
 * 
 * 
 * 
 */

package
{
	public class cAnalyticsParser
	{
		import mx.collections.ArrayCollection;
		import mx.collections.XMLListCollection;
		import mx.controls.Alert;
		
		//////////////////////////////////////////////////////////////////////////////////////
		//
		//							CONSTRUCTOR
		//
		////////////////////////////////////////////////////////////////////////////////////// 		
		
		/**
		 * 
		 */
		public function cAnalyticsParser(xmlTree:XML):void
		{						
			var  analysisInformationList:XMLListCollection = new XMLListCollection(xmlTree.elements("Information"));
			if(analysisInformationList.children().length() > 0) {    				
				var communityId:int = int(analysisInformationList.elements("communityId"));
				var websiteName:String = analysisInformationList.elements("websiteName").toString();
				
				mWebsiteName = websiteName;
				mCommGroupID = communityId;
				
				var  keywordsList:XMLListCollection = new XMLListCollection(xmlTree.elements("Keywords"));
				var i:int, j:int, k:int;
				for(i = 0; i < keywordsList.children().length(); i++)
				{		
					var keywordList:XMLListCollection = new XMLListCollection(keywordsList.elements("Keyword" + i));
					
					for(j = 0; j < keywordList.children().length(); j=+2)
					{		
						var keywordInformationList:XMLListCollection = new XMLListCollection(keywordList.elements("KeywordInformation"));
						var keywordName:String = keywordInformationList.elements("keywordName");	
						
						var dataList:XMLListCollection = new XMLListCollection(keywordList.elements("Data"));
						
						var timeCollection:ArrayCollection = new ArrayCollection();
						var countCollection:ArrayCollection = new ArrayCollection();
						var goodnessCollection:ArrayCollection = new ArrayCollection();
						var englishProficiencyCollection:ArrayCollection = new ArrayCollection();
						
						for(k = 0; k < dataList.children().length(); k++)
						{
							var statInformationList:XMLListCollection = new XMLListCollection(dataList.elements("Stat" + k));
						
							var time:int = int(statInformationList.elements("time"));
							var count:int = int(statInformationList.elements("count"));
							var goodness:Number = Number(statInformationList.elements("goodness"));
							var englishProficiency:Number = Number(statInformationList.elements("englishProficiency"));
							
							// store the metrics in their appropriate collections
							timeCollection.addItem(time);
							countCollection.addItem(count);
							goodnessCollection.addItem(goodness);
							englishProficiencyCollection.addItem(englishProficiency);
						}
						// generate the color for the keyword
						var color:uint = 0xFFFFFF * Math.random();
						
						var keywordAnalyticsEntry:cKeywordAnalyticsEntry = new cKeywordAnalyticsEntry(0,
																									  keywordName,
																									  color,
																									  timeCollection,
																									  countCollection,
																									  goodnessCollection,
																									  englishProficiencyCollection);
						// store the keyword in the collection																		  
						mKeywordData.addItem(keywordAnalyticsEntry);
						
					} // end for( j ...		
				} // end for( i ...
				
				/*var  linksList:XMLListCollection = new XMLListCollection(xmlTree.elements("Links"));
				for(i = 0; i < linksList.children().length(); i++)
				{		
					var linkList:XMLListCollection = new XMLListCollection(linksList.elements("Link" + i));
					
					for(j = 0; j < linkList.children().length(); j=+2)
					{		
						var linkInformationList:XMLListCollection = new XMLListCollection(linkList.elements("LinkInformation"));
						var linkName:String = linkInformationList.elements("linkName");	
						
						var linkDataList:XMLListCollection = new XMLListCollection(linkList.elements("Data"));
						
						var linkTimeCollection:ArrayCollection = new ArrayCollection();
						var linkCountCollection:ArrayCollection = new ArrayCollection();
						var linkGoodnessCollection:ArrayCollection = new ArrayCollection();
						var linkEnglishProficiencyCollection:ArrayCollection = new ArrayCollection();
						
						for(k = 0; k < linkDataList.children().length(); k++)
						{
							var linkStatInformationList:XMLListCollection = new XMLListCollection(linkDataList.elements("Stat" + k));
						
							var _time:int = int(linkStatInformationList.elements("time"));
							var _count:int = int(linkStatInformationList.elements("count"));
							var _goodness:Number = Number(linkStatInformationList.elements("goodness"));
							var _englishProficiency:Number = Number(linkStatInformationList.elements("englishProficiency"));
							
							// store the metrics in their appropriate collections
							linkTimeCollection.addItem(_time);
							linkCountCollection.addItem(_count);
							linkGoodnessCollection.addItem(_goodness);
							linkEnglishProficiencyCollection.addItem(_englishProficiency);
						}
						var linkAnalyticsEntry:cLinkAnalyticsEntry = new cLinkAnalyticsEntry(0,
																							 linkName,
																							 linkTimeCollection,
																							 linkCountCollection,
																							 linkGoodnessCollection,
																							 linkEnglishProficiencyCollection);
						// store the keyword in the collection																		  
						mLinksData.addItem(linkAnalyticsEntry);
					}
				}*/
			
				// now that we have gathered the keywords and links we must sub catagorize them by day.						
				var countVal:Number = 0;
				var goodnessVal:Number = 0;
				var englishProficiencyVal:Number = 0;
				var prevDate:String = "";
				var counter:int;
				for(i = 0; i < mKeywordData.length; i++) {
					
					var keyword:cKeywordAnalyticsEntry = cKeywordAnalyticsEntry(mKeywordData.getItemAt(i));
					var _timeCollection:ArrayCollection = new ArrayCollection();
					var _countCollection:ArrayCollection = new ArrayCollection();
					var _goodnessCollection:ArrayCollection = new ArrayCollection();
					var _englishProficiencyCollection:ArrayCollection = new ArrayCollection();
					counter = 0;
					
					for(j = 0; j < keyword.mTimeCollection.length; j++) {
						var timeStamp:Number = (int(keyword.mTimeCollection.getItemAt(j)));
						var date:Date = new Date(int(timeStamp)*1000); 
						var currentDate:String = (date.getMonth() + 1) + "/" + date.getDate() + "/" + date.getFullYear(); 
						        
					        if(prevDate == "") {
					        	prevDate = currentDate
					        	countVal = keyword.mCountCollection[j];
					        	goodnessVal = keyword.mGoodnessCollection[j];
					        	englishProficiencyVal = keyword.mEnglishProficiencyCollection[j];
					        	counter++;
					        }
					        else if(prevDate == currentDate) {    	
					        	countVal = countVal + keyword.mCountCollection[j];
					        	goodnessVal =+ keyword.mGoodnessCollection[j];
					        	englishProficiencyVal =+ keyword.mEnglishProficiencyCollection[j];
					        	counter++;
					        }	
					        else {        	
					        	countVal = countVal;
					        	goodnessVal = goodnessVal;
					        	englishProficiencyVal = englishProficiencyVal;			     
					        	
					        	_timeCollection.addItem(prevDate);
					        	_countCollection.addItem(countVal);
					        	_goodnessCollection.addItem(goodnessVal);
					        	_englishProficiencyCollection.addItem(englishProficiencyVal/counter);
					        	
					        	prevDate = currentDate;
					        	
					        	countVal = keyword.mCountCollection[j];
					        	goodnessVal = keyword.mGoodnessCollection[j];
					        	englishProficiencyVal = keyword.mEnglishProficiencyCollection[j];
					        	counter = 1;
					        } 				
					}
					
					if(prevDate != "") {
						//countVal = keyword.mCountCollection[j];
			        	//goodnessVal = keyword.mGoodnessCollection[j];
			        	//englishProficiencyVal = keyword.mEnglishProficiencyCollection[j];
			        	
			        	_timeCollection.addItem(prevDate);
			        	_countCollection.addItem(countVal);
			        	_goodnessCollection.addItem(goodnessVal);
			        	_englishProficiencyCollection.addItem(englishProficiencyVal/counter);
			        	var _keywordAnalyticsEntry:cKeywordAnalyticsEntry = new cKeywordAnalyticsEntry(0,
																								 		keyword.mName,
																								 		keyword.mColor,
																								 		_timeCollection,
																								 		_countCollection,
																								 		_goodnessCollection,
																								 		_englishProficiencyCollection);
						mSortedData.addItem(_keywordAnalyticsEntry);
					}
					
					
				}
				
				
			} // end if
		} // end function
		
		
		//////////////////////////////////////////////////////////////////////////////////////
		//
		//							MEMBER FUNCTIONS
		//
		////////////////////////////////////////////////////////////////////////////////////// 
		
		/**
		 *  Returns the number of days in a particular month.
		 */
		private function daysInMonth(month:Number):Number {
			if(month == 0) { return 31; }
			else if(month == 1) { return 28; }
			else if(month == 2) { return 31; }
			else if(month == 3) { return 30; }
			else if(month == 4) { return 31; }
			else if(month == 5) { return 30; }
			else if(month == 6) { return 31; }
			else if(month == 7) { return 31; }
			else if(month == 8) { return 30; }
			else if(month == 9) { return 31; }
			else if(month == 10) { return 30; }
			else { return 31; }
		}
		
		
		//////////////////////////////////////////////////////////////////////////////////////
		//
		//							DATA MEMBERS
		//
		//////////////////////////////////////////////////////////////////////////////////////
		
		// website name
		public var mWebsiteName:String = "";
		// community group id
		public var mCommGroupID:int = -1;
		// a collection of cKeywordAnalysisEntry objects
		public var mKeywordData:ArrayCollection = new ArrayCollection();
		// a collection of cLinkAnalysisEntry objects
		public var mLinksData:ArrayCollection = new ArrayCollection();	
		// a collection of cKeywordAnalysisEntry objects with time stamps from the past week
		public var mSortedData:ArrayCollection = new ArrayCollection();
	}
}