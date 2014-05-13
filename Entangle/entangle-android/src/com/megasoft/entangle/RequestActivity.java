package com.megasoft.entangle;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.R.integer;
import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.LinearLayout;

import com.megasoft.config.Config;
import com.megasoft.requests.GetRequest;

public class RequestActivity extends FragmentActivity {
	/**
	 * the tangle Id
	 */
	int tangleId;
	/**
	 * the request Id
	 */
	int requestId;
	/**
	 * the session Id
	 */
	String sessionId;
	/**
	 * settings
	 */
	SharedPreferences settings;
	/**
	 * array of offers for a request
	 */
	JSONArray offers;
	/**
	 * array to add details about offer
	 */
	String[][] offerDetails;
	/**
	 * saved values to add to request fields
	 */
	String[] requestDetailNames = { "Description", "Requester", "Date", "Tags",
			"Price", "Deadline", "Status" };
	/**
	 * saved values for json fields of apiary
	 */
	String[] apiOfferNames = { "requestedPrice", "date", "description",
			"offererId", "status" };
	/**
	 * saved values of offer fields of apairy
	 */
	String[] offerFieldNames = { "Requested Price: ", "Date: ",
			"Description: ", "Offered By: ", "Status: " };
	/**
	 * this activity
	 */
	final Activity self = this;
	/**
	 * this layout
	 */
	LinearLayout requestLayout;
	/**
	 * this layout
	 */
	LinearLayout offersLayout;
	/**
	 * this is the endpoint string
	 */
	String REQUEST;

	/**
	 * this is for checking if I have my own request open
	 */
	boolean myRequest = false;
	/**
	 * this is an array to match the request status code to it's worded
	 * equivalent
	 */
	String[] requestStatusCodes = { "OPEN", "CLOSED", "FROZEN" };
	/**
	 * this is an array to match the offer status code to it's worded equivalent
	 */
	String[] offerStatusCodes = { "PENDING", "DONE", "ACCEPTED", "FAILED",
			"REJECTED" };

