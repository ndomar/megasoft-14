package com.megasoft.entangle;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.ActionBar.LayoutParams;
import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Color;
import android.graphics.Typeface;
import android.os.Bundle;
import android.util.Log;
import android.view.Gravity;
import android.view.Menu;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.megasoft.config.Config;
import com.megasoft.requests.GetRequest;

public class RequestActivity extends Activity {
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
	LinearLayout layout;
	/**
	 * this is the endpoint string
	 */
	final String REQUEST = "/tangle/" + tangleId + "/request/" + requestId;
	
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

		Intent intent = getIntent();
		this.requestId = intent.getIntExtra("RequestId", -1);
		this.tangleId = intent.getIntExtra("TangleId", -1);
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_request);
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
	//	layout = (LinearLayout) this.findViewById(R.id.request_activity);
		LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(
				LayoutParams.WRAP_CONTENT, LayoutParams.WRAP_CONTENT);
		params.gravity = Gravity.RIGHT;
		ImageView icon = new ImageView(this);
		icon.setLayoutParams(params);
		layout.addView(icon);
		this.settings = getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		GetRequest request = new GetRequest(Config.API_BASE_URL + REQUEST) {
			protected void onPostExecute(String response) {
				try {
					Log.e("zeft", response);
					JSONObject json = new JSONObject(response);
					addRequestFields(json);
					addOffers(json);
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
		TextView title = new TextView(self);
		title.setText("\n Request " + requestId + ":" + "\n");
		title.setTextSize(25);
		title.setTypeface(null, Typeface.BOLD_ITALIC);
		title.setTextColor(Color.WHITE);
		layout.addView(title);
		for (int i = 0; i < requestDetailNames.length; i++) {
			TextView detail = new TextView(self);
			String fieldDetails = " ";
			if (i == 3) {
				JSONArray tagArray = (JSONArray) json.get("Tags");
				fieldDetails = getTags(tagArray);
			} else {
				if (i == 6) {
					String status = (String) json.get(requestDetailNames[i]);
					fieldDetails += requestDetailNames[i] + ": "
							+ requestStatusCodes[Integer.parseInt(status)];
				} else {
					fieldDetails += requestDetailNames[i] + ": "
							+ json.get(requestDetailNames[i]);
				}
			}
			detail.setTypeface(null, Typeface.BOLD_ITALIC);
			detail.setTextSize(20);
			detail.setText(fieldDetails);
			detail.setTextColor(Color.WHITE);
			layout.addView(detail);
		}
		String myRequestString = json.getString("MyRequest");
		if (myRequestString.equals("1")) {
			myRequest = true;
		}
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
		JSONArray offers = (JSONArray) json.get("Offers");
		TextView title = new TextView(self);
		title.setText("\n Offers:");
		title.setTextSize(25);
		title.setTypeface(null, Typeface.BOLD_ITALIC);
		title.setTextColor(Color.WHITE);
		layout.addView(title);
		for (int i = 0; i < offers.length(); i++) {
			JSONObject offer = offers.getJSONObject(i);
			TextView details = new TextView(self);
			String add = "\n Offer " + (i + 1) + ": ";
			for (int j = 0; j < apiOfferNames.length; j++) {
				if (j == 4) {
					String field = (String) offer.get(apiOfferNames[j]);
					add += "\n" + offerFieldNames[j]
							+ offerStatusCodes[Integer.parseInt(field)];
				} else {
					String field = (String) offer.get(apiOfferNames[j]);
					add += "\n " + offerFieldNames[j] + field;
				}
			}
			details.setTextSize(20);
			details.setTypeface(null, Typeface.BOLD_ITALIC);
			details.setText(add);
			details.setTextColor(Color.WHITE);
			layout.addView(details);
		}
		if (myRequest == true) {
			Button deleteRequest = new Button(this);
			deleteRequest.setText("Delete");
			layout.addView(deleteRequest);
		}
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

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.

		getMenuInflater().inflate(R.menu.request_information, menu);
		return true;
	}

}
