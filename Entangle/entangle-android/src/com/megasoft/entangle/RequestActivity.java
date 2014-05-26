package com.megasoft.entangle;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.AlertDialog.Builder;
import android.app.Dialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.LinearLayout;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.requests.DeleteRequest;
import com.megasoft.requests.GetRequest;
import com.megasoft.entangle.megafragments.*;

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
	boolean isMyRequest = false;
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

	MenuItem deleteItem = null;

	Activity activity = null;
	private boolean isDestroyed;
	private Menu menu;

	public Activity getActivity() {
		return activity;
	}

	public void setActivity(Activity activity) {
		this.activity = activity;
	}

	public MenuItem getDeleteItem() {
		return deleteItem;
	}

	public void setDeleteItem(MenuItem deleteItem) {
		this.deleteItem = deleteItem;
	}

	public void setIsMyRequest(boolean myRequest) {
		this.isMyRequest = myRequest;
	}

	public boolean getIsMyRequest() {
		return this.isMyRequest;
	}

	public int getRequestId() {
		return this.requestId;
	}

	public String getSessionId() {
		return this.sessionId;
	}

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
		setActivity(this);
		Intent intent = getIntent();
		this.requestId = intent.getExtras().getInt("requestId", -1);
		this.tangleId = intent.getExtras().getInt("tangleId", -1);
		REQUEST = "/tangle/" + tangleId + "/request/" + requestId;

		requestLayout = (LinearLayout) this
				.findViewById(R.id.request_entry_layout);
		offersLayout = (LinearLayout) this
				.findViewById(R.id.offer_entries_layout);

	}

	public void onResume() {
		super.onResume();
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
		GetRequest request = new GetRequest(Config.API_BASE_URL
				+ REQUEST) {
			protected void onPostExecute(String response) {
				if(isDestroyed){
					return;
				}
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
						// showErrorMessage();
						// TODO
						// TextView errorMessage = new TextView(self);
						// errorMessage.setText(json.getString("Error"));
						// errorMessage.setTextSize(25);
						// errorMessage.setTypeface(null, Typeface.BOLD_ITALIC);
						// errorMessage.setTextColor(Color.WHITE);
						// layout.addView(errorMessage);
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
		args.putString("description", json.getString("description"));
		args.putString("requesterName", json.getString("requesterName"));
		args.putString("date", json.getJSONObject("date").getString("date"));
		args.putString("tags", getTags(json.getJSONArray("tags")));
		if(json.getString("price").equals("null")){
			args.putString("price", "--");
		}else{
			args.putString("price", json.getString("price"));
		}
		
		if (json.get("deadline") == null) {
			args.putString("deadline", json.getJSONObject("deadline")
					.getString("date"));
		}
		args.putString("status",
				requestStatusCodes[Integer.parseInt(json.getString("status"))]);
		requestFragmet.setArguments(args);

		setIsMyRequest(Integer.parseInt(json.getString("MyRequest")) == 1);
		if (getIsMyRequest() && getDeleteItem() != null) {
			getDeleteItem().setEnabled(true);
			getDeleteItem().setVisible(true);
			this.menu.findItem(R.id.createOffer).setVisible(false);
			this.menu.findItem(R.id.createOffer).setEnabled(false);
		}
		
		if(activity.getSharedPreferences(Config.SETTING, 0).getString(Config.USERNAME, "").equals(json.getString("requesterName"))){
			
		}

		getSupportFragmentManager().beginTransaction()
				.add(R.id.request_entry_layout, requestFragmet).commit();

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
		if (offers.length() == 0) {
			findViewById(R.id.view_request_offer_header).setVisibility(
					View.INVISIBLE);
		} else {
			findViewById(R.id.view_request_offer_header).setVisibility(
					View.VISIBLE);
		}
		
		boolean offeredBefore = false;
		for (int i = 0; i < offers.length(); i++) {
			JSONObject offer = offers.getJSONObject(i);
			OfferEntryFragment offerFragmet = new OfferEntryFragment();
			Bundle args = new Bundle();

			args.putInt("offerId", Integer.parseInt(offer.getString("id")));
			args.putString("requestedPrice", offer.getString("price"));
			args.putString("date", offer.getJSONObject("date")
					.getString("date"));
			args.putString("description", offer.getString("description"));
			args.putString("offerer", offer.getString("offererName"));
			args.putString("offererAvatar", offer.getString("offererAvatar"));
			args.putString("status", offerStatusCodes[Integer.parseInt(offer
					.getString("status"))]);
			offerFragmet.setArguments(args);

			getSupportFragmentManager().beginTransaction()
					.add(R.id.offer_entries_layout, offerFragmet).commit();
			
			if(offer.getString("offererName").equals(settings.getString(Config.USERNAME, ""))){
				offeredBefore = true;
			}
		}
		
		if(offeredBefore){
			this.menu.findItem(R.id.createOffer).setVisible(false);
			this.menu.findItem(R.id.createOffer).setEnabled(false);
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

	/*
	 * Sends a delete request to the server to delete the viewed request
	 * 
	 * @author OmarElAzazy
	 */
	public void sendDeleteRequest() {
		DeleteRequest deleteRequest = new DeleteRequest(Config.API_BASE_URL
				+ "/request/" + getRequestId()) {
			protected void onPostExecute(String response) {
				if(isDestroyed){
					return;
				}
				if (!this.hasError()) {
					getActivity().finish();
				} else {
					toasterShow("Something went wrong, Please try again.");
				}
			}
		};

		deleteRequest.addHeader(Config.API_SESSION_ID, getSessionId());
		deleteRequest.execute();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.

		getMenuInflater().inflate(R.menu.request_information, menu);

		setDeleteItem(menu.findItem(R.id.deleteRequestOption));
		this.menu = menu;
		if (getIsMyRequest()) {
			getDeleteItem().setEnabled(true);
			getDeleteItem().setVisible(true);
			this.menu.findItem(R.id.createOffer).setVisible(false);
			this.menu.findItem(R.id.createOffer).setEnabled(false);
		}

		return true;
	}

	@Override
	public boolean onOptionsItemSelected(MenuItem item) {

		switch (item.getItemId()) {
		case R.id.deleteRequestOption:
			this.showDialog(0);
			return true;

		case R.id.createOffer:
			Intent intent = new Intent(this, CreateOfferActivity.class);
			intent.putExtra("tangleId", this.tangleId);
			intent.putExtra("requestId", this.requestId);
			startActivity(intent);
			return true;

		default:
			return super.onOptionsItemSelected(item);
		}

	}

	/*
	 * Shows a message in a toaster
	 * 
	 * @author Omar ElAzazy
	 */
	public void toasterShow(String message) {
		Toast.makeText(getActivity().getBaseContext(), message,
				Toast.LENGTH_LONG).show();
	}

	/**
	 * This method is called when showDialog(int) method is called and it is
	 * responsible for creating a dialog to make sure that the user wants to
	 * leave the tangle
	 * 
	 * @param dialogId
	 *            , is an int that corresponds to the id of the dialog being
	 *            created but it is not used in this situation
	 * 
	 * @author OmarElAzazy
	 */
	@Override
	protected Dialog onCreateDialog(int dialogId) {
		Builder dialogBuilder = new AlertDialog.Builder(this);
		if (dialogId == 0) {
			dialogBuilder.setTitle("Deleting the request");
			dialogBuilder
					.setMessage("Are you sure you want to delete this request ?");
			dialogBuilder.setPositiveButton("Yes",
					new DialogInterface.OnClickListener() {

						@Override
						public void onClick(DialogInterface dialog, int which) {
							sendDeleteRequest();
							dialog.dismiss();
						}
					});
			dialogBuilder.setNegativeButton("No",
					new DialogInterface.OnClickListener() {

						@Override
						public void onClick(DialogInterface dialog, int which) {
							dialog.dismiss();
						}
					});
		}
		return dialogBuilder.create();
	}
	
	public void onPause() {
		super.onPause();
		isDestroyed = true;
	}

}
