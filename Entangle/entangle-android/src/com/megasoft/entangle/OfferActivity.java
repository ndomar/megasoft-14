package com.megasoft.entangle;

import com.megasoft.config.Config;
import com.megasoft.requests.GetRequest;
import com.megasoft.requests.ImageRequest;
import com.megasoft.requests.PostRequest;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
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
import android.util.Log;
import android.view.Menu;
import android.view.View;

/**
 * View an offer given the offer Id
 * 
 * @author Almgohar
 */
public class OfferActivity extends Activity {

	/**
	 * The TextView that holds the offer's description
	 */
	private TextView offerDescription;

	/**
	 * The TextView that holds the offer's expected deadline
	 */
	private TextView offerDeadline;

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
	 * The EditText of the comment
	 */
	private EditText comment;
	
	/**
	 * The button that enables the user to add his comment
	 */
	private ImageView addComment;
	/**
	 * The imageView containing the offerer's photo
	 */
	private com.megasoft.entangle.views.RoundedImageView offererAvatar;
	
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
		this.offerId = intent.getIntExtra("offerID", 1);
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
		offererAvatar = (com.megasoft.entangle.views.RoundedImageView) findViewById(R.id.offerer_avatar);
		offerDescription = (TextView) findViewById(R.id.offer_description);
		offerDeadline = (TextView) findViewById(R.id.offer_deadline);
		offererName = (TextView) findViewById(R.id.offerer_name);
		offerStatus = (TextView) findViewById(R.id.offer_status);
		offerPrice = (TextView) findViewById(R.id.offer_price);
		offerDate = (TextView) findViewById(R.id.offer_date);
		comment = (EditText) findViewById(R.id.add_comment);
		addComment = (ImageView) findViewById(R.id.add);
		
		//deleteOfferLayout = (LinearLayout) findViewById(R.id.delete_offer_layout);
		//acceptOffer = (Button) findViewById(R.id.accept_offer);

		String link = Config.API_BASE_URL + "/offer/" + offerId;

		GetRequest request = new GetRequest(link) {
			@Override
			protected void onPostExecute(String response) {
				if (this.getStatusCode() == 200) {
					try {
						JSONObject jSon = new JSONObject(response);
						tangleId = jSon.getInt("tangleId");
						JSONObject offerInformation = jSon
								.getJSONObject("offerInformation");
						viewOfferInfo(offerInformation);
					} catch (JSONException e) {
						e.printStackTrace();
					}
				} else {
					Toast toast = Toast
							.makeText(
									getApplicationContext(),
									this.getErrorMessage() + " " + this.getStatusCode(),
									Toast.LENGTH_SHORT);
					toast.show();
				}
			}
		};
		request.addHeader("X-SESSION-ID", this.sessionId);
		request.execute();
	}

	/**
	 * Retrieves the required offer information from the JSonObject Views the
	 * offer information
	 * @param JSonObject offerInformation
	 * @author Almgohar
	 */
	private void viewOfferInfo(JSONObject offerInformation) {
		try {
			viewProfilePicture(offerInformation.getString("offererAvatar"));
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
				offerStatus.setText("Pending");
			else if (status == 2)
				offerStatus.setText("Accepted");
			if (userId == loggedInId) {
				transaction = getFragmentManager().beginTransaction();
			//	DeleteButtonFragment deleteFragment = new DeleteButtonFragment();
				Bundle bundle = new Bundle();
				bundle.putString("resourceType", "offer");
				bundle.putInt("offerId", offerId);
			//	deleteFragment.setArguments(bundle);
			//	transaction.add(R.id.delete_offer_layout, deleteFragment);
				transaction.commit();
			}
			addComment.setOnClickListener(new View.OnClickListener() {	
				@Override
				public void onClick(View v) {
					if(comment.getText().toString().matches("")) {
						Toast toast = Toast
								.makeText(
										getApplicationContext(),
										"The comment cannot be empty.",
										Toast.LENGTH_SHORT);
						toast.show();
									
				} else {
					addComment(userId, comment.getText().toString());
				}
				}
			});
			
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
	 * Adds a comment to the stream of comments
	 * @param int userId
	 * @author Almgohar
	 */
	private void addComment(int userId, String comment) {
		Toast toast = Toast
				.makeText(
						getApplicationContext(),
						"Comment added.",
						Toast.LENGTH_SHORT);
		toast.show();
	}

	/**
	 * Views the user's profile picture
	 * @param String imageURL
	 * @author Almgohar
	 */ 
	public void viewProfilePicture(String imageURL) {
            ImageRequest image = new ImageRequest(offererAvatar);
            image.execute(imageURL);
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

			@Override
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
			@Override
			public void onClick(View v) {
				PostRequest r = new PostRequest(Config.API_BASE_URL + ACCEPT);
				r.setBody(returnedResponse);
				r.addHeader("x-session-id", Config.SESSION_ID);
				r.execute();
				acceptOffer.setVisibility(View.GONE);

			}
		});

	}

}
