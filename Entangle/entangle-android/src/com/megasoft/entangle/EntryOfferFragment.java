package com.megasoft.entangle;

import com.megasoft.entangle.views.RoundedImageView;

import android.content.Intent;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

public class EntryOfferFragment extends Fragment {
	/**
	 * This is the id of the offer
	 */
	private int offerId;

	/**
	 * This is the view of the fragment
	 */
	private View view;

	/**
	 * This is the offerer avatar
	 */
	private RoundedImageView offererAvatar;

	/**
	 * This is the TextView holding the name of the offerer
	 */
	private TextView offererName;

	/**
	 * This is the id of the tangle
	 */
	private int tangleId;

	/**
	 * This is the name of the tangle
	 */
	private String tangleName;

	/**
	 * This is the id of the offerer
	 */
	private int offererId;

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstanceState) {
		view = inflater
				.inflate(R.layout.template_offer_entry, container, false);
		setAttributes();
		view.setOnClickListener(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
				Intent intent = new Intent(getActivity(), OfferActivity.class);
				intent.putExtra("offerID", getOfferId());
				startActivity(intent);
			}
		});
		setOffererRedirection();
		return view;
	}

	/**
	 * This method id used to set the fields of the view
	 * 
	 * @author HebaAamer
	 */
	private void setAttributes() {
		Bundle args = getArguments();
		setOfferId(args.getInt("offerId"));
		offererAvatar = (RoundedImageView) view
				.findViewById(R.id.offererAvatar);
		((TextView) view.findViewById(R.id.offerPrice)).setText(args
				.getString("requestedPrice"));
		((TextView) view.findViewById(R.id.offerDescription)).setText(args
				.getString("description"));
		offererName = ((TextView) view.findViewById(R.id.offererName));
		offererName.setText(args.getString("offerer"));
		tangleId = args.getInt("tangleId");
		tangleName = args.getString("tangleName");
		offererId = args.getInt("userId");
	}

	/**
	 * This a getter method to get the id of the offer
	 * 
	 * @return offerID
	 * @author HebaAamer
	 */
	public int getOfferId() {
		return offerId;
	}

	/**
	 * This is a setter method to set the id of the offer
	 * 
	 * @param id
	 *            , id of the offer
	 * @author HebaAamer
	 */
	public void setOfferId(int id) {
		this.offerId = id;
	}

	/**
	 * This method is used to make the avatar and the name of the offerer
	 * redirect to the profile
	 * 
	 * @author HebaAamer
	 */
	private void setOffererRedirection() {
		offererName.setTextSize(16);
		offererName.setOnClickListener(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
				Intent intent = new Intent(getActivity().getBaseContext(),
						ProfileActivity.class);
				intent.putExtra("tangleId", getTangleId());
				intent.putExtra("tangleName", getTangleName());
				intent.putExtra("userId", getOffererId());
				startActivity(intent);
			}
		});
		offererAvatar.setOnClickListener(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
				Intent intent = new Intent(getActivity().getBaseContext(),
						ProfileActivity.class);
				intent.putExtra("tangleId", getTangleId());
				intent.putExtra("tangleName", getTangleName());
				intent.putExtra("userId", getOffererId());
				startActivity(intent);
			}
		});
	}

	/**
	 * This is a getter method to get the id of the tangle
	 * 
	 * @return tangle id
	 * @author HebaAamer
	 */
	public int getTangleId() {
		return tangleId;
	}

	/**
	 * This is a getter method to get the id of the offerer
	 * 
	 * @return offerer id
	 * @author HebaAamer
	 */
	public int getOffererId() {
		return offererId;
	}

	/**
	 * This is a getter method to get the name of the tangle
	 * 
	 * @return tangle name
	 * @author HebaAamer
	 */
	public String getTangleName() {
		return tangleName;
	}
}
