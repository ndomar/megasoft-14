package com.megasoft.entangle;
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

public class MainActivity extends Activity {
	String requestId;
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
        	
             GetRequest request = new GetRequest("http://entangle2.apiary-mock.com/request/" + requestId) {
     			 protected void onPostExecute(String response) {
     				 
					try {
						JSONObject json = new JSONObject(response);
	     				 TextView requester = (TextView) findViewById(R.id.requester); 
	     				 requester.setText("Requester: " + json.getString("requester"));
	     				 TextView description = (TextView) findViewById(R.id.description); 
	     				 description.setText("Description: "+ json.getString("description"));
	     				 TextView date = (TextView) findViewById(R.id.date); 
	     				 date.setText("Date of Request: " + json.getString("date"));
	     				 TextView tags = (TextView) findViewById(R.id.tags); 
	     				 tags.setText("Tags: " + json.getString("tags"));
	     				 TextView price = (TextView) findViewById(R.id.price); 
	     				 price.setText("Expected Price: " + json.getString("price"));
	     				 TextView deadline = (TextView) findViewById(R.id.deadline); 
	     				 deadline.setText("Deadline: " + json.getString("deadline"));
	     				 TextView status = (TextView) findViewById(R.id.status); 
	     				 status.setText("Status :" + json.getString("status")); 
					} catch (JSONException e) {
						
						e.printStackTrace();
					}
     				 
     			 }

				
     			 };
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
