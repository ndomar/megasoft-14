package com.megasoft.entangle;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.graphics.Color;
import android.graphics.Typeface;
import android.os.Bundle;
import android.view.Menu;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.megasoft.requests.GetRequest;

public class Request extends Activity {
	String requestId = "1";
	JSONArray offers;
	String[][] offerDetails;
	String[] requestDetailNames = { "Description", "Requester", "Date", "Tags",
			"Price", "Deadline", "Status" };
	String[] apiOfferNames = { "requestedPrice", "date", "description",
			"offererId", "status" };
	String[] offerFieldNames = { "Requested Price: ", "Date: ",
			"Description: ", "Offered By: ", "Status: " };

	final Activity self = this;
	LinearLayout layout;

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

		// Intent intent = getIntent();
		// requestId = intent.getExtras().getString("RequestId");
		super.onCreate(savedInstanceState);
		setContentView(R.layout.request);
		this.fillRequestDetails();
	}

	/**
	 * this receives a response from backend and calls addRequestFields and
	 * addOffers
	 * 
	 * @param none
	 * @return none
	 * @author sak93
	 */
	public void fillRequestDetails() {
		layout = (LinearLayout) this.findViewById(R.id.layout);
		GetRequest request = new GetRequest(
				"http://entangle2.apiary-mock.com/request/" + requestId) {
			protected void onPostExecute(String response) {
				try {
					JSONObject json = new JSONObject(response);
					addRequestFields(json);
					addOffers(json);
				} catch (JSONException e) {
					e.printStackTrace();
				}
			}
		};
		request.addHeader("X-SESSION-ID", "asdasdasdsadasdasd");
		request.execute();

	}

	/* Uncomment when linked */
	public void setCreateOfferButton() {
		// final Intent intentAddOffer = new Intent(this,CreateOffers.class);
		/*
		 * Button addOffer = (Button) findViewById(R.id.button1);
		 * addOffer.setOnClickListener(new OnClickListener(){ String requestId=
		 * ""; public void onClick(View arg0) {
		 * intentAddOffer.putExtra("RequestId", requestId);
		 * startActivity(intentAddOffer); } });
		 */
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
				fieldDetails += requestDetailNames[i] + ": "
						+ json.get(requestDetailNames[i]);
			}
			detail.setTextSize(20);
			detail.setTypeface(null, Typeface.BOLD_ITALIC);
			detail.setText(fieldDetails);
			detail.setTextColor(Color.WHITE);
			layout.addView(detail);
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

				String field = (String) offer.get(apiOfferNames[j]);
				add += "\n " + offerFieldNames[j] + field;
			}
			details.setTextSize(20);
			details.setTypeface(null, Typeface.BOLD_ITALIC);
			details.setText(add);
			details.setTextColor(Color.WHITE);
			layout.addView(details);
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
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}
}
