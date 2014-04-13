package com.megasoft.entangle;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.requests.GetRequest;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.TextView;

public class request extends Activity {
	String requestId;
	String [] requestDetailNames={"Description", "Requester", "Date", "Tags", "Price", "Deadline","Status"}; 
	String [] apiOfferNames = {"id", "requestedPrice", "date", "description", "offererId", "status"};
    @Override
    protected void onCreate(Bundle savedInstanceState) {
    	setContentView(R.layout.request);
    	Intent intent = getIntent();
    	//requestId = intent.getExtras().getString("RequestId");
    	requestId="1";
    	super.onCreate(savedInstanceState);
        this.fillRequestDetails();
        this.setCreateOfferButton(); 
        this.setViewOffersButton();
        }	
    public void fillRequestDetails(){
    	requestId="test";
         GetRequest request = new GetRequest("http://entangle2.apiary-mock.com/request/" +requestId) {	
        	 protected void onPostExecute(String response) {
					try {
							//Log.e("test",response);
		     				JSONObject json = new JSONObject(response);
							for(int k =0; k<requestDetailNames.length; k++){
								TextView textView = (TextView) findViewById(R.id.requester);
								textView.setText(requestDetailNames[k] + " " + json.getString(requestDetailNames[k]));
							}
							JSONArray offers = new JSONArray(json.getString("offers"));
		     				 String[] offerDetails = new String[offers.length()];
		     				 String [] apiOfferNames = {"id", "requestedPrice", "date", "description", "offererId", "status"};
		     				 for(int i = 0 ; i < offers.length(); i++) {
		     				    String [] details = new String[6];
		     				    for(int j=0; j<6;j++){
		     				    	 details[j]= offers.getJSONObject(i).getString(apiOfferNames[j]);
		     				    }
		     				 
		     				 }
		     				} 
		     				
		     			 catch (JSONException e) {
							
							e.printStackTrace();
						}
	     				
					
					
					
     			 }
        	};
        	
        	request.addHeader("X-SESSION-ID" , "asdasdasdsadasdasd");
        	request.execute();
             
            
    }
        
        public void setViewOffersButton(){
        	 final Intent intentViewOffers = new Intent(this,ViewOffers.class); 
             Button viewOffers = (Button) findViewById(R.id.button2); 
             viewOffers.setOnClickListener(new OnClickListener(){
     			public void onClick(View arg0) {
     				intentViewOffers.putExtra("RequestId", requestId);
     				startActivity(intentViewOffers); 
     				}
             });
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
    
        

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.main, menu);
        return true;
    }
    
    
}
