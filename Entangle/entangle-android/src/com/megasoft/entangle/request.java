package com.megasoft.entangle;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;


import com.megasoft.requests.GetRequest;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.TextView;

public class request extends Activity {
	String requestId;
	JSONArray offers; 
	String[][] offerDetails;
	int x =1; 
	String [] requestDetailNames={"Description", "Requester", "Date", "Tags", "Price", "Deadline","Status"}; 
	String [] apiOfferNames = {"requestedPrice", "date", "description", "offererId", "status"};
	String [] offerFieldNames = {"Requested Price: ", "Date: ", "Description: ", "Offered By: ", "Status: "};

	final Activity self = this;
	LinearLayout layoutContainer;
	@Override
    protected void onCreate(Bundle savedInstanceState) {
		
    	//Intent intent = getIntent();
    	//requestId = intent.getExtras().getString("RequestId");
    	requestId="1";
    	super.onCreate(savedInstanceState);
    	setContentView(R.layout.request);
        this.fillRequestDetails();
       // this.setCreateOfferButton(); 
       
        }	
    
    public void fillRequestDetails(){
    	requestId="test";
    	layoutContainer = (LinearLayout) this.findViewById(R.id.layout); 
    	GetRequest request = new GetRequest("http://sak93.apiary-mock.com/request/" +requestId) {	
        	 protected void onPostExecute(String response) {
					try {
						Log.e("test",response);
		     			JSONObject json = new JSONObject(response);
		     			addRequestFields(json);
		     			addOffers(json);
		     			}	
		     			 catch (JSONException e) {
							e.printStackTrace();
						}
     			 }
        	};  	
        	request.addHeader("X-SESSION-ID" , "asdasdasdsadasdasd");
        	request.execute();
             
            
    }
      
    
        
        /*Uncomment when linked*/
        public void setCreateOfferButton(){
            // final Intent intentAddOffer = new Intent(this,CreateOffers.class);
        	/*Button addOffer = (Button) findViewById(R.id.button1);
    		addOffer.setOnClickListener(new OnClickListener(){
    			String requestId= ""; 
    			public void onClick(View arg0) {
    				intentAddOffer.putExtra("RequestId", requestId);
    				startActivity(intentAddOffer); 
    				}
            });*/
        }
    
        public void addRequestFields(JSONObject json) throws JSONException{
        	
        	for(int i =0; i<requestDetailNames.length;i++){
        		TextView detail = new TextView(self);
        		String fieldDetails = "";
        		if(i==3){
        			JSONArray tagArray= (JSONArray)json.get("Tags");
        			fieldDetails = getTags(tagArray);
        		}
        		else{
        		fieldDetails += requestDetailNames[i] + ": " + json.get(requestDetailNames[i]);
        		}
        		detail.setText(fieldDetails);
        		layoutContainer.addView(detail);
        		}
        }
			
        public void addOffers(JSONObject json) throws JSONException{
        	JSONArray offers = (JSONArray) json.get("Offers");
			for(int i =0; i<offers.length();i++){
				JSONObject offer = offers.getJSONObject(i);
        		TextView details = new TextView(self);
        		String add = "\nOffer " + (i+1) + ": ";
        		for(int j =0; j<apiOfferNames.length;j++){
        		
        		String field = (String) offer.get(apiOfferNames[j]);
        		add += "\n" + offerFieldNames[j]+ field;
        		}
        		details.setText(add);
        			layoutContainer.addView(details);
			}
        }
        public String getTags(JSONArray tagArray) throws JSONException{
        	String tags="Tags: "; 
        	for(int i=0; i<tagArray.length();i++){
    			if(i<(tagArray.length()-1)) tags+= tagArray.get(i) + ", ";
    			else tags+= tagArray.get(i);
    			}
        	return tags; 
        }
        

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.main, menu);
        return true;
    }
    }
