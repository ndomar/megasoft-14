package com.megasoft.entangle;

import com.megasoft.config.Config;
import com.megasoft.requests.GetRequest;
import com.megasoft.requests.PostRequest;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;
import org.json.JSONException;
import android.content.Intent;
import android.content.SharedPreferences;
import android.app.Activity;
import android.app.FragmentTransaction;

import org.json.JSONObject;
import android.os.Bundle;
import android.view.Menu;
import android.view.View;

/**
 * View an offer given the offer Id
 * 
 * @author Almgohar
 */
public class OfferActivity extends Activity {
	/**
	 * The TextView that holds the request's description
	 */
	private TextView requestDescription;

	/**
	 * The TextView that holds the offer's description
	 */
	private TextView offerDescription;

	/**
	 * The TextView that holds the offer's expected deadline
	 */
	private TextView offerDeadline;

	/**
	 * The TextView that holds the requester's name
	 */
	private TextView requesterName;

	/**
	 * The TextView that holds the offerer's name
	 */
	private TextView offererName;

	/**
	 * The TextView that holds the offer's status
	 */
	private TextView offerStatus;

	/**
	 * The TextView that holds the offer's price
	 */
	private TextView offerPrice;

	/**
	 * The TextView that holds the date on which the offer was created
	 */
	private TextView offerDate;

	/**
	 * The button that allows the user to accept an offer
	 */
	private Button acceptOffer;

	/**
	 * The tangle Id
	 */
	private int tangleId;

	/**
	 * The offer Id
	 */
	private int offerId;

	/**
	 * The session Id
	 */
	private String sessionId;

	/**
	 * The preferences instance
	 */
	private SharedPreferences settings;

	/**
	 * JSON object to be received from Get request
	 */
	JSONObject returnedResponse = null;
	/**
	 * The layout containing the DeleteOffer button
	 */
	LinearLayout deleteOfferLayout;
	/**
	 * String for get offer details endpoint
	 */
	final String OFFER = "/offer/" + offerId;
	/**
	 * String for post offer endpoint
	 */
	final String ACCEPT = "/accept/offer";
	/**
	 * String for post and get offer endpoint
	 */
	final String Request = "/request/";
	/**
	 * String for post offer endpoint
	 */
	final String Offer = "/offers/";
	/**
	 * String for offer status
	 */
	final String Pending = "0";
	/**
	 * String for offer status
	 */
	final String Done = "1";
	/**
	 * The id of the logged in user
	 */
	private int loggedInId;
	
