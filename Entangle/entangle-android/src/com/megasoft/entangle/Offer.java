package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.requests.PostRequest;

import android.os.Bundle;
import android.app.Activity;
import android.view.Menu;
import android.widget.EditText;


public class Offer extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_offer);
		searchOffer(1);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.offer, menu);
		return true;
	}
	public void searchOffer(int id){
		
		  JSONObject json = new JSONObject();
	        try {
	            json.put("id", id );
	           
	        } catch (JSONException e) {
	            e.printStackTrace();
	        }
	
	      //Creating a new Post Request
	        PostRequest request = new PostRequest("http://entangle.io/Offer"){
	            protected void onPostExecute(String response) {  // On Post execute means after the execution of the request ( the callback )
	                 if( this.getStatusCode() == 201 ){
	                	 String temp="";
	                	 JSONObject x;
	                	 try {
							x=new JSONObject(response);
							temp=x.getString("price");
							EditText textview1=(EditText)findViewById(R.id.editText1);
							textview1.setText(temp);
							temp=x.getString("date");
							EditText textview2=(EditText)findViewById(R.id.editText2);
							textview2.setText(temp);
							temp=x.getString("description");
							EditText textview3=(EditText)findViewById(R.id.editText3);
							textview3.setText(temp);
							temp=x.getString("expecteddeadline");
							EditText textview4=(EditText)findViewById(R.id.editText4);
							textview4.setText(temp);
						} catch (JSONException e) {
							// TODO Auto-generated catch block
							e.printStackTrace();
						}
	                     viewSuccessMessage(response);
	                  }else if( this.getStatusCode() == 400 ) {
	                      showErrorMessage(response);
	                  }
	             }

				private void showErrorMessage(String response) {
					// TODO Auto-generated method stub
					
				}

				private void viewSuccessMessage(String response) {
					// TODO Auto-generated method stub
					
				}
	        };
	        request.setBody(json); // Adding the json to the body of the request
	        request.addHeader("X", "Hi"); // Adding any additional header needed to the request
	        request.execute(); // Executing the request
	
	}
	public void markAsDone(int id){
		JSONObject json = new JSONObject();
        try {
            json.put("id", id );
        } catch (JSONException e) {
            e.printStackTrace();
            PostRequest request = new PostRequest("http://entangle.io/Offer"){
	            protected void onPostExecute(String response) {  // On Post execute means after the execution of the request ( the callback )
	                 if( this.getStatusCode() == 201 ){
	                     viewSuccessMessage(response);
	                  }else if( this.getStatusCode() == 400 ) {
	                      showErrorMessage(response);
	                  }
	             }

				private void showErrorMessage(String response) {
					// TODO Auto-generated method stub
					
				}

				private void viewSuccessMessage(String response) {
					// TODO Auto-generated method stub
					
				}
	        };
	        request.setBody(json); // Adding the json to the body of the request
	        request.addHeader("X", "Hi"); // Adding any additional header needed to the request
	        request.execute(); // Executing the request
        }
	}
}