	/**
	 * this calls fillRequestDetails() to generate the request preview
	 * 
	 * @param Bundle
	 *            savedInstanceState
	 * @return none
	 * @author sak93
	 */
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_request);
		
		Intent intent = getIntent();
		this.requestId = intent.getIntExtra("requestId", -1);
		this.tangleId = intent.getIntExtra("tangleId", -1);
		 REQUEST = "/tangle/" + tangleId + "/request/" + requestId;
		
		requestLayout = (LinearLayout) this.findViewById(R.id.request_entry_layout);
		offersLayout = (LinearLayout) this.findViewById(R.id.offer_entries_layout);
		
		
		this.fillRequestDetails();
	}

	/**
	 * this receives a response from back end and calls addRequestFields and
	 * addOffers
	 * 
	 * @param none
	 * @return none
	 * @author sak93
	 */
	public void fillRequestDetails() {

		this.settings = getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		GetRequest request = new GetRequest(Config.API_BASE_URL_SERVER + REQUEST) {
			protected void onPostExecute(String response) {
				try {
					
					if (this.getStatusCode() == 200) {
						JSONObject json = new JSONObject(response);
						requestLayout.removeAllViewsInLayout();
						offersLayout.removeAllViewsInLayout();
						addRequestFields(json);
						addOffers(json);
					} else {
						Log.e("test", this.getErrorMessage());
						Log.e("test", REQUEST);
						//showErrorMessage();
						// TODO
//						TextView errorMessage = new TextView(self);
//						errorMessage.setText(json.getString("Error"));
//						errorMessage.setTextSize(25);
//						errorMessage.setTypeface(null, Typeface.BOLD_ITALIC);
//						errorMessage.setTextColor(Color.WHITE);
//						layout.addView(errorMessage);
					}
				} catch (JSONException e) {
					e.printStackTrace();
				}
			}
		};
		request.addHeader(Config.API_SESSION_ID, sessionId);
		request.execute();

	}

	/**
	 * this retrieves request detail fields from JSONOBject and adds them to
	 * text fields
	 * 
	 * @param JSONObject
	 *            json Json that holds request details
	 * @return none
	 * @author sak93
	 */
	public void addRequestFields(JSONObject json) throws JSONException {
		
		RequestEntryFragment requestFragmet = new RequestEntryFragment();
		Bundle args = new Bundle();
		args.putString("description",json.getString("description"));
		args.putString("requesterName",json.getString("requesterName"));
		args.putString("date",json.getJSONObject("date").getString("date"));
		args.putString("tags",getTags(json.getJSONArray("tags")));
		args.putString("price",json.getString("price"));
		if(json.get("deadline") == null){
			args.putString("deadline",json.getJSONObject("deadline").getString("date"));
		}
		args.putString("status",requestStatusCodes[Integer.parseInt(json.getString("status"))]);
		requestFragmet.setArguments(args);
		
		getSupportFragmentManager().beginTransaction().add(R.id.request_entry_layout,requestFragmet).commit();
	}

	/**
	 * this retrieves offers and offer detail fields from JSONOBject and adds
	 * them to text fields
	 * 
	 * @param JSONObject
	 *            json Json that holds request details
	 * @return none
	 * @author sak93
	 */

	public void addOffers(JSONObject json) throws JSONException {
		JSONArray offers = (JSONArray) json.get("offers");
		if(offers.length() == 0){
			findViewById(R.id.view_request_offer_header).setVisibility(View.INVISIBLE);
		}else{
			findViewById(R.id.view_request_offer_header).setVisibility(View.VISIBLE);
		}
		
		for (int i = 0; i < offers.length(); i++) {
			JSONObject offer = offers.getJSONObject(i); 
			OfferEntryFragment offerFragmet = new OfferEntryFragment();
			Bundle args = new Bundle();
			args.putInt("offerId", Integer.parseInt(offer.getString("id"))); 
			args.putString("requestedPrice",offer.getString("price"));
			args.putString("date",offer.getJSONObject("date").getString("date"));
			args.putString("description",offer.getString("description"));
			args.putString("offerer",offer.getString("offererName"));
			args.putString("status",offerStatusCodes[Integer.parseInt(offer.getString("status"))]);
			offerFragmet.setArguments(args);
			
			getSupportFragmentManager().beginTransaction().add(R.id.offer_entries_layout,offerFragmet).commit();
		}
		
//		if (myRequest == true) {
//			Button deleteRequest = new Button(this);
//			deleteRequest.setText("Delete");
//			layout.addView(deleteRequest);
//		}
	}

	/**
	 * this generates a String of tags
	 * 
	 * @param JSONArray
	 *            tagArray JsonArray of tags
	 * @return String tags String of tags
	 * @author sak93
	 */
	public String getTags(JSONArray tagArray) throws JSONException {
		String tags = " Tags: ";
		for (int i = 0; i < tagArray.length(); i++) {
			if (i < (tagArray.length() - 1))
				tags += tagArray.get(i) + ", ";
			else
				tags += tagArray.get(i);
		}
		return tags;

	}
	
	public void sendDeleteRequest(){
		
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.

		getMenuInflater().inflate(R.menu.request_information, menu);
		return true;
	}
	
	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
	     
	 	 switch (item.getItemId()) {
	 	 	case R.id.createOffer:
	 	 		Intent intent = new Intent(this, CreateOfferActivity.class);
	 	        intent.putExtra("tangleId", this.tangleId);
	 	        intent.putExtra("requestId", this.requestId);
	 	        startActivity(intent);
	 	        return true;
	 	 	
	 	 	case R.id.deleteRequest:
	 	 		sendDeleteRequest();
	 	 		return true;
	 	 		
	 	 		
	 	    default:
	 	        return super.onOptionsItemSelected(item);
	 	 }

	}

}