	/**
	 * The FragmentTransaction that handles adding the fragments to the activity
	 */
	private FragmentTransaction transaction;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_offer);
		Intent intent = getIntent();
		this.settings = getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		this.loggedInId = settings.getInt(Config.USER_ID, 1);
		this.offerId = intent.getIntExtra("offerID", 3);
		viewOffer();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		getMenuInflater().inflate(R.menu.offer, menu);
		return true;
	}

	/**
	 * Initializes all views to link to the XML views Sends a GET request and
	 * get the JSon response Calls the ViewRequestInformation method Calls the
	 * ViewOfferInformation method
	 * 
	 * @author Almgohar
	 */
	public void viewOffer() {
		requestDescription = (TextView) findViewById(R.id.request_description);
		offerDescription = (TextView) findViewById(R.id.offer_description);
		offerDeadline = (TextView) findViewById(R.id.offer_deadline);
		requesterName = (TextView) findViewById(R.id.requester_name);
		offererName = (TextView) findViewById(R.id.offerer_name);
		offerStatus = (TextView) findViewById(R.id.offer_status);
		offerPrice = (TextView) findViewById(R.id.offer_price);
		offerDate = (TextView) findViewById(R.id.offer_date);
		
		deleteOfferLayout = (LinearLayout) findViewById(R.id.delete_offer_layout);
		acceptOffer = (Button) findViewById(R.id.accept_offer);

		String link = "http://entangle2.apiary-mock.com/offer/" + offerId + "/";

		GetRequest request = new GetRequest(link) {
			protected void onPostExecute(String response) {
				if (this.getStatusCode() == 200) {
					try {
						JSONObject jSon = new JSONObject(response);
						tangleId = jSon.getInt("tangleId");
						JSONObject requestInformation = jSon
								.getJSONObject("requestInformation");
						JSONObject offerInformation = jSon
								.getJSONObject("offerInformation");
						viewRequestInfo(requestInformation);
						viewOfferInfo(offerInformation);
					} catch (JSONException e) {
						e.printStackTrace();
					}
				} else {
					Toast toast = Toast
							.makeText(
									getApplicationContext(),
									"There seemed to be an error in viewing this offer",
									Toast.LENGTH_SHORT);
					toast.show();
				}
			}
		};
		request.addHeader("X-SESSION-ID", this.sessionId);
		request.execute();
	}

	/**
	 * Retrieves the required request information from the JSonObject Views the
	 * request information
	 * 
	 * @param JSonObject
	 *            requestInformation
	 * @author Almgohar
	 */
	private void viewRequestInfo(JSONObject requestInformation) {
		try {
			requesterName
					.setText(requestInformation.getString("requesterName"));
			requestDescription.setText(requestInformation
					.getString("requestDescription"));

			final int userId = requestInformation.getInt("requesterID");
			final int requestId = requestInformation.getInt("requestID");

			if (userId == loggedInId) {
				acceptOffer.setVisibility(View.VISIBLE);
				validate();

			}
			requesterName.setOnClickListener(new View.OnClickListener() {
				@Override
				public void onClick(View v) {
					goToProfile(userId);
				}
			});

			requestDescription.setOnClickListener(new View.OnClickListener() {
				@Override
				public void onClick(View v) {
					goToRequest(requestId);
				}
			});

		} catch (JSONException e) {
			e.printStackTrace();
		}
	}

	/**
	 * Retrieves the required offer information from the JSonObject Views the
	 * offer information
	 * 
	 * @param JSonObject
	 *            offerInformation
	 * @author Almgohar
	 */
	private void viewOfferInfo(JSONObject offerInformation) {

		try {
			offerDescription.setText(offerInformation
					.getString("offerDescription"));
			offerDeadline.setText(offerInformation.getString("offerDeadline"));
			offererName.setText(offerInformation.getString("offererName"));
			offerDate.setText(offerInformation.getString("offerDate"));
			offerPrice.setText(Integer.toString(offerInformation
					.getInt("offerPrice")));

			final int userId = offerInformation.getInt("offererID");
			int status = offerInformation.getInt("offerStatus");

			if (status == 0)
				offerStatus.setText("New");
			else if (status == 1)
				offerStatus.setText("Done");

			if (userId == loggedInId) {
				transaction = getFragmentManager().beginTransaction();
				DeleteButtonFragment deleteFragment = new DeleteButtonFragment();
				Bundle bundle = new Bundle();
				bundle.putString("resourceType", "offer");
				bundle.putInt("offerId", offerId);
				deleteFragment.setArguments(bundle);
				transaction.add(R.id.delete_offer_layout, deleteFragment);
				transaction.commit();
			}
			offererName.setOnClickListener(new View.OnClickListener() {

				@Override
				public void onClick(View v) {
					goToProfile(userId);
				}
			});
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}

	/**
	 * Redirects to a user's profile given his id
	 * 
	 * @param int userId
	 * @author Almgohar
	 */
	private void goToProfile(int userId) {
		Intent profile = new Intent(this, ProfileActivity.class);
		profile.putExtra("user id", userId);
		profile.putExtra("tangle id", this.tangleId);
		startActivity(profile);
	}

	/**
	 * Redirects to a request given its id
	 * 
	 * @param int requestId
	 * @author Almgohar
	 */
	private void goToRequest(int requestId) {
		Intent request = new Intent(this, RequestActivity.class);
		request.putExtra("request id", requestId);
		startActivity(request);
	}

	/**
	 * this checks if offer can be accepted and calls addAcceptButton() if it
	 * can
	 * 
	 * @param none
	 * @return None
	 * @author sak93
	 */
	public void validate() {
		GetRequest request = new GetRequest(Config.API_BASE_URL + OFFER) {

			protected void onPostExecute(String response) {
				try {

					JSONObject jsonResponse = new JSONObject(response);
					JSONObject offerDetails = (JSONObject) jsonResponse
							.get("offerInformation");
					JSONObject requestDetails = (JSONObject) jsonResponse
							.get("requestInformation");
					int requestStatus = (Integer) requestDetails
							.get("requestStatus");
					if (requestStatus != 0) {
						return;
					} else {
						int offerStatus = (Integer) offerDetails
								.get("offerStatus");
						if (offerStatus == 0) {
							addAcceptButton();
						}
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
	 * this adds a button which if clicked sends a POST method to update the
	 * offer as accepted
	 * 
	 * @param none
	 * @return None
	 * @author sak93
	 */
	public void addAcceptButton() throws JSONException {
		acceptOffer.setText("Accept");
		acceptOffer.setVisibility(1);
		returnedResponse = new JSONObject();
		returnedResponse.put("offerId", "" + offerId);
		acceptOffer.setOnClickListener(new View.OnClickListener() {
			public void onClick(View v) {
				PostRequest r = new PostRequest(Config.API_BASE_URL + ACCEPT);
				r.setBody(returnedResponse);
				r.addHeader("x-session-id", Config.SESSION_ID);
				r.execute();
				acceptOffer.setVisibility(View.GONE);

			}
		});

	}
	/**
	 * this checks if an offer is already marked as done or not accepted.if
	 * neither it navigates to the actual notifying method
	 * @param View view The Button clicked
	 * @return None
	 * @author mohamedzayan
	 */
	public void notifyCheck(View view) {
		GetRequest initRequest = new GetRequest(
				Config.API_BASE_URL+Request + 1 + Offer + offerId) {
			protected void onPostExecute(String response) {
				if (this.getStatusCode() == 200) {
					JSONObject jresponse;
					try {
						jresponse = new JSONObject(response);
						if (jresponse.getString("status").equals(Pending)
								|| jresponse.getString("status").equals(Done)) {
							Toast error = Toast.makeText(
									getApplicationContext(), R.string.error,
									Toast.LENGTH_LONG);
							error.show();
						} else {
							sendNotification(offerId);
						}

					} catch (JSONException e) {
						// TODO Auto-generated catch block
						e.printStackTrace();
					}
				}
			}
		};
		initRequest.addHeader(Config.API_SESSION_ID, sessionId);
		initRequest.execute();
	}
	/**
	 * this sends the actual notification
	 * @param  Int OfferId offer ID
	 * @return None
	 * @author mohamedzayan
	 */
	public void sendNotification(int Offerid) {
		PostRequest request = new PostRequest(
				Config.API_BASE_URL+Request + Offerid) {
			protected void onPostExecute(String response) {
				if (this.getStatusCode() == 201) {
					Toast success = Toast.makeText(getApplicationContext(),
							R.string.note, Toast.LENGTH_LONG);
					success.show();
				}
			}

		};
		request.addHeader(Config.API_SESSION_ID, sessionId);
		request.execute();
	}

}
